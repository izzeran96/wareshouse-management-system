<?php

namespace App\Http\Controllers;

use App\Models\Subscride;
use App\Models\SubscribePackage;
use App\Models\SubscrideTransasction;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Server-to-server callback from ToyyibPay.
     *
     * ToyyibPay sends: refno, status (1=success, 2=pending, 3=fail),
     * billcode, order_id, amount, transaction_time.
     */
    public function callback(Request $request, $transaction)
    {
        $record = SubscrideTransasction::find($transaction);

        if (! $record) {
            return response('not found', 404);
        }

        $status = $request->input('status');
        $billCode = $request->input('billcode', $record->bill_code);
        $payId = $request->input('transaction_id') ?? $request->input('refno');

        $this->settle($record, $status === '1', $billCode, $payId);

        return response('OK', 200);
    }

    /**
     * Browser return URL after the user completes (or abandons) payment.
     */
    public function return(Request $request, $transaction)
    {
        $record = SubscrideTransasction::find($transaction);

        if (! $record) {
            return redirect()->route('subscribe.index');
        }

        // Verify the real status against the API rather than trusting the
        // query string alone.
        $service = new ToyyibPayService();
        $paid = $record->bill_code ? $service->isBillPaid($record->bill_code) : false;

        if (! $paid) {
            // Fall back to the status_id passed in the return URL.
            $paid = $request->input('status_id') === '1';
        }

        $this->settle($record, $paid, $record->bill_code, $request->input('order_id'));

        if ($record->fresh()->status === SubscrideTransasction::STATUS_SUCCESS) {
            session()->flash('toast', __('Subscription activated. Welcome aboard!'));
            return redirect()->route('dashboard.index');
        }

        session()->flash('toast', __('Payment was not completed. Please try again.'));
        return redirect()->route('subscribe.index');
    }

    /**
     * Mark a transaction paid/failed and (if paid) activate the subscription.
     */
    protected function settle(SubscrideTransasction $record, bool $paid, ?string $billCode = null, ?string $payId = null): void
    {
        // Idempotent: never re-process an already successful transaction.
        if ($record->status === SubscrideTransasction::STATUS_SUCCESS) {
            return;
        }

        if (! $paid) {
            $record->update([
                'status' => SubscrideTransasction::STATUS_FAILED,
                'bill_code' => $billCode ?: $record->bill_code,
                'pay_id' => $payId ?: $record->pay_id,
            ]);
            return;
        }

        $record->update([
            'status' => SubscrideTransasction::STATUS_SUCCESS,
            'bill_code' => $billCode ?: $record->bill_code,
            'pay_id' => $payId ?: $record->pay_id,
        ]);

        $this->activateSubscription($record);
    }

    /**
     * Create or extend the user's subscription based on the paid transaction.
     */
    protected function activateSubscription(SubscrideTransasction $record): void
    {
        $package = $record->subscribe_package_id
            ? SubscribePackage::find($record->subscribe_package_id)
            : null;

        $durationDays = $package?->duration_days ?? 30;

        // Extend from the current expiry if the user still has an active plan.
        $existing = Subscride::query()
            ->where('user_id', $record->user_id)
            ->active()
            ->latest('expired_date')
            ->first();

        $start = $existing && $existing->expired_date && $existing->expired_date->isFuture()
            ? $existing->expired_date->copy()
            : Carbon::now();

        Subscride::create([
            'user_id' => $record->user_id,
            'subscribe_package_id' => $record->subscribe_package_id,
            'period' => $package?->title,
            'started_at' => Carbon::now(),
            'expired_date' => $start->copy()->addDays($durationDays),
            'price' => $record->amount,
            'status' => Subscride::STATUS_ACTIVE,
        ]);
    }
}
