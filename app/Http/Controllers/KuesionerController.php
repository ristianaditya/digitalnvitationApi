<?php

namespace App\Http\Controllers;

use App\Models\kuesioner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class KuesionerController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quiz' => 'required|json',
                'feedback' => 'string',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = JWTAuth::parseToken()->authenticate();
            $quiz = kuesioner::create(array_merge(
                $validator->validated(),
                ['user_id' => $user->id],
            ));
            if($quiz) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quiz successfully submited',
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }
}
