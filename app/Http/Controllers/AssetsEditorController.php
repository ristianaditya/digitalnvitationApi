<?php

namespace App\Http\Controllers;

use App\Models\assetsEditor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Storage\StorageClient;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AssetsEditorController extends Controller
{
    public function getAll()
    {
        $assets = assetsEditor::all();
        return response()->json(['assets' => $assets], 200);
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_name' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

               // Upload file ke Google Cloud Storage
                $filePath = 'assets/' . $newFileName;
                Storage::disk('gcs')->put($filePath, file_get_contents($file));

                // Membuat URL untuk file yang diupload
                $storage = new StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => storage_path('app/google-cloud-key.json'),
                ]);
                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object($filePath);

                // URL publik
                $fileUrl = $object->signedUrl(new \DateTime('tomorrow'));
                $user = JWTAuth::parseToken()->authenticate();

                assetsEditor::create(
                    array_merge(
                        $validator->validated(),
                        ['file_path' => $fileUrl],
                        ['user_id' => $user->id],
                    ));

                return response()->json(['message' => 'File successfully created']);
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
                'file_name' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFileName = $file->getClientOriginalName();

                // Mendapatkan tanggal dan waktu saat ini
                $currentDateTime = now()->format('YmdHis');

                // Membuat nama baru untuk file
                $newFileName = $currentDateTime . '_' . $originalFileName;

                // Upload file ke Google Cloud Storage
                $filePath = 'assets/' . $newFileName;
                Storage::disk('gcs')->put($filePath, file_get_contents($file));

                // Membuat URL untuk file yang diupload
                $storage = new StorageClient([
                    'projectId' => env('GOOGLE_CLOUD_PROJECT_ID'),
                    'keyFilePath' => storage_path('app/google-cloud-key.json'),
                ]);
                $bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
                $object = $bucket->object($filePath);

                // URL publik
                $fileUrl = $object->signedUrl(new \DateTime('tomorrow'));
                $user = JWTAuth::parseToken()->authenticate();

                $file = assetsEditor::find($id);
                $file->update(
                    array_merge(
                        $validator->validated(),
                        ['file_path' => $fileUrl],
                        ['user_id' => $user->id],
                    ));

                return response()->json(['message' => 'File successfully updated']);
            }else{
                $file = assetsEditor::find($id);
                $file->update($validator->validated());
                return response()->json(['message' => 'File successfully updated']);
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
        $file = assetsEditor::find($id);
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }
        return response()->json(['assets' => $file], 200);
    }

    public function destroy($id)
    {
        $file = assetsEditor::find($id);
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully'
        ]);
    }
}
