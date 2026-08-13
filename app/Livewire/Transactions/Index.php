<?php

namespace App\Livewire\Transactions;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
  use WithPagination;

  public string $search = '';

  public string $typeFilter = 'all';

  public string $monthFilter = 'all';

  public function updatingSearch(): void
  {
    $this->resetPage();
  }

  public function updatingTypeFilter(): void
  {
    $this->resetPage();
  }

  public function updatingMonthFilter(): void
  {
    $this->resetPage();
  }

  public function render(): View
  {
    $userId = Auth::id();

    $transactions = Transaction::query()
      ->with([
        'account:id,name',
        'targetAccount:id,name',
        'category:id,name',
        'item:id,name',
        'targetItem:id,name',
      ])
      ->when($userId, fn($query) => $query->forUser($userId))
      ->when($this->typeFilter !== 'all', fn($query) => $query->where('type', $this->typeFilter))
      ->when($this->monthFilter !== 'all', fn($query) => $query->where('month', (int) $this->monthFilter))
      ->when($this->search !== '', function ($query) {
        $search = '%' . str_replace('%', '\\%', $this->search) . '%';

        $query->where(function ($query) use ($search) {
          $query->where('description', 'like', $search)
            ->orWhereHas('account', fn($accountQuery) => $accountQuery->where('name', 'like', $search))
            ->orWhereHas('targetAccount', fn($accountQuery) => $accountQuery->where('name', 'like', $search))
            ->orWhereHas('category', fn($categoryQuery) => $categoryQuery->where('name', 'like', $search))
            ->orWhereHas('item', fn($itemQuery) => $itemQuery->where('name', 'like', $search))
            ->orWhereHas('targetItem', fn($itemQuery) => $itemQuery->where('name', 'like', $search));
        });
      })
      ->latest('transaction_date')
      ->latest('id')
      ->paginate(12);

    $monthOptions = [
      'all' => 'Semua Bulan',
      1 => 'Januari',
      2 => 'Februari',
      3 => 'Maret',
      4 => 'April',
      5 => 'Mei',
      6 => 'Juni',
      7 => 'Juli',
      8 => 'Agustus',
      9 => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Desember',
    ];

    return view('livewire.transactions.index', [
      'transactions' => $transactions,
      'monthOptions' => $monthOptions,
      'typeOptions' => [
        'all' => 'Semua Tipe',
        TransactionType::Income->value => TransactionType::Income->label(),
        TransactionType::Expense->value => TransactionType::Expense->label(),
        TransactionType::Transfer->value => TransactionType::Transfer->label(),
      ],
    ]);
  }
}
