<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index($course_id)
    {
        $lessons = Lesson::where("course_id", $course_id)->get();
        return response()->json($lessons, 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required|exists:courses,id',
            'video' => 'required|mimes:mp4,mov,avi,wmv',
        ]);

        $path = Storage::disk('s3')->put('videos', $request->file('video'));
        $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));

        $lesson = Lesson::create([
            'title' => $request->title,
            'video_path' => $url,
            'course_id' => $request->course_id,
        ]);

        return response()->json([
            'message' => 'Video uploaded successfully',
            'url' => $url,
        ], 201);
    }


    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json($lesson, 200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required|exists:courses,id',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv',
        ]);

        $lesson = Lesson::findOrFail($id);

        $url = $lesson->video_path;

        if ($request->hasFile('video')) {
            $path = Storage::disk('s3')->put('videos', $request->file('video'));
            $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(60));
        }

        $lesson->update([
            'title' => $request->title,
            'video_path' => $url,
            'course_id' => $request->course_id,
        ]);
        return response()->json([
            'message' => 'Lesson updated successfully'
        ], 200);
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        return response()->json([
            'message' => "Lesson deleted successfully"
        ], 200);
    }
}
