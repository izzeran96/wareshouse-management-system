<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Auth;
use App\Models\Bill;
class ToyyibPayment extends Component
{
    public function render()
    {
        return view('livewire.toyyib-payment');
    }

    public function createBill(){
        $user = Auth::user();
        $options = array(
            'userSecretKey' => env('TOYYIBPAY_SECRET_KEY'),
            'categoryCode' => env('TOYYIB_CODE'),
            'billName' => Str::limit(Auth::user()->name,26),
            'billDescription' => 'Subscribe By'.$user->email. \Carbon\Carbon::now(),
            'billPriceSetting'=>1,
            'billPayorInfo' => 1,
            'billAmount' => $amount,
            'billReturnUrl' => route('check.Transactions.pptem'),
            'billCallbackUrl' => route('check.Transactions.pptem'),
            'billExternalReferenceNo' => 'TPPM',
            'billTo' =>  Str::limit(Auth::user()->name,26),
            'billEmail' => $user->email,
            'billPhone' => $user->phone_number,
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => 2,
            'billContentEmail' => 'Thank you for purchasing our product!',
            'billChargeToCustomer' => 1,
        );

    }

    public function getBill(){
        $where = Bill::where('status','failed')->get();
    }

    public function checkBill(){

    }
}
