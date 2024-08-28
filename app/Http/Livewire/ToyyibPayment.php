<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ToyyibPayment extends Component
{
    public function render()
    {
        return view('livewire.toyyib-payment');
    }

    public function createBill(){

    }

    public function getBill(){
        $where = Bill::where('status','failed')->get();
    }

    public function checkBill(){

    }
}
