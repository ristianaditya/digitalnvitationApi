<?php

namespace App\Http\Controllers;

use App\Models\Invitations;
use App\Models\rsvp;
use App\Models\Projects;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
