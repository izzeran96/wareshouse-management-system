<?php

namespace App\Http\Livewire\Subscribe;

use App\Models\PaymentGateway;
use Livewire\Component;

class SubscribePayment extends Component
{
    public function render()
    {
        return view('livewire.subscribe.subscribe-payment');
    }

    public function PaymentGateway(){
        $paymentGateway = PaymentGateway::all();
        $package;
    }
}
