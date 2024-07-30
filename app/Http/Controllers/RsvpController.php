<?php

namespace App\Http\Controllers;

use App\Models\rsvp;
use App\Models\Projects;
use App\Models\guestList;
use App\Models\Invitations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class RsvpController extends Controller
{
    public function getById($id)
    {
        try {
            $invitation = Invitations::where('project_id', $id)->first();

            if(!!$invitation){
                $guests = rsvp::with('guest')
                ->whereHas('guest', function ($query) use ($invitation) {
                    $query->where('invitation_id', $invitation->id);
                })
                ->get();
                return response()->json([
                    'success' => true,
                    'data' => $guests,
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No invitations found',
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request){
        try {
            $getGuest = guestList::with('invitation')
            ->whereHas('invitation', function ($query) use ($request) {
                $query->where('invite_link', $request->invite_link);
            })
            ->where('name', $request->name)
            ->first();
            $validator = Validator::make($request->all(), [
                'attendance' => 'required',
                'greeting' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $rsvp = rsvp::create(array_merge(
                $validator->validated(),
                ['guest_id' => $getGuest->id],
            ));
            if($rsvp) {
                return response()->json([
                    'success' => true,
                    'message' => 'RSVP successfully registered',
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function guestStats($id)
    {
        try {
            $invitation = Invitations::where('project_id', $id)->first();
            $totalGreetings = rsvp::with('guest.invitation')
            ->whereHas('guest.invitation', function ($query) use ($id) {
                $query->where('project_id', $id);
            })
            ->count();
            $totalGuests = GuestList::with('invitation')
            ->whereHas('invitation', function ($query) use ($id) {
                $query->where('project_id', $id);
            })
            ->count();
            $totalAttended = rsvp::with('guest')
            ->whereHas('guest', function ($query) use ($invitation) {
                $query->where('invitation_id', $invitation->id);
            })
            ->where('attendance', 'attended')->count();
            $totalNotAttended = rsvp::with('guest')
            ->whereHas('guest', function ($query) use ($invitation) {
                $query->where('invitation_id', $invitation->id);
            })
            ->where('attendance', '!=', 'attended')->count();

            return response()->json(['data' => [
                'total_guests' => $totalGuests,
                'total_attended' => $totalAttended,
                'total_not_attended' => $totalNotAttended,
                'total_greetings' => $totalGreetings,
            ]], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
