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

    public function getAll()
    {
        $data = kuesioner::with('user')->get();
        return response()->json(['data' => $data], 200);
    }
    public function feedbackExport(Request $request)
    {
        $quiz = kuesioner::with('user')->get();

        $result = $quiz->map(function ($quiz) {
            $data = json_decode($quiz->quiz);
            $param = [
                '1'=> 'Sangat Tidak Setuju',
                '2'=> 'Tidak Setuju',
                '3'=> 'Ragu-ragu',
                '4'=> 'Setuju',
                '5'=> 'Sangat Setuju'
            ];
            return [
                'id' => $quiz->id,
                'email' => $quiz->user->email,
                'question1' => $param[$data->satu],
                'question2' => $param[$data->dua],
                'question3' => $param[$data->tiga],
                'question4' => $param[$data->empat],
                'question5' => $param[$data->lima],
                'feedback' => $quiz->feedback,
            ];
        });

        return response()->json(['data' => $result], 200);
    }
}
