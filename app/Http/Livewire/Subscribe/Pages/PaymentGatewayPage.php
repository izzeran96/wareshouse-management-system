<?php

namespace App\Http\Livewire\Subscribe\Pages;

use App\Models\PaymentGateway;
use Livewire\Component;

class PaymentGatewayPage extends Component
{
    public string|null $secret_key = null;
    public string|null $category_code = null;
    public bool $is_sandbox = true;
    public bool $is_active = true;

    public $gateway;

    protected $rules = [
        'secret_key' => 'nullable|max:191',
        'category_code' => 'nullable|max:191',
        'is_sandbox' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function mount()
    {
        $this->gateway = PaymentGateway::current();

        if ($this->gateway) {
            $this->secret_key = $this->gateway->secret_key;
            $this->category_code = $this->gateway->category_code;
            $this->is_sandbox = $this->gateway->is_sandbox;
            $this->is_active = $this->gateway->is_active;
        }
    }

    public function submit()
    {
        $this->validate();

        $data = [
            'secret_key' => $this->secret_key,
            'category_code' => $this->category_code,
            'is_sandbox' => $this->is_sandbox,
            'is_active' => $this->is_active,
        ];

        if ($this->gateway) {
            $this->gateway->update($data);
        } else {
            $this->gateway = PaymentGateway::create($data);
        }

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => __('Payment gateway settings saved'),
        ]);
    }

    public function render()
    {
        return view('livewire.subscribe.pages.payment-gateway-page');
    }
}
