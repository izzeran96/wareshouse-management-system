<?php

namespace App\Http\Livewire\Subscribe\Pages;

use App\Models\SubscribePackage;
use App\Models\SubscrideTransasction;
use App\Services\ToyyibPayService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscribeIndexPage extends Component
{
    public $packages;
    public $activeSubscribe;

    public function mount()
    {
        $this->packages = SubscribePackage::active()->orderBy('price')->get();
        $this->activeSubscribe = Auth::user()->activeSubscribe;
    }

    /**
     * Start the payment flow for the chosen package: create a pending
     * transaction, generate a ToyyibPay bill and redirect to the payment page.
     */
    public function subscribe($packageId)
    {
        $package = SubscribePackage::active()->find($packageId);

        if (! $package) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('Package not found'),
            ]);
            return;
        }

        $service = new ToyyibPayService();

        if (! $service->isConfigured()) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('Payment gateway is not configured yet. Please contact the administrator.'),
            ]);
            return;
        }

        $user = Auth::user();

        $transaction = SubscrideTransasction::create([
            'user_id' => $user->id,
            'subscribe_package_id' => $package->id,
            'status' => SubscrideTransasction::STATUS_PENDING,
            'transaction_description' => 'Subscribe: ' . $package->title,
            'amount' => $package->price,
        ]);

        $billCode = $service->createBill(
            (float) $package->price,
            $package->title,
            'Subscription by ' . $user->email,
            route('subscribe.return', ['transaction' => $transaction->id]),
            route('subscribe.callback', ['transaction' => $transaction->id]),
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number ?? '0000000000',
            ],
            'WMS-' . $transaction->id
        );

        if (! $billCode) {
            $transaction->update(['status' => SubscrideTransasction::STATUS_FAILED]);
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('Could not create the payment bill. Please try again.'),
            ]);
            return;
        }

        $transaction->update(['bill_code' => $billCode]);

        return redirect()->away($service->billUrl($billCode));
    }

    public function render()
    {
        return view('livewire.subscribe.pages.subscribe-index-page');
    }
}
