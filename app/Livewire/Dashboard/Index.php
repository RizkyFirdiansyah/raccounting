<?php

namespace App\Livewire\Dashboard;

use App\Enums\ItemPriority;
use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\FinancialCalculatorService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Index extends Component
{
  public int $month;

  public int $year;

  public string $transactionTypeFilter = 'all';

  public string $itemStatusFilter = 'all';

  public string $itemPriorityFilter = 'all';

  public string $itemSort = 'target_desc';

  public function mount(): void
  {
    $this->month = (int) now()->format('n');
    $this->year = (int) now()->format('Y');
  }

  public function updatedMonth(): void
  {
    // Triggers re-render automatically
  }

  public function updatedYear(): void
  {
    // Triggers re-render automatically
  }

  /**
   * Build 6-month trending data (current month + 5 months back).
   *
   * @return array{labels: string[], income: float[], expense: float[]}
   */
  protected function buildTrendData(): array
  {
    $labels = [];
    $incomes = [];
    $expenses = [];

    $service = app(FinancialCalculatorService::class);
    $userId = Auth::id();

    for ($i = 5; $i >= 0; $i--) {
      $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonths($i);
      $labels[] = $date->isoFormat('MMM YY');
      $summary = $service->getMonthlySummary((int) $date->format('n'), (int) $date->format('Y'), $userId);
      $incomes[] = $summary['total_income'];
      $expenses[] = $summary['total_expense'];
    }

    return compact('labels', 'incomes', 'expenses');
  }

  /**
   * Build the most recent transactions for the dashboard log.
   *
   * @return Collection<int, Transaction>
   */
  protected function buildRecentTransactions(): Collection
  {
    $userId = Auth::id();

    return Transaction::query()
      ->with([
        'account:id,name',
        'targetAccount:id,name',
        'category:id,name',
        'item:id,name',
        'targetItem:id,name',
      ])
      ->when($userId, fn($query) => $query->forUser($userId))
      ->forMonthYear($this->month, $this->year)
      ->when($this->transactionTypeFilter !== 'all', fn($query) => $query->where('type', $this->transactionTypeFilter))
      ->latest('transaction_date')
      ->latest('id')
      ->limit(8)
      ->get();
  }

  /**
   * Build the filtered sinking fund item list.
   *
   * @return Collection<int, Item>
   */
  protected function buildSinkingFundItems(): Collection
  {
    $userId = Auth::id();

    $items = Item::query()
      ->with('category:id,name,color')
      ->when($userId !== null, function ($query) use ($userId): void {
        $query->where(function ($subQuery) use ($userId): void {
          $subQuery->where('user_id', $userId)->orWhereNull('user_id');
        });
      })
      ->when($this->itemStatusFilter !== 'all', fn($query) => $query->where('status', $this->itemStatusFilter))
      ->when($this->itemPriorityFilter !== 'all', fn($query) => $query->where('priority', $this->itemPriorityFilter))
      ->get();

    $priorityOrder = [
      ItemPriority::Wajib->value => 0,
      ItemPriority::Emergency->value => 1,
      ItemPriority::RutinBulanan->value => 2,
      ItemPriority::KeinginanShortterm->value => 3,
    ];

    $sortedItems = match ($this->itemSort) {
      'current_desc' => $items->sort(function (Item $left, Item $right): int {
        return (float) $right->current_amount <=> (float) $left->current_amount;
      }),
      'remaining_desc' => $items->sort(function (Item $left, Item $right): int {
        $leftRemaining = max(0, (float) $left->target_amount - (float) $left->current_amount);
        $rightRemaining = max(0, (float) $right->target_amount - (float) $right->current_amount);

        return $rightRemaining <=> $leftRemaining;
      }),
      'priority' => $items->sort(function (Item $left, Item $right) use ($priorityOrder): int {
        $leftPriority = $priorityOrder[$left->priority->value] ?? 99;
        $rightPriority = $priorityOrder[$right->priority->value] ?? 99;

        if ($leftPriority !== $rightPriority) {
          return $leftPriority <=> $rightPriority;
        }

        return (float) $right->target_amount <=> (float) $left->target_amount;
      }),
      default => $items->sort(function (Item $left, Item $right): int {
        return (float) $right->target_amount <=> (float) $left->target_amount;
      }),
    };

    return $sortedItems->take(6)->values();
  }

  public function render(): View
  {
    $service = app(FinancialCalculatorService::class);
    $userId = Auth::id();

    $summary = $service->getMonthlySummary($this->month, $this->year, $userId);

    $accounts = Account::query()
      ->when($userId, fn($query) => $query->where('user_id', $userId)->orWhereNull('user_id'))
      ->where('is_active', true)
      ->get()
      ->map(fn(Account $account) => [
        'id' => $account->id,
        'name' => $account->name,
        'type' => $account->type,
        'balance' => $service->getRealAccountBalance($account),
      ]);

    $totalIncome = $summary['total_income'];
    $allocation = [
      'keseharian' => round($totalIncome * 0.60, 2),
      'sinkingfund' => round($totalIncome * 0.25, 2),
      'buffer' => round($totalIncome * 0.15, 2),
    ];

    $trendData = $this->buildTrendData();
    $recentTransactions = $this->buildRecentTransactions();
    $sinkingFundItems = $this->buildSinkingFundItems();

    $monthNames = [
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

    $transactionTypeOptions = [
      'all' => 'Semua',
      TransactionType::Income->value => TransactionType::Income->label(),
      TransactionType::Expense->value => TransactionType::Expense->label(),
      TransactionType::Transfer->value => TransactionType::Transfer->label(),
    ];

    $itemStatusOptions = [
      'all' => 'Semua Status',
      ItemStatus::Belum->value => ItemStatus::Belum->label(),
      ItemStatus::Proses->value => ItemStatus::Proses->label(),
      ItemStatus::Terpenuhi->value => ItemStatus::Terpenuhi->label(),
    ];

    $itemPriorityOptions = [
      'all' => 'Semua Prioritas',
      ItemPriority::Wajib->value => ItemPriority::Wajib->label(),
      ItemPriority::Emergency->value => ItemPriority::Emergency->label(),
      ItemPriority::RutinBulanan->value => ItemPriority::RutinBulanan->label(),
      ItemPriority::KeinginanShortterm->value => ItemPriority::KeinginanShortterm->label(),
    ];

    $itemSortOptions = [
      'target_desc' => 'Target Terbesar',
      'current_desc' => 'Saldo Terbesar',
      'remaining_desc' => 'Sisa Terbesar',
      'priority' => 'Prioritas',
    ];

    $years = range(now()->year - 2, now()->year + 1);

    return view('livewire.dashboard.index', compact(
      'summary',
      'accounts',
      'allocation',
      'trendData',
      'monthNames',
      'years',
      'recentTransactions',
      'sinkingFundItems',
      'transactionTypeOptions',
      'itemStatusOptions',
      'itemPriorityOptions',
      'itemSortOptions'
    ));
  }
}
