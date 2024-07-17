<?php

namespace App\Http\Controllers;

use App\Models\rsvp;
use App\Models\Projects;
use App\Models\guestList;
use App\Models\Invitations;
use Illuminate\Http\Request;
use App\Imports\GuestListImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class GuestListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getById($project_id)
    {
        try {
            $guests = GuestList::with('invitation')
            ->whereHas('invitation', function ($query) use ($project_id) {
                $query->where('project_id', $project_id);
            })
            ->get();

            return response()->json(['data' => $guests], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function attendance(Request $request)
    {
        try {
            $guests = GuestList::with('invitation')
                ->where('name', $request->name)
                ->withWhereHas('invitation', function ($query) use ($request) {
                    $query->where('invite_link', $request->invite_link);
                })
                ->update(['attendance' => 'attended']);

            if($guests) {
                return response()->json([
                    'success' => true,
                    'message' => 'Guest successfully updated',
                ], 200);
            }
            return response()->json(['success' => false, 'message' => 'Guest not found'], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getByIdGuest($id)
    {
        try {
            $guests = GuestList::find($id);
            return response()->json(['data' => $guests], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'invitation_id' => 'required',
                'name' => 'required|string|between:3,100|unique:guest_lists',
                'no_phone' => 'required|string|unique:guest_lists',
                'attendance' => 'in:attended,absent,unconfirmed',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $guest = guestList::create(array_merge(
                $validator->validated()
            ));
            if($guest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Guest successfully created',
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'invitation_id' => 'required',
                'name' => 'required|string|between:3,100',
                'no_phone' => 'required',
                'attendance' => 'in:attended,absent,unconfirmed',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $guest = guestList::find($id);
            $guest->update($validator->validated());
            if($guest) {
                return response()->json([
                    'success' => true,
                    'message' => 'Guest successfully updated',
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function destroy($id)
    {
        $musics = guestList::find($id);
        if (!$musics) {
            return response()->json(['message' => 'Guest not found'], 404);
        }
        $musics->delete();

        return response()->json([
            'message' => 'Guest deleted successfully'
        ]);
    }

    public function guestStats($id)
    {
        try {
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
            $totalAttended = GuestList::with('invitation')
                ->whereHas('invitation', function ($query) use ($id) {
                $query->where('project_id', $id);
            })
            ->where('attendance', 'attended')->count();
            $totalNotAttended = GuestList::with('invitation')
                ->whereHas('invitation', function ($query) use ($id) {
                $query->where('project_id', $id);
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

    public function import(Request $request)
    {
        $invite = Invitations::where('project_id', $request->project_id)->first();
        try {
            if ($request->hasFile('excel')) {
                $file = $request->file('excel');
                Excel::import(new GuestListImport($invite->id), $file);
                return response()->json(['message' => 'File imported successfully'], 200);
            }
            return response()->json(['message' => 'No file uploaded'], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

}
