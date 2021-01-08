<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;

class Add_invoice_new extends Notification
{
    use Queueable;

    private $invoice;
    private $user_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice,$user_name)
    {
        $this->invoice = $invoice;
        $this->user_name = $user_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        //this array name is data
        return [
            //'data' => $this->details['body']
            'id' => $this->invoice->id,
            'title' => 'تم إضافة فاتورة جديدة بواسطة : ',
            'user' => $this->user_name
        ];
    }


}
