<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $quizzes = $course->quizzes()->latest()->get();
        return response()->json([
            'quizzes' => $quizzes
        ], 200);
    }

    public function store(Request $request)
    {
            $request->validate([
                'title' => 'required',
                'course_id' => 'required|exists:courses,id',
            ]);
        $quiz = Quiz::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
        ]);
        return response()->json([
            'message' => " Quiz created successfully"
        ], 201);
    }

    public function show(Quiz $quiz)
    {
        return response()->json([
            'quiz' => $quiz
        ], 200);
    }
    public function update(Request $request,Quiz $quiz)
    {
            $request->validate([
                'title' => 'required',
                'course_id' => 'required|exists:courses,id',
            ]);

            $quiz->update([
                'title' => $request->title,
                'course_id' => $request->course_id,
            ]);

        return response()->json([
            'message' => "Quiz updated successfully"
        ], 200);
    }

    public function destroy(Quiz $quiz) {
        $quiz->delete();
        return response()->json([
            'message' => " Quiz deleted successfully"
        ], 200);
    }
}
