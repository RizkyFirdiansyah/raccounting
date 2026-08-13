<?php

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Login - Cashflow Tracker')]
#[Layout('layouts.guest')] // Menggunakan layout khusus guest/auth tanpa navbar internal
class Login extends Component
{
  #[Rule(['required', 'email'])]
  public string $email = '';

  #[Rule(['required'])]
  public string $password = '';

  public bool $remember = false;

  public function login(): void
  {
    $this->validate();

    if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
      session()->regenerate();

      $this->redirectIntended(default: route('dashboard'), navigate: true);

      return;
    }

    $this->addError('email', 'Email atau password tidak sesuai dengan catatan kami.');
  }

  public function render(): View
  {
    return view('livewire.auth.login');
  }
}
