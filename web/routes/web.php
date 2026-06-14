<?php

use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\ImageProxyController;
use App\Http\Controllers\Frontend\RootController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [RootController::class, 'index'])->middleware(['installed'])->name('home');
Route::get('/image-proxy', ImageProxyController::class)->middleware(['installed'])->name('image-proxy');
Route::prefix('payment')->name('payment.')->middleware(['installed'])->group(function () {
    Route::get('/{paymentGateway:slug}/pay/{order}', [PaymentController::class, 'index'])->name('index');
    Route::post('/{order}/pay', [PaymentController::class, 'payment'])->name('store');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/success', [PaymentController::class, 'success'])->name('success');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/fail', [PaymentController::class, 'fail'])->name('fail');
    Route::match(['get', 'post'], '/{paymentGateway:slug}/{order}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/successful/{order}', [PaymentController::class, 'successful'])->name('successful');
});

Route::get('/{any}', [RootController::class, 'index'])->middleware(['installed'])->where(['any' => '.*']);
