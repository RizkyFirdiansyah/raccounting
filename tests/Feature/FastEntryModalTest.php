<?php

use App\Enums\AccountType;
use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Livewire\Transactions\FastEntryModal;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinancialCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('fast entry modal component renders and toggles open state', function () {
    Livewire::test(FastEntryModal::class)
        ->assertSet('isOpen', false)
        ->call('openModal')
        ->assertSet('isOpen', true)
        ->call('closeModal')
        ->assertSet('isOpen', false);
});

test('changing category cascades available items and resets selected item_id', function () {
    $category1 = Category::factory()->create(['name' => 'Kategori A']);
    $category2 = Category::factory()->create(['name' => 'Kategori B']);

    $item1 = Item::factory()->create(['category_id' => $category1->id, 'name' => 'Item A1']);
    $item2 = Item::factory()->create(['category_id' => $category2->id, 'name' => 'Item B1']);

    Livewire::test(FastEntryModal::class)
        ->set('category_id', $category1->id)
        ->set('item_id', $item1->id)
        ->assertSet('item_id', $item1->id)
        ->set('category_id', $category2->id)
        ->assertSet('item_id', null);
});

test('validation requires transfer destination and blocks insufficient source balance', function () {
    $account = Account::factory()->create();
    $targetAccount = Account::factory()->create();
    $category = Category::factory()->create();
    $item = Item::factory()->create(['category_id' => $category->id]);

    // 1. Missing account & amount
    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->call('save');

    expect(Transaction::count())->toBe(0);

    // 2. Transfer missing destination
    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->set('type', TransactionType::Transfer->value)
        ->set('account_id', $account->id)
        ->set('amount', 'Rp 100.000')
        ->call('save');

    expect(Transaction::count())->toBe(0);

    // 3. Transfer with same account_id and target_account_id
    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->set('type', TransactionType::Transfer->value)
        ->set('account_id', $account->id)
        ->set('target_account_id', $account->id)
        ->set('amount', 'Rp 100.000')
        ->call('save');

    expect(Transaction::count())->toBe(0);

    // 4. Transfer to sinking fund item with insufficient balance
    $this->actingAs(User::factory()->create());

    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->set('type', TransactionType::Transfer->value)
        ->set('account_id', $targetAccount->id)
        ->set('category_id', $category->id)
        ->set('target_item_id', $item->id)
        ->set('amount', 'Rp 999.999')
        ->call('save');

    expect(Transaction::count())->toBe(0);
});

test('successful transaction creation sanitizes currency input and updates item status', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'user_id' => $user->id,
        'target_amount' => 500000,
        'current_amount' => 0,
        'status' => ItemStatus::Belum,
    ]);

    $account = Account::factory()->create(['user_id' => $user->id, 'type' => AccountType::Bank]);

    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->set('transaction_date', '2026-08-12')
        ->set('type', TransactionType::Income->value)
        ->set('category_id', $category->id)
        ->set('item_id', $item->id)
        ->set('account_id', $account->id)
        ->set('amount', 'Rp 500.000,00')
        ->set('notes', 'Gaji / Sinking fund')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    expect(Transaction::count())->toBe(1);

    $transaction = Transaction::first();
    expect($transaction->amount)->toEqual(500000.0)
        ->and($transaction->type)->toBe(TransactionType::Income)
        ->and($transaction->month)->toBe(8)
        ->and($transaction->year)->toBe(2026)
        ->and($transaction->description)->toBe('Gaji / Sinking fund');

    // Verify Item observer updated status to Terpenuhi
    $item->refresh();
    expect((float) $item->current_amount)->toEqual(500000.0)
        ->and($item->status)->toBe(ItemStatus::Terpenuhi);
});

test('transfer to a sinking fund item allocates existing account balance and fulfills the item', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'user_id' => $user->id,
        'target_amount' => 300000,
        'current_amount' => 0,
        'status' => ItemStatus::Belum,
    ]);

    $sourceAccount = Account::factory()->create([
        'user_id' => $user->id,
        'type' => AccountType::Bank,
        'initial_balance' => 500000,
        'current_balance' => 500000,
    ]);

    Livewire::test(FastEntryModal::class)
        ->call('openModal')
        ->set('transaction_date', '2026-08-12')
        ->set('type', TransactionType::Transfer->value)
        ->set('category_id', $category->id)
        ->set('target_item_id', $item->id)
        ->set('account_id', $sourceAccount->id)
        ->set('amount', 'Rp 300.000')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('isOpen', false);

    expect(Transaction::count())->toBe(1);

    $transaction = Transaction::first();
    expect($transaction->type)->toBe(TransactionType::Transfer)
        ->and($transaction->target_item_id)->toBe($item->id)
        ->and($transaction->target_account_id)->toBeNull();

    $item->refresh();
    expect((float) $item->current_amount)->toEqual(300000.0)
        ->and($item->status)->toBe(ItemStatus::Terpenuhi);

    $balance = app(FinancialCalculatorService::class)->getRealAccountBalance($sourceAccount);
    expect($balance)->toEqual(200000.0);
});
