<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRegisterRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

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
            [
                'password' => bcrypt($request->password),
                'verified' => false,
            ],
        ));
        // Generate a random OTP
        $otp = random_int(100000, 999999);
        $email = $request->email;
        // Calculate expiry time
        $expiresAt = Carbon::now()->addMinutes(5);
        // Store or update the OTP in the database
        Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );
        Mail::to($request->email)->send(new OtpMail($otp,$email));
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

    public function registerGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|between:2,100',
            'email'     => 'required|email',
            'username'  => 'required',
            'googleId'  => 'required|string',
            'picture'   => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)
                    ->where('google_id', $request->googleId)
                    ->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $this->generateUniqueUsername($request->username),
                'google_id' => $request->googleId,
                'picture' => $request->picture,
                'password' => bcrypt(Str::random(40)),
                'verified' => true,
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'google_id' => $request->googleId,
                'picture' => $request->picture,
            ]);
        }

        try {
            $token = auth()->guard('api')->login($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role ?? 'user',
            'no_phone' => $user->no_phone,
            'gender' => $user->gender,
            'picture' => $user->picture,
        ];

        $payload = JWTAuth::setToken($token)->getPayload();
        $expirationTime = $payload->get('exp');

        return response()->json([
            'success' => true,
            'data' => $userData,
            'expires_in' => $expirationTime,
            'access_token' => $token,
        ], 200);
    }

    protected function generateUniqueUsername($baseUsername)
    {
        $username = Str::slug($baseUsername);
        $originalUsername = $username;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        return $username;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'string|between:2,100',
            'email'     => 'required|email|unique:users',
            'username'     => 'required|unique:users',
            'no_phone' => 'unique:users',
            'gender' => 'in:male,female',
            'role' => 'in:admin,user',
            'password'  => 'required|min:8',
            'address'  => 'string|min:8',
        ]);
        $fileUrl = '';

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFileName = $file->getClientOriginalName();

            // Mendapatkan tanggal dan waktu saat ini
            $currentDateTime = now()->format('YmdHis');

            // Membuat nama baru untuk file
            $newFileName = $currentDateTime . '_' . $originalFileName;

            $file->move(public_path('avatars'), $newFileName);
            $fileUrl = asset('avatars/' . $newFileName);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['picture' => $fileUrl],
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

    public function updateAdmin(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'string|between:2,100',
            'email'     => 'email|unique:users,email,'.$user->id,
            'username'     => 'string|between:2,100|unique:users,username,'.$user->id,
            'no_phone' => 'between:6,15|unique:users,no_phone,'.$user->id,
            'gender' => 'in:male,female',
            'role' => 'in:admin,user',
            'address' => 'string|between:2,200',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFileName = $file->getClientOriginalName();

            // Mendapatkan tanggal dan waktu saat ini
            $currentDateTime = now()->format('YmdHis');

            // Membuat nama baru untuk file
            $newFileName = $currentDateTime . '_' . $originalFileName;

            $file->move(public_path('avatars'), $newFileName);
            $fileUrl = asset('avatars/' . $newFileName);

            if($request->password){
                $user->update(
                    array_merge(
                        $validator->validated(),
                        ['picture' => $fileUrl],
                        ['password' => bcrypt($request->password)],
                    ));
            }else{
                $user->update(
                array_merge(
                    $validator->validated(),
                    ['picture' => $fileUrl]
                ));
            }
        }

        if($request->password){
            $user->update(
                array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)],
                ));
        }else{
            $user->update($validator->validated());
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 201);
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
        if (!$user->verified) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email before logging in.'
            ], 403); // 403 Forbidden
        }
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

                $file->move(public_path('avatars'), $newFileName);
                $fileUrl = asset('avatars/' . $newFileName);

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

    public function destroy($id)
    {
        $users = User::find($id);
        if (!$users) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $users->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
