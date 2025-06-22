<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->latest()->get();
        return response()->json([
            'questions' => $questions
        ], 200);
    }

    public function store (Request $request)
    {
        $request->validate([
            'question_text' => 'required',
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        $question = Question::create([
            'question_text' => $request->question_text,
            'quiz_id' => $request->quiz_id,
        ]);
        return response()->json([
            'message' => "Question created successfully"
        ], 201);
    }

    public function show(Question $question)
    {
        return response()->json([
            'question' => $question
        ], 200);
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => 'required',
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'quiz_id' => $request->quiz_id,
        ]);

        return response()->json([
            'message' => "Question updated successfully"
        ], 200);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json([
            'message' => "Questions deleted successfully"
        ], 200);
    }
}
