<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonReview;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonReviewController extends Controller
{

    public function index(Lesson $lesson)
    {
        $reviews = $lesson->reviews()->with('user')->latest()->get();
        return response()->json([
            'reviews' => $reviews
        ], 200);
    }

    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'rating' => 'required|min:1|max:5'
        ]);

        $review = LessonReview::create([
            'comment' => $request->comment,
            'rating' => $request->rating,
            'user_id' => Auth::id(),
            'lesson_id' => $lesson->id
        ]);
        return response()->json([
            'message' => 'Review added successfully',
            'review' => $review
        ], 201);
    }
}
