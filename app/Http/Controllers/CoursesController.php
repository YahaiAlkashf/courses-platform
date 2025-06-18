<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
        public function index()
    {
        $courses = Course::all();
        return response()->json([
            'courses' => $courses
        ], 200);
    }

    public function create(Request $request)
    {
            $request->validate([
                'name' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'category_id' => 'required|exists:categories,id',
                'price'=>'required'
            ]);

        $imagePath = $request->file('image')->store('Courses', 'public');
        $course = Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'price'=>$request->price
        ]);
        return response()->json([
            'message' => "successfully"
        ], 200);
    }

    public function show($id)
    {
        $course = Course::where('id', $id)->first();
        return response()->json([
            'course' => $course
        ], 200);
    }
    public function update(Request $request,$id)
    {
            $request->validate([
                'name' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif',
                'category_id' => 'required|exists:categories,id',
                'price'=>'required'
            ]);

        $course = Course::where('id', $id)->first();
        if ($request->image) {
            $imagePath = $request->file('image')->store('Courses', 'public');
            $course->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'image' => $imagePath,
                'price'=>$request->price
            ]);
        }else{
            $course->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'price'=>$request->price
            ]);
        }

        return response()->json([
            'message' => "seccessfully"
        ], 200);
    }

    public function destroy($id) {
        $course = Course::where('id', $id)->first();
        $course->delete();
        return response()->json([
            'message' => "seccessfully"
        ], 200);
    }
}
