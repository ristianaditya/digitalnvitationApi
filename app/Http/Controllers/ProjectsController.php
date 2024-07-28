<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PhpParser\Node\Stmt\TryCatch;

class ProjectsController extends Controller
{
    public function getById($id)
    {
        $project = Projects::find($id);
        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }
        return response()->json(['project' => $project], 200);
    }
    public function storeOrUpdate(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'editor' => 'required',
            'html'      => 'required',
            'css'     => 'required',
            'project_name'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $project = Projects::updateOrCreate(
            ['id' => $request->id],
            ['user_id' => $user->id, 'editor' => $request->editor, 'html' => $request->html, 'css' => $request->css, 'canvas' => $request->canvas, 'project_name' => $request->project_name]
        );

        return response()->json(['message' => 'Project created/updated successfully', 'project' => $project], 200);
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'html'      => 'required',
                'css'     => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $project = Projects::find($id);
            $project->update($validator->validated());
            return response()->json([
                'success' => true,
                'data' => 'project updated successfully.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 409);
        }
    }
    public function getByUserId()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $projects = Projects::with(['invitation'])->where('user_id', $user->id)->get();

        return response()->json(['projects' => $projects], 200);
    }
    public function destroy($id)
    {
        $projects = Projects::find($id);
        if (!$projects) {
            return response()->json(['message' => 'Projects not found'], 404);
        }
        $projects->delete();

        return response()->json([
            'message' => 'Projects deleted successfully'
        ]);
    }
}
