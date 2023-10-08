<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::get('/deposit', [TransactionController::class, 'showDeposits']);
    Route::post('/deposit', [TransactionController::class, 'deposit'])->name('deposits.store');
    Route::get('/withdrawal', [TransactionController::class, 'showWithdrawals']);
    Route::post('/withdrawal', [TransactionController::class, 'withdraw'])->name('withdrawals.store');
});

if (auth()->guest()) {
    // User is not authenticated
}
