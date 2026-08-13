<?php

namespace App\Livewire\Transactions;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Services\FinancialCalculatorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FastEntryModal extends Component
{
    public bool $isOpen = false;

    public string $transaction_date = '';

    public string $type = 'expense';

    /**
     * Mode tujuan transfer: 'account' (Antar Dompet) atau 'sinking_fund' (Alokasi Sinking Fund)
     */
    public string $transfer_target_type = 'account';

    public ?int $category_id = null;

    public ?int $item_id = null;

    public ?int $target_item_id = null;

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

    /**
     * Handler saat jenis transfer target berubah (Antar Dompet vs Sinking Fund)
     */
    public function updatedTransferTargetType(): void
    {
        // Bersihkan state yang berlawanan agar tidak ada double input
        if ($this->transfer_target_type === 'account') {
            $this->category_id = null;
            $this->target_item_id = null;
        } else {
            $this->target_account_id = null;
        }
    }

    public function updatedCategoryId(): void
    {
        if ($this->type === TransactionType::Transfer->value) {
            $this->target_item_id = null;

            return;
        }

        $this->item_id = null;
    }

    public function updatedType(): void
    {
        if ($this->type !== TransactionType::Transfer->value) {
            $this->target_account_id = null;
            $this->target_item_id = null;

            return;
        }

        $this->item_id = null;
        // Tentukan default transfer target type saat berpindah ke mode Transfer
        $this->transfer_target_type = 'account';
    }

    protected function rules(): array
    {
        $isTransfer = $this->type === TransactionType::Transfer->value;

        return [
            'transaction_date' => ['required', 'date'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'item_id' => ['nullable', 'exists:items,id'],
            'target_item_id' => [
                Rule::requiredIf($isTransfer && $this->transfer_target_type === 'sinking_fund'),
                'nullable',
                'exists:items,id',
            ],
            'amount' => ['required', 'string'],
            'account_id' => ['required', 'exists:accounts,id'],
            'target_account_id' => [
                Rule::requiredIf($isTransfer && $this->transfer_target_type === 'account'),
                'nullable',
                'different:account_id',
                'exists:accounts,id',
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
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

    public function save(): void
    {
        $this->validate();

        $numericAmount = $this->sanitizeAmount($this->amount);

        if ($numericAmount <= 0) {
            $this->addError('amount', 'Nominal transaksi harus lebih besar dari 0.');

            return;
        }

        if ($this->type === TransactionType::Transfer->value) {
            // Pastikan state aman sebelum menyimpan
            if ($this->transfer_target_type === 'account') {
                $this->target_item_id = null;
                $this->category_id = null;
            } else {
                $this->target_account_id = null;
            }

            $sourceAccount = Account::query()->find($this->account_id);

            if (! $sourceAccount) {
                throw ValidationException::withMessages([
                    'account_id' => 'Dompet asal tidak ditemukan.',
                ]);
            }

            $availableBalance = app(FinancialCalculatorService::class)->getRealAccountBalance($sourceAccount);

            if ($availableBalance < $numericAmount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo asal tidak mencukupi untuk transfer ini.',
                ]);
            }
        }

        Transaction::create([
            'user_id' => Auth::id(),
            'transaction_date' => $this->transaction_date,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'item_id' => $this->item_id,
            'account_id' => $this->account_id,
            'target_account_id' => $this->type === TransactionType::Transfer->value ? $this->target_account_id : null,
            'target_item_id' => $this->type === TransactionType::Transfer->value ? $this->target_item_id : null,
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
        $this->transfer_target_type = 'account';
        $this->category_id = null;
        $this->item_id = null;
        $this->target_item_id = null;
        $this->amount = '';
        $this->account_id = null;
        $this->target_account_id = null;
        $this->notes = null;
    }

    public function render(): View
    {
        $categories = Category::query()
            ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id())->orWhereNull('user_id'))
            ->orderBy('name')
            ->get();

        $availableItems = collect();
        if ($this->category_id) {
            $availableItems = Item::query()
                ->where('category_id', $this->category_id)
                ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id())->orWhereNull('user_id'))
                ->orderBy('name')
                ->get();
        }

        $accounts = Account::query()
            ->when(Auth::check(), fn($q) => $q->where('user_id', Auth::id())->orWhereNull('user_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.transactions.index', [
            'categories' => $categories,
            'availableItems' => $availableItems,
            'accounts' => $accounts,
        ]);
    }
}
