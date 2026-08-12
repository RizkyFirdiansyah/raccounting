<?php

use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinancialCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('new transaction automatically updates item current amount and status from belum to proses or terpenuhi', function () {
    $category = Category::factory()->create();
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'target_amount' => 1000000,
        'current_amount' => 0,
        'status' => ItemStatus::Belum,
    ]);

    expect($item->status)->toBe(ItemStatus::Belum);

    // 1. Partial income -> Status changes to Proses
    Transaction::factory()->create([
        'type' => TransactionType::Income,
        'item_id' => $item->id,
        'amount' => 500000,
    ]);

    $item->refresh();
    expect((float) $item->current_amount)->toEqual(500000.0)
        ->and($item->status)->toBe(ItemStatus::Proses);

    // 2. Additional income reaching target -> Status changes to Terpenuhi
    Transaction::factory()->create([
        'type' => TransactionType::Income,
        'item_id' => $item->id,
        'amount' => 500000,
    ]);

    $item->refresh();
    expect((float) $item->current_amount)->toEqual(1000000.0)
        ->and($item->status)->toBe(ItemStatus::Terpenuhi);
});

test('updating or deleting transaction triggers item status recalculation', function () {
    $category = Category::factory()->create();
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'target_amount' => 1000000,
    ]);

    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Income,
        'item_id' => $item->id,
        'amount' => 1000000,
    ]);

    expect($item->fresh()->status)->toBe(ItemStatus::Terpenuhi);

    // Update transaction amount down to 400k
    $transaction->update(['amount' => 400000]);
    expect((float) $item->fresh()->current_amount)->toEqual(400000.0)
        ->and($item->fresh()->status)->toBe(ItemStatus::Proses);

    // Delete transaction
    $transaction->delete();
    expect((float) $item->fresh()->current_amount)->toEqual(0.0)
        ->and($item->fresh()->status)->toBe(ItemStatus::Belum);
});

test('real account balance calculation produces accurate results', function () {
    $account = Account::factory()->create([
        'initial_balance' => 1000000,
    ]);

    $targetAccount = Account::factory()->create([
        'initial_balance' => 200000,
    ]);

    // Income +500.000 to account
    Transaction::factory()->create([
        'account_id' => $account->id,
        'type' => TransactionType::Income,
        'amount' => 500000,
    ]);

    // Expense -300.000 from account
    Transaction::factory()->create([
        'account_id' => $account->id,
        'type' => TransactionType::Expense,
        'amount' => 300000,
    ]);

    // Transfer out -100.000 from account to targetAccount
    Transaction::factory()->create([
        'account_id' => $account->id,
        'target_account_id' => $targetAccount->id,
        'type' => TransactionType::Transfer,
        'amount' => 100000,
    ]);

    $service = app(FinancialCalculatorService::class);

    // Account 1: 1.000.000 + 500.000 - 300.000 - 100.000 = 1.100.000
    expect($service->getRealAccountBalance($account))->toBe(1100000.0);

    // Account 2: 200.000 + 100.000 (transfer in) = 300.000
    expect($service->getRealAccountBalance($targetAccount))->toBe(300000.0);
});

test('monthly summary calculates income expense net cashflow and total savings correctly', function () {
    $user = User::factory()->create();
    $item = Item::factory()->create(['user_id' => $user->id, 'target_amount' => 500000]);

    // Income 2.000.000 in May 2026
    Transaction::factory()->create([
        'user_id' => $user->id,
        'type' => TransactionType::Income,
        'amount' => 2000000,
        'transaction_date' => '2026-05-10',
    ]);

    // Expense 500.000 in May 2026
    Transaction::factory()->create([
        'user_id' => $user->id,
        'type' => TransactionType::Expense,
        'amount' => 500000,
        'transaction_date' => '2026-05-15',
    ]);

    // Savings allocation (Income into Item) 300.000 in May 2026
    Transaction::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'type' => TransactionType::Income,
        'amount' => 300000,
        'transaction_date' => '2026-05-20',
    ]);

    $service = app(FinancialCalculatorService::class);
    $summary = $service->getMonthlySummary(5, 2026, $user->id);

    expect($summary['total_income'])->toBe(2300000.0)
        ->and($summary['total_expense'])->toBe(500000.0)
        ->and($summary['net_cashflow'])->toBe(1800000.0)
        ->and($summary['total_savings'])->toBe(300000.0);
});
