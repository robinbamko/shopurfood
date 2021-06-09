<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $cart_array;
    public $logo;
    public $footer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cart_array,$logo,$footer)
    {
         $this->cart_array = $cart_array;
         $this->logo = $logo;
         $this->footer = $footer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cart is reserved!')->view('email.abandoned_cart_mail');
    }
}
