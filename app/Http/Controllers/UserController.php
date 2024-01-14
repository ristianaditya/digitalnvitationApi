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

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|between:2,100',
            'email'     => 'required|email|unique:users',
            'username'     => 'required|unique:users',
            'no_phone' => 'required|unique:users',
            'gender' => 'required|in:male,female',
            'role' => 'required|in:admin,user',
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
            'subscribe' => $user->subscribe,
        ];

        return response()->json([
            'success' => true,
            'data'    => $user,
            'access_token'   => $token
        ], 200);
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
                return response()->json([
                    'success' => true,
                    'message' => 'Token successfully refresh',
                    'access_token' => $newToken,
                ]);
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
            'name'      => 'required|string|between:2,100',
            'email'     => 'required|email',
            'username'     => 'required',
            'no_phone' => 'required',
            'gender' => 'required|in:male,female',
            'role' => 'required|in:admin,user',
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
}
