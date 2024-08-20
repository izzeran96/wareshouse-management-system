<?php

namespace App\Http\Livewire\Subscribe;

use App\Models\Subscride;
use Livewire\Component;

class Subscribe extends Component
{
    public function render()
    {
        return view('livewire.subscribe.subscribe');
    }

    public function index(){
        $subscride = Subscride::all();
    }

    public function subscribePost(){
        $paySubscribe = SubscridePackage::all();
        return view('');
    }
}
