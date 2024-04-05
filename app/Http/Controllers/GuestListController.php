<?php

namespace App\Http\Controllers;

use App\Models\guestList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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
}
