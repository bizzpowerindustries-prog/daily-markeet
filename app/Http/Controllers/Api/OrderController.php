<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cash_on_delivery,gateway,wallet',
            'gateway' => 'required_if:payment_method,gateway|string',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $cartItems = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 422);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->sale_price * $item->quantity;
        });

        $shippingFee = 0; // Calculate based on seller/address
        $tax = $subtotal * 0.05; // 5% tax
        $discount = 0;

        // Apply coupon logic
        if ($request->coupon_code) {
            $discount = $this->applyCoupon($request->coupon_code, $subtotal);
        }

        $total = $subtotal + $shippingFee + $tax - $discount;

        // Check wallet payment
        if ($request->payment_method === 'wallet') {
            $wallet = Wallet::where('owner_type', 'App\Models\User')
                ->where('owner_id', $user->id)
                ->first();

            if (!$wallet || $wallet->balance < $total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance'
                ], 422);
            }

            // Deduct from wallet
            $wallet->balance -= $total;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $total,
                'balance_after' => $wallet->balance,
                'source' => 'order',
                'description' => 'Payment for order #' . Str::random(8),
                'status' => 'completed'
            ]);
        }

        // Create order
        $orderNumber = 'DM-' . strtoupper(uniqid());
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'shipping_address_id' => $request->shipping_address_id,
            'payment_method' => $request->payment_method,
            'payment_gateway' => $request->gateway ?? null,
            'coupon_code' => $request->coupon_code,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'paid'
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'seller_id' => $cartItem->product->seller_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->sale_price,
                'total' => $cartItem->product->sale_price * $cartItem->quantity
            ]);

            // Update product stock
            $product = $cartItem->product;
            $product->stock -= $cartItem->quantity;
            $product->sales_count += $cartItem->quantity;
            $product->save();
        }

        // Clear cart
        Cart::where('user_id', $user->id)->delete();

        // Create tracking event
        $order->trackingEvents()->create([
            'status' => 'pending',
            'description' => 'Order placed successfully',
            'location' => 'System'
        ]);

        // Generate tracking number
        $order->generateTrackingNumber();

        // Send notifications
        $this->sendOrderNotifications($order);

        // Process gateway payment if needed
        if ($request->payment_method === 'gateway') {
            $paymentData = [
                'amount' => $total,
                'order_id' => $order->id,
                'item_name' => 'Order #' . $orderNumber,
                'customer_email' => $user->email,
                'return_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
                'notify_url' => route('payment.webhook')
            ];

            try {
                $paymentResponse = $this->paymentService->processPayment($paymentData);
                
                // Update order with transaction info
                $order->payment_gateway = $this->paymentService->getActiveGateway()['name'];
                $order->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed. Please complete payment.',
                    'data' => [
                        'order' => $order,
                        'payment' => $paymentResponse
                    ]
                ]);

            } catch (\Exception $e) {
                // Rollback order if payment fails
                $order->update(['status' => 'cancelled']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => $order->load('items', 'trackingEvents')
        ]);
    }

    public function getOrders(Request $request)
    {
        $orders = Order::with(['items.product', 'trackingEvents'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::with([
            'items.product',
            'trackingEvents',
            'shippingAddress',
            'return'
        ])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    public function trackOrder($trackingNumber)
    {
        $order = Order::with('trackingEvents')
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking number not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tracking_number' => $order->tracking_number,
                'current_status' => $order->status,
                'courier' => $order->courier ?? 'Pakistan Post',
                'expected_delivery' => $order->expected_delivery_date,
                'events' => $order->trackingEvents->map(function ($event) {
                    return [
                        'status' => $event->status,
                        'description' => $event->description,
                        'location' => $event->location,
                        'event_time' => $event->event_time
                    ];
                })
            ]
        ]);
    }

    public function requestReturn(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::find($orderId);

        if (!$order || $order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        if (!$order->canBeReturned()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be returned'
            ], 422);
        }

        // Create return request
        $return = $order->return()->create([
            'user_id' => $request->user()->id,
            'reason' => $request->reason,
            'status' => 'pending',
            'return_amount' => 0, // Calculate based on items
        ]);

        // Add return items
        foreach ($request->items as $item) {
            $orderItem = OrderItem::find($item['order_item_id']);
            $return->items()->create([
                'order_item_id' => $item['order_item_id'],
                'quantity' => $item['quantity'],
                'price' => $orderItem->price,
                'total' => $orderItem->price * $item['quantity']
            ]);

            // Update return amount
            $return->return_amount += $orderItem->price * $item['quantity'];
        }

        $return->save();

        // Handle images
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('returns', 'public');
                $return->images()->create(['path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Return request submitted successfully',
            'data' => $return->load('items', 'images')
        ]);
    }

    private function applyCoupon($code, $subtotal)
    {
        $coupon = Coupon::where('code', $code)
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (!$coupon) {
            return 0;
        }

        if ($coupon->usage_count >= $coupon->usage_limit) {
            return 0;
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                $discount = $coupon->max_discount;
            }
        } else {
            $discount = $coupon->discount_value;
        }

        // Increment usage
        $coupon->increment('usage_count');

        return $discount;
    }

    private function sendOrderNotifications($order)
    {
        // Queue email notification
        dispatch(new \App\Jobs\SendOrderConfirmationEmail($order));
        
        // Queue push notification
        dispatch(new \App\Jobs\SendPushNotification(
            $order->user_id,
            'Order Confirmed',
            'Your order #' . $order->order_number . ' has been confirmed.'
        ));

        // Create in-app notification
        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'title' => 'Order Confirmed',
            'message' => 'Your order #' . $order->order_number . ' has been confirmed.',
            'type' => 'order',
            'read' => false
        ]);
    }
}
