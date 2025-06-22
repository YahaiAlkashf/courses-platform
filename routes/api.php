<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group( function () {
   Route::get('/user',[AuthController::class,'user']);
   Route::get('/logout',[AuthController::class,'logout']);
});
Route::get('/users',[AuthController::class,'users']);
Route::delete('/users/{id}',[AuthController::class,'destroy']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/resend-code', [AuthController::class, 'resendVerificationCode']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user-profile', function (Request $request) {
        return response()->json($request->user());
    });
});

Route::get('/categories',[CategoriesController::class,'index']);
Route::post('/categories',[CategoriesController::class,'create']);
Route::get('/categories/{id}',[CategoriesController::class,'show']);
Route::post('/categories/{id}',[CategoriesController::class,'update']);
Route::delete('/categories/{id}',[CategoriesController::class,'destroy']);

Route::get('/courses',[CoursesController::class,'index']);
Route::post('/courses',[CoursesController::class,'create']);
Route::get('/courses/{course}',[CoursesController::class,'show']);
Route::post('/courses/{id}',[CoursesController::class,'update']);
Route::delete('/courses/{id}',[CoursesController::class,'destroy']);


Route::get('/lessons',[LessonController::class,'index']);
Route::post('/lessons',[LessonController::class,'create']);
Route::get('/lessons/{id}',[LessonController::class,'show']);
Route::post('/lessons/{id}',[LessonController::class,'update']);
Route::delete('/lessons/{id}',[LessonController::class,'destroy']);
Route::get('/lessons/{lesson}/reviews', [LessonReviewController::class, 'index']);
Route::post('/lessons/{lesson}/reviews', [LessonReviewController::class, 'store'])->middleware('auth:sanctum');

Route::get('/courses/{course}/quizzes', [QuizController::class, 'index']);
Route::post('/quizzes',[QuizController::class,'store']);
Route::get('/quizzes/{quiz}',[QuizController::class,'show']);
Route::post('/quizzes/{quiz}',[QuizController::class,'update']);
Route::delete('/quizzes/{quiz}',[QuizController::class,'destroy']);

Route::get('/quizzes/{quiz}/questions', [QuestionController::class, 'index']);
Route::post('/questions', [QuestionController::class, 'store']);
Route::get('/questions/{question}', [QuestionController::class, 'show']);
Route::post('/questions/{question}',[QuestionController::class,'update']);
Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);


Route::get('/questions/{question}/answers', [AnswerController::class, 'index']);
Route::post('/answers', [AnswerController::class, 'store']);
Route::get('/answers/{answer}', [AnswerController::class, 'show']);
Route::post('/answers/{answer}', [AnswerController::class, 'update']);
Route::delete('/answers/{answer}', [AnswerController::class, 'destroy']);


   // Cart Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'viewCart']);
    Route::post('/cart/remove', [CartController::class, 'removeFromCart']);

    // Order Routes
    Route::post('/order/checkout', [OrderController::class, 'checkout']);
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->get('/admin/orders', [OrderController::class, 'allOrders']);
Route::middleware(['auth:sanctum', 'isAdmin'])->post('/admin/orders/{order}/activate', [OrderController::class, 'activateOrder']);

Route::post('/payment', [PaymentController::class, 'processPayment'])->middleware('auth:sanctum');
Route::post('/create-checkout-session', [PaymentController::class, 'createCheckoutSession'])->middleware('auth:sanctum');
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

