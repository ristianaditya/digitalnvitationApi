<?php

namespace App\Http\Controllers;

use App\Models\Invitations;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class InvitationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $invite = Invitations::all();
        return response()->json(['data' => $invite], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $validator = Validator::make($request->all(), [
                'invite_link' => 'required|unique:invitations',
                'music_id' => 'required',
                'project_id' => 'required|string',
                'invite_date' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $invite = Invitations::updateOrCreate(
                ["project_id" => $request->project_id],
                [
                    'invite_link' => $request->invite_link,
                    "user_id" => $user->id,
                    "music_id" => $request->music_id,
                    "invite_date" => $request->invite_date,
                ]
            );
            if($invite) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invitation successfully created',
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function getByProject($id){
        try {
            $invitations = Invitations::with('music')
            ->where('project_id', $id)
            ->get();

            if ($invitations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invitations found for the given project ID',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => $invitations,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
