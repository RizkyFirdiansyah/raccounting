<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Daftar Akun Baru - Cashflow Tracker')]
#[Layout('layouts.guest')] // Menggunakan layout khusus guest/auth tanpa navbar internal
class Register extends Component
{
  #[Rule(['required', 'string', 'max:255'])]
  public string $name = '';

  #[Rule(['required', 'string', 'email', 'max:255', 'unique:users,email'])]
  public string $email = '';

  public string $password = '';

  public string $password_confirmation = '';

  protected function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
      'password' => ['required', 'string', Password::defaults(), 'confirmed'],
    ];
  }

  public function register(): void
  {
    $this->validate();

    $user = User::create([
      'name' => $this->name,
      'email' => $this->email,
      'password' => Hash::make($this->password),
    ]);

    Auth::login($user);

    session()->regenerate();

    $this->redirect(route('dashboard'), navigate: true);
  }

  public function render(): View
  {
    return view('livewire.auth.register');
  }
}
