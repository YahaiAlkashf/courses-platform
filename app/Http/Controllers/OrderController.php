<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,visa,paypal'
        ]);

        $user = $request->user();
        $cartItems = Cart::with('course')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.'], 400);
        }

        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item->course->price;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $item->course_id,
                'price' => $item->course->price
            ]);
        }

        Cart::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $order
        ]);
    }

    // View all orders for the user
    public function myOrders(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('orderItems.course')->where('user_id', $user->id)->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function myCourses(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('orderItems.course')
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $courses = [];

        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $courses[] = $item->course;
            }
        }

        return response()->json(['courses' => $courses]);
    }
    
    // View all orders for the admin
    public function allOrders()
    {
        $orders = Order::with('orderItems.course', 'user')->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function activateOrder(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order is already processed.']);
        }

        $order->status = 'completed';
        $order->save();

        return response()->json(['message' => 'Order activated successfully.']);
    }
}
