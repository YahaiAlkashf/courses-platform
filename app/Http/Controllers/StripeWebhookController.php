<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\User;
use App\Models\Course;

class StripeWebhookController extends Controller
{
public function handleWebhook(Request $request)
{
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        return response('Invalid signature', 400);
    }

    if ($event->type == 'checkout.session.completed') {
        $session = $event->data->object;

        $customer_email = $session->customer_details->email;


        $user = User::where('email', $customer_email)->first();

        if ($user) {

            $order_id = $session->metadata->order_id;


            $order = Order::where('id', $order_id)->where('user_id', $user->id)->first();

            if ($order) {

                $order->status = 'completed';
                $order->payment_method = 'visa';
                $order->save();


                foreach ($order->orderItems as $item) {
                    
                    $user->courses()->attach($item->course_id);
                }
            }
        }
    }

    return response('Webhook handled', 200);
}

}
