<?php

use App\Enums\ItemPriority;
use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Livewire\Dashboard\Index;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('dashboard renders recent transaction logs inside the page', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $category = Category::factory()->create(['user_id' => $user->id]);
  $account = Account::factory()->create(['user_id' => $user->id]);

  Transaction::factory()->create([
    'user_id' => $user->id,
    'account_id' => $account->id,
    'category_id' => $category->id,
    'type' => TransactionType::Expense,
    'description' => 'Belanja bulanan',
    'transaction_date' => '2026-08-11',
  ]);

  Livewire::test(Index::class)
    ->assertSuccessful()
    ->assertSee('Log Transaksi Terbaru')
    ->assertSee('Belanja bulanan');
});

test('dashboard shows and filters sinking fund targets', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  $category = Category::factory()->create(['user_id' => $user->id]);

  Item::factory()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'name' => 'Dana Darurat',
    'target_amount' => 1000000,
    'current_amount' => 250000,
    'status' => ItemStatus::Proses,
    'priority' => ItemPriority::Emergency,
  ]);

  Item::factory()->create([
    'category_id' => $category->id,
    'user_id' => $user->id,
    'name' => 'Liburan Jepang',
    'target_amount' => 5000000,
    'current_amount' => 5000000,
    'status' => ItemStatus::Terpenuhi,
    'priority' => ItemPriority::KeinginanShortterm,
  ]);

  Livewire::test(Index::class)
    ->set('itemStatusFilter', ItemStatus::Proses->value)
    ->assertSee('Dana Darurat')
    ->assertDontSee('Liburan Jepang')
    ->set('itemPriorityFilter', ItemPriority::Emergency->value)
    ->assertSee('Dana Darurat');
});
