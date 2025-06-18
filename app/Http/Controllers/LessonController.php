<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index($course_id)
    {
        $lessons = Lesson::where("course_id", $course_id)->get();
        return response()->json($lessons, 200);
    }

    public function create(Request $request)
    {
        try {
            // Log the incoming request data
            \Log::info('Lesson creation request:', [
                'has_file' => $request->hasFile('lesson'),
                'all_data' => $request->all(),
                'files' => $request->allFiles()
            ]);

            $request->validate([
                'title' => 'required',
                'course_id' => 'required|exists:courses,id',
                'lesson' => 'required|mimes:mp4,mov,avi,wmv', 
            ]);

            if (!$request->hasFile('lesson')) {
                return response()->json([
                    'message' => 'No video file provided',
                    'debug_info' => [
                        'has_file' => $request->hasFile('lesson'),
                        'files' => $request->allFiles()
                    ]
                ], 400);
            }

            $file = $request->file('lesson');
            
            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Invalid file uploaded',
                    'debug_info' => [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'error' => $file->getError()
                    ]
                ], 400);
            }

            \Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            $uploadResult = Cloudinary::uploadVideo($file->getRealPath(), [
                'resource_type' => 'video',
                'folder' => 'course_lessons'
            ]);

            if (empty($uploadResult)) {
                throw new \Exception("Cloudinary returned an empty response");
            }

            $videoUrl = $uploadResult->getSecurePath();
            $publicId = $uploadResult->getPublicId();

            if (!$videoUrl || !$publicId) {
                throw new \Exception("Cloudinary upload failed: Invalid response structure");
            }

            $lesson = Lesson::create([
                'title' => $request->title,
                'video_path' => $videoUrl,
                'public_id' => $publicId,
                'course_id' => $request->course_id,
            ]);

            return response()->json([
                'message' => 'Lesson created successfully',
                'lesson' => $lesson
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Lesson creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'An error occurred while uploading the video.',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'has_file' => $request->hasFile('lesson'),
                    'files' => $request->allFiles(),
                    'request_data' => $request->all()
                ]
            ], 500);
        }
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
            'lesson' => 'nullable|mimes:mp4,mov,avi,wmv',
        ]);

        $lesson = Lesson::findOrFail($id);

        $video_path = $lesson->video_path;

        if ($request->hasFile('lesson')) {
            $video_path = Cloudinary::uploadVideo($request->file('lesson')->getRealPath())->getSecurePath();
        }

        $lesson->update([
            'title' => $request->title,
            'video_path' => $video_path,
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
