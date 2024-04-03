<?php

namespace App\Http\Controllers;

use App\Models\imageUploaders;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ImageUploadersController extends Controller
{
    public function createImage(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

                // Simpan file ke direktori tertentu
                $file->move(public_path('images'), $newFileName);

                $fileUrl = asset('images/' . $newFileName);

                imageUploaders::created(['user_id' => $user->id ,'image' => $fileUrl]);

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
}
