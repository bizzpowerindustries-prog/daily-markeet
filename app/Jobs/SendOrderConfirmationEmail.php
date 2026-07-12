<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        try {
            $user = $this->order->user;
            
            Mail::send('emails.order_confirmation', [
                'order' => $this->order,
                'user' => $user
            ], function ($message) use ($user) {
                $message->from(config('mail.from.address'), config('app.name'))
                        ->to($user->email)
                        ->subject('Order Confirmation #' . $this->order->order_number);
            });

            // Log email
            EmailLog::create([
                'type' => 'order_confirmation',
                'recipient' => $user->email,
                'subject' => 'Order Confirmation #' . $this->order->order_number,
                'status' => 'sent',
                'sent_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            
            // Log error
            EmailLog::create([
                'type' => 'order_confirmation',
                'recipient' => $this->order->user->email,
                'subject' => 'Order Confirmation #' . $this->order->order_number,
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
}
