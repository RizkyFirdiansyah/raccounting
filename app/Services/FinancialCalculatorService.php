<?php

namespace App\Services;

use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Item;
use App\Models\Transaction;

class FinancialCalculatorService
{
    /**
     * Recalculate and update the current_amount and status of a specific Item.
     */
    public function recalculateItemStatus(Item $item): void
    {
        $incomeSum = (float) Transaction::query()
            ->where('type', TransactionType::Income)
            ->where('item_id', $item->id)
            ->sum('amount');

        $transferInSum = (float) Transaction::query()
            ->where('type', TransactionType::Transfer)
            ->where('target_item_id', $item->id)
            ->sum('amount');

        $expenseSum = (float) Transaction::query()
            ->where('type', TransactionType::Expense)
            ->where('item_id', $item->id)
            ->sum('amount');

        $balance = $incomeSum + $transferInSum - $expenseSum;
        $targetAmount = (float) $item->target_amount;

        if ($balance <= 0) {
            $status = ItemStatus::Belum;
        } elseif ($balance >= $targetAmount) {
            $status = ItemStatus::Terpenuhi;
        } else {
            $status = ItemStatus::Proses;
        }

        $item->update([
            'current_amount' => $balance,
            'status' => $status,
        ]);
    }

    /**
     * Calculate the real account balance.
     */
    public function getRealAccountBalance(Account $account): float
    {
        $incomeSum = (float) Transaction::query()
            ->where('type', TransactionType::Income)
            ->where('account_id', $account->id)
            ->sum('amount');

        $transferInSum = (float) Transaction::query()
            ->where('type', TransactionType::Transfer)
            ->where('target_account_id', $account->id)
            ->sum('amount');

        $expenseSum = (float) Transaction::query()
            ->where('type', TransactionType::Expense)
            ->where('account_id', $account->id)
            ->sum('amount');

        $transferOutSum = (float) Transaction::query()
            ->where('type', TransactionType::Transfer)
            ->where('account_id', $account->id)
            ->sum('amount');

        return (float) $account->initial_balance + $incomeSum + $transferInSum - $expenseSum - $transferOutSum;
    }

    /**
     * Get monthly financial summary (Income, Expense, Net Cashflow, Total Savings).
     *
     * @return array{
     *     total_income: float,
     *     total_expense: float,
     *     net_cashflow: float,
     *     total_savings: float
     * }
     */
    public function getMonthlySummary(int $month, int $year, ?int $userId = null): array
    {
        $baseQuery = fn () => Transaction::query()
            ->forMonthYear($month, $year)
            ->when($userId !== null, fn ($q) => $q->forUser($userId));

        $totalIncome = (float) $baseQuery()
            ->where('type', TransactionType::Income)
            ->sum('amount');

        $totalExpense = (float) $baseQuery()
            ->where('type', TransactionType::Expense)
            ->sum('amount');

        $netCashflow = $totalIncome - $totalExpense;

        $incomeToItem = (float) $baseQuery()
            ->where('type', TransactionType::Income)
            ->whereNotNull('item_id')
            ->sum('amount');

        $transferInToItem = (float) $baseQuery()
            ->where('type', TransactionType::Transfer)
            ->whereNotNull('target_item_id')
            ->sum('amount');

        $expenseFromItem = (float) $baseQuery()
            ->where('type', TransactionType::Expense)
            ->whereNotNull('item_id')
            ->sum('amount');

        $totalSavings = $incomeToItem + $transferInToItem - $expenseFromItem;

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_cashflow' => $netCashflow,
            'total_savings' => $totalSavings,
        ];
    }
}
