<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group( function () {
   Route::get('/user',[AuthController::class,'user']);
   Route::get('/logout',[AuthController::class,'logout']);
});
Route::post('/login',[AuthController::class,'login']);
Route::get('/users',[AuthController::class,'users']);
Route::post('/register',[AuthController::class,'register']);
Route::delete('/users/{id}',[AuthController::class,'destroy']);


Route::get('/categories',[CategoriesController::class,'index']);
Route::post('/categories',[CategoriesController::class,'create']);
Route::get('/categories/{id}',[CategoriesController::class,'show']);
Route::post('/categories/{id}',[CategoriesController::class,'update']);
Route::delete('/categories/{id}',[CategoriesController::class,'destroy']);

Route::get('/courses',[CoursesController::class,'index']);
Route::post('/courses',[CoursesController::class,'create']);
Route::get('/courses/{id}',[CoursesController::class,'show']);
Route::post('/courses/{id}',[CoursesController::class,'update']);
Route::delete('/courses/{id}',[CoursesController::class,'destroy']);


Route::get('/lessons',[LessonController::class,'index']);
Route::post('/lessons',[LessonController::class,'create']);
Route::get('/lessons/{id}',[LessonController::class,'show']);
Route::post('/lessons/{id}',[LessonController::class,'update']);
Route::delete('/lessons/{id}',[LessonController::class,'destroy']);
