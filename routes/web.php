<?php

use App\Livewire\Dashboard\Index;
use App\Livewire\Auth\Register;
use App\Livewire\SinkingFunds\Index as SinkingFundsIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;
use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;

// Guest Route (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
  Route::get('/login', Login::class)->name('login');
  Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
  Route::redirect('/', '/dashboard');

  Route::get('/dashboard', Index::class)->name('dashboard');
  Route::get('/sinking-funds', SinkingFundsIndex::class)->name('sinking-funds.index');
  Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');

  // Route Logout Sederhana
  Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('login');
  })->name('logout');
});
