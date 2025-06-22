<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{

public function createCheckoutSession(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
    ]);

    $user = $request->user();
    $course = Course::find($request->course_id);


    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => $course->price,
        'payment_method' => 'visa',
        'status' => 'pending'
    ]);


    OrderItem::create([
        'order_id' => $order->id,
        'course_id' => $course->id,
        'price' => $course->price
    ]);


    Stripe::setApiKey(env('STRIPE_SECRET'));

    try {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $course->name,
                    ],
                    'unit_amount' => $course->price * 100, 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://your-website.com/success',
            'cancel_url' => 'https://your-website.com/cancel',
            'metadata' => [
                'order_id' => $order->id
            ]
        ]);

        return response()->json([
            'id' => $session->id,
            'url' => $session->url,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

}
