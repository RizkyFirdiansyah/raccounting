<?php

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\AccountSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('default seeders populate categories and accounts', function () {
    $this->seed([CategorySeeder::class, AccountSeeder::class]);

    expect(Category::count())->toBe(4)
        ->and(Category::pluck('name')->toArray())->toContain('Keseharian', 'Tagihan Wajib', 'Target/Keinginan', 'Buffer');

    expect(Account::count())->toBe(3)
        ->and(Account::pluck('name')->toArray())->toContain('Cash', 'Bank', 'E-Wallet');
});

test('transaction automatically extracts month and year on saving', function () {
    $transaction = Transaction::factory()->create([
        'transaction_date' => '2026-08-15',
    ]);

    expect($transaction->month)->toBe(8)
        ->and($transaction->year)->toBe(2026);

    $transaction->update([
        'transaction_date' => '2027-12-01',
    ]);

    expect($transaction->fresh()->month)->toBe(12)
        ->and($transaction->fresh()->year)->toBe(2027);
});

test('models belong to user and have proper relationships', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);

    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'item_id' => $item->id,
        'account_id' => $account->id,
        'type' => TransactionType::Expense,
    ]);

    expect($user->categories)->toHaveCount(1)
        ->and($user->items)->toHaveCount(1)
        ->and($user->accounts)->toHaveCount(1)
        ->and($user->transactions)->toHaveCount(1);

    expect($transaction->user->id)->toBe($user->id)
        ->and($transaction->category->id)->toBe($category->id)
        ->and($transaction->item->id)->toBe($item->id)
        ->and($transaction->account->id)->toBe($account->id)
        ->and($transaction->type)->toBe(TransactionType::Expense);
});

test('transaction scopes filter correctly by month year and user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $t1 = Transaction::factory()->create([
        'user_id' => $user1->id,
        'transaction_date' => '2026-05-10',
    ]);

    $t2 = Transaction::factory()->create([
        'user_id' => $user1->id,
        'transaction_date' => '2026-06-15',
    ]);

    $t3 = Transaction::factory()->create([
        'user_id' => $user2->id,
        'transaction_date' => '2026-05-20',
    ]);

    $mayTransactionsUser1 = Transaction::forUser($user1->id)->forMonthYear(5, 2026)->get();

    expect($mayTransactionsUser1)->toHaveCount(1)
        ->and($mayTransactionsUser1->first()->id)->toBe($t1->id);
});
