<?php

use App\Enums\AccountType;
use App\Enums\ItemPriority;
use App\Enums\ItemStatus;
use App\Enums\TransactionType;
use App\Livewire\SinkingFunds\Index;
use App\Models\Account;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('sinking funds index component renders successfully', function () {
    $category = Category::factory()->create(['name' => 'Keseharian']);
    Item::factory()->create(['category_id' => $category->id, 'name' => 'Dana Beras']);

    Livewire::test(Index::class)
        ->assertOk()
        ->assertSee('Keseharian')
        ->assertSee('Dana Beras');
});

test('search filter shows only matching items', function () {
    $category = Category::factory()->create();
    Item::factory()->create(['category_id' => $category->id, 'name' => 'Dana Darurat']);
    Item::factory()->create(['category_id' => $category->id, 'name' => 'Tiket Liburan']);

    Livewire::test(Index::class)
        ->set('search', 'Darurat')
        ->assertSee('Dana Darurat')
        ->assertDontSee('Tiket Liburan');
});

test('status filter shows only items matching status', function () {
    $category = Category::factory()->create();
    Item::factory()->create(['category_id' => $category->id, 'name' => 'Sudah Terpenuhi', 'status' => ItemStatus::Terpenuhi]);
    Item::factory()->create(['category_id' => $category->id, 'name' => 'Belum Proses', 'status' => ItemStatus::Belum]);

    Livewire::test(Index::class)
        ->set('statusFilter', 'terpenuhi')
        ->assertSee('Sudah Terpenuhi')
        ->assertDontSee('Belum Proses');
});

test('modal toggles open and closed when adding new item', function () {
    Category::factory()->create();

    Livewire::test(Index::class)
        ->assertSet('showItemModal', false)
        ->call('openItemModal')
        ->assertSet('showItemModal', true)
        ->call('closeItemModal')
        ->assertSet('showItemModal', false);
});

test('opening edit item modal populates form with existing item data', function () {
    $category = Category::factory()->create();
    $account = Account::factory()->create(['type' => AccountType::Bank]);
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'account_id' => $account->id,
        'name' => 'Servis AC',
        'target_amount' => 500000,
        'priority' => ItemPriority::Wajib,
    ]);

    Livewire::test(Index::class)
        ->call('openItemModal', $item->id)
        ->assertSet('item_name', 'Servis AC')
        ->assertSet('category_id', $category->id)
        ->assertSet('account_id', $account->id)
        ->assertSet('priority', ItemPriority::Wajib->value)
        ->assertSet('showItemModal', true);
});

test('creating a new item saves to database and closes modal', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $account = Account::factory()->create(['user_id' => $user->id]);

    Livewire::test(Index::class)
        ->call('openItemModal')
        ->set('item_name', 'Liburan Bali')
        ->set('category_id', $category->id)
        ->set('account_id', $account->id)
        ->set('target_amount', 'Rp 3.500.000')
        ->set('priority', ItemPriority::KeinginanShortterm->value)
        ->call('saveItem')
        ->assertHasNoErrors()
        ->assertSet('showItemModal', false);

    $item = Item::where('name', 'Liburan Bali')->first();
    expect($item)->not->toBeNull()
        ->and((float) $item->target_amount)->toEqual(3500000.0)
        ->and($item->priority)->toBe(ItemPriority::KeinginanShortterm)
        ->and($item->status)->toBe(ItemStatus::Belum);
});

test('saving item with invalid data shows validation errors', function () {
    Livewire::test(Index::class)
        ->call('openItemModal')
        ->call('saveItem')
        ->assertHasErrors(['item_name', 'category_id', 'target_amount']);
});

test('updating an existing item persists changes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'user_id' => $user->id,
        'name' => 'Laptop Baru',
        'target_amount' => 5000000,
    ]);

    Livewire::test(Index::class)
        ->call('openItemModal', $item->id)
        ->set('item_name', 'Laptop Gaming')
        ->set('target_amount', 'Rp 10.000.000')
        ->call('saveItem')
        ->assertHasNoErrors()
        ->assertSet('showItemModal', false);

    expect($item->fresh()->name)->toBe('Laptop Gaming')
        ->and((float) $item->fresh()->target_amount)->toEqual(10000000.0);
});

test('confirming delete and deleting item removes it from the database', function () {
    $category = Category::factory()->create();
    $item = Item::factory()->create(['category_id' => $category->id]);

    Livewire::test(Index::class)
        ->call('confirmDeleteItem', $item->id)
        ->assertSet('showDeleteConfirm', true)
        ->assertSet('deletingItemId', $item->id)
        ->call('deleteItem')
        ->assertSet('showDeleteConfirm', false);

    expect(Item::find($item->id))->toBeNull();
});

test('category modal opens and saves a new category', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(Index::class)
        ->call('openCategoryModal')
        ->assertSet('showCategoryModal', true)
        ->set('category_name', 'Tabungan Hari Tua')
        ->set('category_description', 'Dana pensiun jangka panjang')
        ->set('category_color', '#6366F1')
        ->call('saveCategory')
        ->assertHasNoErrors()
        ->assertSet('showCategoryModal', false);

    $category = Category::where('name', 'Tabungan Hari Tua')->first();
    expect($category)->not->toBeNull()
        ->and($category->color)->toBe('#6366F1');
});

test('item detail modal shows transaction logs for a sinking fund item', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create([
        'category_id' => $category->id,
        'user_id' => $user->id,
        'name' => 'Keyboard Mechanical',
        'target_amount' => 450000,
        'current_amount' => 100000,
    ]);

    Transaction::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'type' => TransactionType::Income,
        'amount' => 100000,
        'transaction_date' => '2026-08-10',
        'description' => 'Setoran awal keyboard',
    ]);

    Livewire::test(Index::class)
        ->call('openItemDetail', $item->id)
        ->assertSet('showItemDetailModal', true)
        ->assertSee('Riwayat Transaksi Item')
        ->assertSee('Setoran awal keyboard');
});
