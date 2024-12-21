<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\familydata;
use App\Models\Invitations;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
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
                'invite_link' => [
                    'required',
                    Rule::unique('invitations')->ignore($request->project_id, 'project_id'),
                ],
                'invite_name' => 'string|between:2,100',
                'music_id' => 'required',
                'project_id' => 'required|string',
                'invite_date' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $invite_date = Carbon::parse($request->invite_date);
            $invite_date->addHours(7);
            $invite = Invitations::updateOrCreate(
                ["project_id" => $request->project_id],
                [
                    'invite_link' => $request->invite_link,
                    'invite_name' => $request->invite_name,
                    "user_id" => $user->id,
                    "music_id" => $request->music_id,
                    "status" => ($request->status == true)? "publish": "unpublish",
                    "invite_date" => $invite_date,
                    "type" => $request->type,
                ]
            );

            $familyData = familydata::updateOrCreate(
                ['invitation_id' => $invite->id],
                [
                    'personFirst' => $request->personFirst,
                    'genderFirst' => $request->genderFirst,
                    'personSecond' => $request->personSecond,
                    'genderSecond' => $request->genderSecond,
                ]
            );

            if($invite) {
                return response()->json([
                    'success' => true,
                    'message' => $invite,
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }
    public function editClipbord(Request $request){
        try {
            $invite = Invitations::where('project_id', $request->project_id)
            ->update(['clipboard' => $request->clipboard]);
            if($invite) {
                return response()->json([
                    'success' => true,
                    'message' => 'clipboard successfully updated',
                ], 200);
            }
            return response()->json(['success' => false, 'message' => 'invitations not found'], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function getByProject($id){
        try {
            $invitations = Invitations::with(['music', 'familydata'])
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
    public function getInvitation($link){
        try {
            $invitations = Invitations::with(['project','familydata', 'music'])
            ->where('invite_link', $link)
            ->get();

            if ($invitations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invitations found',
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
    public function destroy($id)
    {
        $invitations = Invitations::find($id);
        if (!$invitations) {
            return response()->json(['message' => 'Invitations not found'], 404);
        }
        $invitations->delete();

        return response()->json([
            'message' => 'Invitations deleted successfully'
        ]);
    }
    public function getTransaction($link)
    {
        try {
            $invitations = Invitations::where('invite_link', $link)
            ->first();
            $userWithTransaction = User::with(['latestTransaction'])->find($invitations->user_id);
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
}
