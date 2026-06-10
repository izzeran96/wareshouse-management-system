<?php

namespace App\Http\Livewire\Worker\Pages;

use App\Events\GoodsTransactionCreated;
use App\Models\Goods;
use App\Models\GoodsTransaction;
use App\Models\GoodsTransactionCategory;
use App\Models\GoodsTransactionGoods;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * A focused, mobile-friendly scanning screen for warehouse workers.
 *
 * Scan a barcode -> review the goods -> choose Receive (+) or Dispatch (-)
 * with a quantity -> submit. Each submission creates a one-line transaction
 * and updates stock via the existing GoodsTransactionCreated event.
 */
class WorkerScanPage extends Component
{
    public $code = '';
    public $goods = null;       // matched Goods (array snapshot for the view)
    public $goodsId = null;
    public $quantity = 1;
    public $action = 'receiving'; // receiving | dispatching

    /** @var array<int, array{name:string, code:string, action:string, quantity:int}> */
    public array $recentScans = [];

    protected $rules = [
        'goodsId' => 'required',
        'quantity' => 'required|numeric|min:1',
        'action' => 'required|in:receiving,dispatching',
    ];

    /**
     * Called by the barcode scanner component (browser event).
     */
    public function handleScan($scanned)
    {
        $scanned = is_string($scanned) ? trim($scanned) : '';

        if ($scanned === '') {
            return;
        }

        $this->code = $scanned;
        $this->lookup();
    }

    public function lookup()
    {
        $goods = Goods::with('unit')->where('barcode', trim($this->code))
            ->orWhere('code', trim($this->code))
            ->first();

        if (! $goods) {
            $this->reset(['goods', 'goodsId']);
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('No goods found for code') . ': ' . $this->code,
            ]);
            return;
        }

        $this->goodsId = $goods->id;
        $this->goods = [
            'name' => $goods->name,
            'code' => $goods->code,
            'barcode' => $goods->barcode,
            'stock' => $goods->stock,
            'unit' => optional($goods->unit)->symbol,
        ];
        $this->quantity = 1;
    }

    public function submit()
    {
        $this->validate();

        $goods = Goods::find($this->goodsId);

        if (! $goods) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('Goods not found'),
            ]);
            return;
        }

        if ($this->action === 'dispatching' && $this->quantity > $goods->stock) {
            $this->addError('quantity', __('Quantity cannot be more than current stock.'));
            return;
        }

        $category = $this->action === 'dispatching'
            ? GoodsTransactionCategory::dispatching()->first()
            : GoodsTransactionCategory::receiving()->first();

        if (! $category) {
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => __('Transaction category is not configured. Please contact the administrator.'),
            ]);
            return;
        }

        $transaction = GoodsTransaction::create([
            'category_id' => $category->id,
            'transaction_at' => time(),
            'description' => __('Worker scan') . ' - ' . Auth::user()->name,
            'created_by' => Auth::id(),
        ]);

        GoodsTransactionGoods::create([
            'transaction_id' => $transaction->id,
            'goods_id' => $goods->id,
            'quantity' => $this->quantity,
        ]);

        event(new GoodsTransactionCreated($transaction));

        array_unshift($this->recentScans, [
            'name' => $goods->name,
            'code' => $goods->code,
            'action' => $this->action,
            'quantity' => (int) $this->quantity,
        ]);
        $this->recentScans = array_slice($this->recentScans, 0, 8);

        $this->dispatchBrowserEvent('toast', [
            'type' => 'success',
            'message' => ($this->action === 'dispatching' ? __('Dispatched') : __('Received'))
                . ' ' . $this->quantity . ' x ' . $goods->name,
        ]);

        $this->reset(['code', 'goods', 'goodsId', 'quantity']);
        $this->quantity = 1;
    }

    public function incrementQty()
    {
        $this->quantity = (int) $this->quantity + 1;
    }

    public function decrementQty()
    {
        $this->quantity = max(1, (int) $this->quantity - 1);
    }

    public function clearGoods()
    {
        $this->reset(['code', 'goods', 'goodsId']);
        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.worker.pages.worker-scan-page');
    }
}
