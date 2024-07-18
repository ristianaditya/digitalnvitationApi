<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'string|between:2,100',
            'email'     => 'required|email|unique:users',
            'username'     => 'required|unique:users',
            'no_phone' => 'unique:users',
            'gender' => 'in:male,female',
            'role' => 'in:admin,user',
            'password'  => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
        ));

        if($user) {
            return response()->json([
                'success' => true,
                'message' => 'User successfully registered',
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'email or password is incorrect'
            ], 401);
        }

        $user = auth()->guard('api')->user();
        $user = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'no_phone' => $user->no_phone,
            'gender' => $user->gender,
        ];

        $payload = JWTAuth::setToken($token)->getPayload();
        $expirationTime = $payload->get('exp');

        return response()->json([
            'success' => true,
            'data'    => $user,
            'expires_in' => $expirationTime,
            'access_token'   => $token,
        ], 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
    }

    public function logout()
    {
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());
            if($removeToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'User successfully signed out',
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function getSession()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $userWithTransaction = User::with(['latestTransaction'])->find($user->id);

            return response()->json([
                'success' => true,
                'data' => $userWithTransaction
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 401);
        }
    }

    public function user(Request $request){
        try {
            return $request->user();
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 401);
        }
    }

    public function refreshToken(){
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            if($newToken) {
                $payload = JWTAuth::setToken($newToken)->getPayload();
                $expirationTime = $payload->get('exp');
                return response()->json([
                    'success' => true,
                    'message' => 'Token successfully refresh',
                    'access_token' => $newToken,
                    'expires_in' => $expirationTime,
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 401);
        }
    }

    public function getUser(){
        return response()->json([
            'success' => true,
            'data' => User::all()
        ]);
    }

    public function showUser(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'string|between:2,100',
            'email'     => 'email|unique:users,email,'.$user->id,
            'username'     => 'string|between:2,100|unique:users,username,'.$user->id,
            'no_phone' => 'between:6,15|unique:users,no_phone,'.$user->id,
            'gender' => 'in:male,female',
            'address'      => 'string|between:2,200',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function uploadPhoto(Request $request, User $user)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

                // Upload file ke Google Cloud Storage
                $filePath = 'avatars/' . $newFileName;
                Storage::disk('gcs')->put($filePath, file_get_contents($file));

                // Membuat URL untuk file yang diupload
                $storage = new StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => storage_path('app/google-cloud-key.json'),
                ]);
                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object($filePath);

                // URL publik
                $fileUrl = "https://storage.googleapis.com/digiapicloud/{$filePath}";

                $user->update(['picture' => $fileUrl]);

                return response()->json(['message' => 'File uploaded successfully']);
            }
            return response()->json(['message' => 'No file uploaded'], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password'  => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = JWTAuth::parseToken()->authenticate();
            if(Hash::check($request->old_password, $user->password, [])) {
                $user->update(['password' => bcrypt($request->password)]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'password failed to update',
                ], 422);
            }
            return response()->json([
                'success' => true,
                'data' => 'password successfully updated'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 401);
        }

    }
}
