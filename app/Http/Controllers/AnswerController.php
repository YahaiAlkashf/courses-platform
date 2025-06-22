<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
        public function index(Question $question)
    {
        $answers = $question->answers()->get();
        return response()->json([
            'answers' => $answers
        ], 200);
    }

    public function store (Request $request)
    {
        $request->validate([
            'answer_text' => 'required',
            'is_correct' => 'required',
            'question_id' => 'required|exists:questions,id',
        ]);

        $answer = Answer::create([
            'answer_text' => $request->answer_text,
            'is_correct' => $request->is_correct,
            'question_id' => $request->question_id,
        ]);
        return response()->json([
            'message' => "Answer created successfully"
        ], 201);
    }

    public function show(Answer $answer)
    {
        return response()->json([
            'answer' => $answer
        ], 200);
    }

    public function update(Request $request, Answer $answer)
    {
        $request->validate([
            'answer_text' => 'required',
            'is_correct' => 'required|boolean',
            'question_id' => 'required|exists:questions,id',
        ]);

        $answer->update([
            'answer_text' => $request->answer_text,
            'is_correct' => $request->is_correct,
            'question_id' => $request->question_id,
        ]);

        return response()->json([
            'message' => "Answer updated successfully"
        ], 200);
    }

    public function destroy(Answer $answer)
    {
        $answer->delete();
        return response()->json([
            'message' => "Answer deleted successfully"
        ], 200);
    }
}
