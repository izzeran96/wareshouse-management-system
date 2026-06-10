<?php

namespace App\Http\Livewire\Subscribe\Pages;

use App\Models\SubscribePackage;
use Livewire\Component;

class AddPackagePage extends Component
{
    public string $title = '';
    public string|null $description = null;
    public $price = 0;
    public $duration_days = 30;
    public $is_active = true;

    protected $rules = [
        'title' => 'required|max:120',
        'description' => 'nullable|max:1000',
        'price' => 'required|numeric|min:0',
        'duration_days' => 'required|integer|min:1',
        'is_active' => 'boolean',
    ];

    public function submit()
    {
        $this->validate();

        SubscribePackage::create([
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'is_active' => $this->is_active,
        ]);

        return redirect()->to(route('subscribe-package.index'));
    }

    public function render()
    {
        return view('livewire.subscribe.pages.add-package-page');
    }
}
