<?php

namespace App\Livewire\Transactions;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

class FastEntryModal extends Component
{
    public bool $isOpen = false;

    public string $transaction_date = '';

    public string $type = 'expense';

    public ?int $category_id = null;

    public ?int $item_id = null;

    public string $amount = '';

    public ?int $account_id = null;

    public ?int $target_account_id = null;

    public ?string $notes = null;

    public function mount(): void
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->type = TransactionType::Expense->value;
    }

    public function openModal(): void
    {
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function updatedCategoryId(): void
    {
        $this->item_id = null;
    }

    public function updatedType(): void
    {
        if ($this->type !== TransactionType::Transfer->value) {
            $this->target_account_id = null;
        }
    }

    protected function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'item_id' => ['nullable', 'exists:items,id'],
            'amount' => ['required', 'string'],
            'account_id' => ['required', 'exists:accounts,id'],
            'target_account_id' => [
                Rule::requiredIf($this->type === TransactionType::Transfer->value),
                'nullable',
                'different:account_id',
                'exists:accounts,id',
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function sanitizeAmount(string $value): float
    {
        // Strip out 'Rp', spaces, and currency symbols
        $clean = preg_replace('/[^\d.,]/', '', $value);

        if (empty($clean)) {
            return 0.0;
        }

        // If formatted like Indonesian currency "150.000,00" or "150.000"
        if (str_contains($clean, '.')) {
            if (str_contains($clean, ',')) {
                // "150.000,50" -> remove thousand dots, replace decimal comma with dot
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                // "150.000" -> remove thousand dots
                $clean = str_replace('.', '', $clean);
            }
        } else {
            if (str_contains($clean, ',')) {
                // "150000,50" -> replace comma with dot
                $clean = str_replace(',', '.', $clean);
            }
        }

        return (float) $clean;
    }

    public function save(): void
    {
        $this->validate();

        $numericAmount = $this->sanitizeAmount($this->amount);

        if ($numericAmount <= 0) {
            $this->addError('amount', 'Nominal transaksi harus lebih besar dari 0.');

            return;
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'transaction_date' => $this->transaction_date,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'item_id' => $this->item_id,
            'account_id' => $this->account_id,
            'target_account_id' => $this->type === TransactionType::Transfer->value ? $this->target_account_id : null,
            'amount' => $numericAmount,
            'description' => $this->notes,
        ]);

        session()->flash('message', 'Transaksi berhasil disimpan!');
        $this->dispatch('transaction-created');
        $this->closeModal();
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->transaction_date = now()->format('Y-m-d');
        $this->type = TransactionType::Expense->value;
        $this->category_id = null;
        $this->item_id = null;
        $this->amount = '';
        $this->account_id = null;
        $this->target_account_id = null;
        $this->notes = null;
    }

    public function render(): View
    {
        $categories = Category::query()
            ->when(auth()->check(), fn ($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'))
            ->orderBy('name')
            ->get();

        $availableItems = collect();
        if ($this->category_id) {
            $availableItems = Item::query()
                ->where('category_id', $this->category_id)
                ->when(auth()->check(), fn ($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'))
                ->orderBy('name')
                ->get();
        }

        $accounts = Account::query()
            ->when(auth()->check(), fn ($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.transactions.fast-entry-modal', [
            'categories' => $categories,
            'availableItems' => $availableItems,
            'accounts' => $accounts,
        ]);
    }
}
