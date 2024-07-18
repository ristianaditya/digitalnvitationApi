<?php

namespace App\Http\Controllers;

use App\Models\Musics;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;

class MusicsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $musics = Musics::all();
        return response()->json(['musics' => $musics], 200);
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'music_name' => 'required',
                'singer' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            if ($request->hasFile('music')) {
                $file = $request->file('music');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

               // Upload file ke Google Cloud Storage
                $filePath = 'musics/' . $newFileName;
                Storage::disk('gcs')->put($filePath, file_get_contents($file));

                // Membuat URL untuk file yang diupload
                $storage = new StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => storage_path('app/google-cloud-key.json'),
                ]);
                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object($filePath);

                // URL publik
                $fileUrl = "https://storage.googleapis.com/digiapicloud/{$filePath}";

                Musics::create(
                    array_merge(
                        $validator->validated(),
                        ['music' => $fileUrl]
                    ));

                return response()->json(['message' => 'Music successfully created']);
            }
            return response()->json(['message' => 'No file uploaded'], 400);
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
                'music_name' => 'required',
                'singer' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            if ($request->hasFile('music')) {
                $file = $request->file('music');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

                // Upload file ke Google Cloud Storage
                $filePath = 'musics/' . $newFileName;
                Storage::disk('gcs')->put($filePath, file_get_contents($file));

                // Membuat URL untuk file yang diupload
                $storage = new StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => storage_path('app/google-cloud-key.json'),
                ]);
                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object($filePath);

                // URL publik
                $fileUrl = "https://storage.googleapis.com/digiapicloud/{$filePath}";

                $musics = Musics::find($id);
                $musics->update(
                    array_merge(
                        $validator->validated(),
                        ['music' => $fileUrl]
                    ));

                return response()->json(['message' => 'Music successfully updated']);
            }else{
                $musics = Musics::find($id);
                $musics->update($validator->validated());
                return response()->json(['message' => 'Music successfully updated']);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }

    public function getById($id)
    {
        $musics = Musics::find($id);
        if (!$musics) {
            return response()->json(['message' => 'Music not found'], 404);
        }
        return response()->json(['musics' => $musics], 200);
    }
    public function destroy($id)
    {
        $musics = Musics::find($id);
        if (!$musics) {
            return response()->json(['message' => 'Music not found'], 404);
        }
        $musics->delete();

        return response()->json([
            'message' => 'Music deleted successfully'
        ]);
    }
}
