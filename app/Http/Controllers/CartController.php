<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Add course to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = $request->user();
        $cartItem = Cart::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $request->course_id],
        );
        return response()->json([
            'message' => 'Course added to cart successfully.',
            'cart_item' => $cartItem
        ]);
    }

    // View cart
    public function viewCart(Request $request)
    {
        $user = $request->user();

        $cartItems = Cart::with('course')->where('user_id', $user->id)->get();

        return response()->json([
            'cart' => $cartItems
        ]);
    }

    // Remove item from cart
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = $request->user();

        Cart::where('user_id', $user->id)
            ->where('course_id', $request->course_id)
            ->delete();

        return response()->json(['message' => 'Course removed from cart successfully.']);
    }
}

