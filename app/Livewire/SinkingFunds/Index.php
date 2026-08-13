<?php

namespace App\Livewire\SinkingFunds;

use App\Enums\ItemPriority;
use App\Enums\ItemStatus;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\FinancialCalculatorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'detail';

    public string $search = '';

    public string $statusFilter = 'all';

    // Item Modal State & Form
    public bool $showItemModal = false;

    public ?int $editingItemId = null;

    public string $item_name = '';

    public ?int $category_id = null;

    public ?int $account_id = null;

    public string $target_amount = '';

    public string $priority = 'rutin_bulanan';

    public ?string $target_date = null;

    public ?string $note = null;

    // Category Modal State & Form
    public bool $showCategoryModal = false;

    public ?int $editingCategoryId = null;

    public string $category_name = '';

    public ?string $category_description = null;

    public string $category_color = '#3B82F6';

    public string $category_icon = 'heroicon-o-folder';

    // Delete Item State
    public bool $showDeleteConfirm = false;

    public ?int $deletingItemId = null;

    public bool $showItemDetailModal = false;

    public ?int $selectedItemId = null;

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openItemDetail(int $itemId): void
    {
        $this->selectedItemId = $itemId;
        $this->showItemDetailModal = true;
    }

    public function closeItemDetail(): void
    {
        $this->showItemDetailModal = false;
        $this->selectedItemId = null;
    }

    public function openItemModal(?int $itemId = null, ?int $defaultCategoryId = null): void
    {
        $this->resetValidation();
        $this->editingItemId = $itemId;

        if ($itemId) {
            $item = Item::findOrFail($itemId);
            $this->item_name = $item->name;
            $this->category_id = $item->category_id;
            $this->account_id = $item->account_id;
            $this->target_amount = number_format((float) $item->target_amount, 0, ',', '.');
            $this->priority = $item->priority->value;
            $this->target_date = $item->target_date ? $item->target_date->format('Y-m-d') : null;
            $this->note = $item->note;
        } else {
            $this->resetItemForm();
            if ($defaultCategoryId) {
                $this->category_id = $defaultCategoryId;
            }
        }

        $this->showItemModal = true;
    }

    public function closeItemModal(): void
    {
        $this->showItemModal = false;
        $this->resetItemForm();
    }

    public function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->item_name = '';
        $this->category_id = null;
        $this->account_id = null;
        $this->target_amount = '';
        $this->priority = ItemPriority::RutinBulanan->value;
        $this->target_date = null;
        $this->note = null;
    }

    protected function sanitizeAmount(string $value): float
    {
        $clean = preg_replace('/[^\d.,]/', '', $value);

        if (empty($clean)) {
            return 0.0;
        }

        if (str_contains($clean, '.')) {
            if (str_contains($clean, ',')) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace('.', '', $clean);
            }
        } else {
            if (str_contains($clean, ',')) {
                $clean = str_replace(',', '.', $clean);
            }
        }

        return (float) $clean;
    }

    public function saveItem(): void
    {
        $this->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'target_amount' => ['required', 'string'],
            'priority' => ['required', Rule::enum(ItemPriority::class)],
            'target_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $numericTarget = $this->sanitizeAmount($this->target_amount);

        if ($numericTarget < 0) {
            $this->addError('target_amount', 'Target biaya tidak boleh negatif.');

            return;
        }

        $itemData = [
            'user_id' => Auth::id(),
            'category_id' => $this->category_id,
            'account_id' => $this->account_id,
            'name' => $this->item_name,
            'target_amount' => $numericTarget,
            'priority' => $this->priority,
            'target_date' => $this->target_date ?: null,
            'note' => $this->note,
        ];

        if ($this->editingItemId) {
            $item = Item::findOrFail($this->editingItemId);
            $item->update($itemData);
            session()->flash('message', 'Item berhasil diperbarui!');
        } else {
            $itemData['current_amount'] = 0;
            $itemData['status'] = ItemStatus::Belum->value;
            $item = Item::create($itemData);
            session()->flash('message', 'Item baru berhasil ditambahkan!');
        }

        // Recalculate item status & current amount
        app(FinancialCalculatorService::class)->recalculateItemStatus($item);

        $this->closeItemModal();
    }

    public function confirmDeleteItem(int $itemId): void
    {
        $this->deletingItemId = $itemId;
        $this->showDeleteConfirm = true;
    }

    public function deleteItem(): void
    {
        if ($this->deletingItemId) {
            $item = Item::find($this->deletingItemId);
            if ($item) {
                $item->delete();
                session()->flash('message', 'Item berhasil dihapus.');
            }
        }

        $this->showDeleteConfirm = false;
        $this->deletingItemId = null;
    }

    // Category Modal Management
    public function openCategoryModal(?int $categoryId = null): void
    {
        $this->resetValidation();
        $this->editingCategoryId = $categoryId;

        if ($categoryId) {
            $cat = Category::findOrFail($categoryId);
            $this->category_name = $cat->name;
            $this->category_description = $cat->description;
            $this->category_color = $cat->color ?? '#3B82F6';
            $this->category_icon = $cat->icon ?? 'heroicon-o-folder';
        } else {
            $this->resetCategoryForm();
        }

        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    public function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->category_name = '';
        $this->category_description = null;
        $this->category_color = '#3B82F6';
        $this->category_icon = 'heroicon-o-folder';
    }

    public function saveCategory(): void
    {
        $this->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'category_description' => ['nullable', 'string', 'max:500'],
            'category_color' => ['nullable', 'string', 'max:20'],
        ]);

        $catData = [
            'user_id' => Auth::id(),
            'name' => $this->category_name,
            'description' => $this->category_description,
            'color' => $this->category_color,
            'icon' => $this->category_icon,
        ];

        if ($this->editingCategoryId) {
            $cat = Category::findOrFail($this->editingCategoryId);
            $cat->update($catData);
            session()->flash('message', 'Pos Utama berhasil diperbarui!');
        } else {
            Category::create($catData);
            session()->flash('message', 'Pos Utama baru berhasil ditambahkan!');
        }

        $this->closeCategoryModal();
    }

    public function render(): View
    {
        $categoriesQuery = Category::query()
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id())->orWhereNull('user_id'))
            ->with(['items' => function ($q) {
                $q->with([
                    'account',
                    'transactions' => function ($transactionQuery) {
                        $transactionQuery->with(['account', 'category', 'targetAccount'])
                            ->orderByDesc('transaction_date')
                            ->orderByDesc('id');
                    },
                    'targetTransactions' => function ($transactionQuery) {
                        $transactionQuery->with(['account', 'category', 'targetAccount'])
                            ->orderByDesc('transaction_date')
                            ->orderByDesc('id');
                    },
                ])
                    ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                    ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter));
            }])
            ->orderBy('name');

        $categories = $categoriesQuery->get();

        $categorySummaries = $categories->map(function (Category $category): array {
            $targetTotal = (float) $category->items->sum('target_amount');
            $currentTotal = (float) $category->items->sum('current_amount');

            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'color' => $category->color,
                'item_count' => $category->items->count(),
                'target_total' => $targetTotal,
                'current_total' => $currentTotal,
                'remaining_total' => max(0, $targetTotal - $currentTotal),
                'items' => $category->items->map(function (Item $item): array {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'status' => $item->status,
                        'priority' => $item->priority,
                        'current_amount' => (float) $item->current_amount,
                        'target_amount' => (float) $item->target_amount,
                    ];
                })->all(),
            ];
        })->values();

        $accounts = Account::query()
            ->when(Auth::check(), fn ($q) => $q->where('user_id', Auth::id())->orWhereNull('user_id'))
            ->where('is_active', true)
            ->get();

        $selectedItem = null;
        $selectedItemLogs = collect();

        if ($this->selectedItemId) {
            $selectedItem = Item::query()
                ->with([
                    'category',
                    'account',
                    'transactions' => function ($transactionQuery) {
                        $transactionQuery->with(['account', 'category', 'targetAccount'])
                            ->orderByDesc('transaction_date')
                            ->orderByDesc('id');
                    },
                    'targetTransactions' => function ($transactionQuery) {
                        $transactionQuery->with(['account', 'category', 'targetAccount'])
                            ->orderByDesc('transaction_date')
                            ->orderByDesc('id');
                    },
                ])
                ->find($this->selectedItemId);

            if ($selectedItem) {
                $selectedItemLogs = $selectedItem->transactions
                    ->merge($selectedItem->targetTransactions)
                    ->sortByDesc(fn (Transaction $transaction): string => $transaction->transaction_date?->format('Y-m-d') ?? '')
                    ->sortByDesc(fn (Transaction $transaction): int => (int) $transaction->id)
                    ->values();
            }
        }

        return view('livewire.sinking-funds.index', [
            'categories' => $categories,
            'categorySummaries' => $categorySummaries,
            'accounts' => $accounts,
            'selectedItem' => $selectedItem,
            'selectedItemLogs' => $selectedItemLogs,
        ]);
    }
}
