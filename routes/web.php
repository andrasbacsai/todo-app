<?php

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\EnsureInstanceAdmin;
use App\Http\Middleware\EnsurePaymentRoutesRegistered;
use App\Http\Middleware\EnsureSubscription;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Billing;
use App\Livewire\Dashboard;
use App\Livewire\Dump;
use App\Livewire\InstanceSettings as LivewireInstanceSettings;
use App\Livewire\Todo;
use App\Models\Purchase;
use App\Settings\InstanceSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Cashier\Cashier;

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::group(['middleware' => 'auth', 'prefix' => 'i'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/', [DashboardController::class, 'store'])->name('dashboard.store');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/t/{id}', Todo::class)->name('todo');

    Route::get('/dump', Dump::class)->name('dump')->middleware(EnsureSubscription::class);

    Route::get('/instance-settings', LivewireInstanceSettings::class)->name('instance-settings')->middleware(EnsureInstanceAdmin::class);

    Route::get('/billing', Billing::class)->name('billing');
});

// Subscription routes
Route::middleware(['auth', EnsurePaymentRoutesRegistered::class])->group(function () {
    Route::get('/subscription/checkout', function (Request $request) {
        $priceId = $request->session()->get('price_id');
        $request->session()->forget('price_id');

        $instanceSettings = app(InstanceSettings::class);
        config(['cashier.key' => $instanceSettings->stripe_key]);
        config(['cashier.secret' => $instanceSettings->stripe_secret]);
        config(['cashier.webhook.secret' => $instanceSettings->stripe_webhook_secret]);

        return $request->user()
            ->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing'),
                'cancel_url' => route('billing'),
            ]);
    })->name('subscription-checkout');
    Route::get('/subscription/billing-portal', function (Request $request) {

        $instanceSettings = app(InstanceSettings::class);
        config(['cashier.key' => $instanceSettings->stripe_key]);
        config(['cashier.secret' => $instanceSettings->stripe_secret]);
        config(['cashier.webhook.secret' => $instanceSettings->stripe_webhook_secret]);

        return $request->user()->redirectToBillingPortal(route('billing'));
    })->name('subscription-billing-portal');

    // Route::view('/subscription/checkout/success', 'checkout.success')->name('subscription-checkout-success');
    // Route::view('/subscription/checkout/cancel', 'checkout.cancel')->name('subscription-checkout-cancel');
});

// One-time payments
Route::middleware(['auth', EnsurePaymentRoutesRegistered::class])->group(function () {
    Route::get('/one-time-payment/checkout', function (Request $request) {
        $priceId = $request->session()->get('price_id');
        if (blank($priceId)) {
            return redirect()->route('billing');
        }
        $request->session()->forget('price_id');

        $instanceSettings = app(InstanceSettings::class);
        config(['cashier.key' => $instanceSettings->stripe_key]);
        config(['cashier.secret' => $instanceSettings->stripe_secret]);
        config(['cashier.webhook.secret' => $instanceSettings->stripe_webhook_secret]);

        return $request->user()->checkout([$priceId => 1], [
            'success_url' => route('one-time-payment-checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing'),

        ]);
    })->name('one-time-payment-checkout');

    Route::get('/one-time-payment/checkout/success', function (Request $request) {
        $sessionId = $request->get('session_id');
        if ($sessionId === null) {
            return;
        }

        $instanceSettings = app(InstanceSettings::class);
        config(['cashier.key' => $instanceSettings->stripe_key]);
        config(['cashier.secret' => $instanceSettings->stripe_secret]);
        config(['cashier.webhook.secret' => $instanceSettings->stripe_webhook_secret]);

        $session = Cashier::stripe()->checkout->sessions->retrieve(
            $sessionId,
            ['expand' => ['line_items']]
        );

        if ($session->payment_status !== 'paid') {
            return;
        }

        if (! $session->line_items || empty($session->line_items->data)) {
            return;
        }

        $priceId = $session->line_items->data[0]->price->id;
        $paymentIntent = $session->payment_intent;

        $purchaseExists = Purchase::where('stripe_id', $session->id)->exists();
        if ($purchaseExists) {
            return redirect()->route('billing');
        }

        Purchase::create([
            'user_id' => $request->user()->id,
            'stripe_id' => $session->id,
            'stripe_payment_intent' => $paymentIntent,
            'stripe_price' => $priceId,
            'stripe_status' => 'paid',
            'quantity' => 1,
        ]);

        return redirect()->route('billing');
    })->name('one-time-payment-checkout-success');
    // Route::get('/one-time-payment/checkout/cancel', function (Request $request) {
    //     $sessionId = $request->get('session_id');
    //     if ($sessionId === null) {
    //         return;
    //     }

    //     $instanceSettings = app(InstanceSettings::class);
    //     config(['cashier.key' => $instanceSettings->stripe_key]);
    //     config(['cashier.secret' => $instanceSettings->stripe_secret]);
    //     config(['cashier.webhook.secret' => $instanceSettings->stripe_webhook_secret]);

    //     $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

    //     return redirect()->route('billing');
    // })->name('one-time-payment-checkout-cancel');
});
