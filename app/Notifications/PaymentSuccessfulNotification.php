<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessfulNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly Invoice $invoice,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('payment.notification_subject'))
            ->greeting(__('payment.notification_greeting', ['name' => $notifiable->name]))
            ->line(__('payment.notification_line', [
                'plan' => $this->order->plan?->name,
                'credits' => number_format((int) $this->order->plan?->credits),
            ]))
            ->line(__('payment.notification_invoice', ['invoice' => $this->invoice->invoice_number]))
            ->action(__('payment.view_invoices'), route('payment.invoices'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('payment.notification_subject'),
            'message' => __('payment.notification_line', [
                'plan' => $this->order->plan?->name,
                'credits' => number_format((int) $this->order->plan?->credits),
            ]),
            'order_id' => $this->order->order_number,
            'invoice_number' => $this->invoice->invoice_number,
        ];
    }
}
