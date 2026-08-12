<?php

namespace App\Models;

use App\Enums\TransactionType;
use Carbon\Carbon;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'target_account_id',
        'category_id',
        'item_id',
        'target_item_id',
        'type',
        'amount',
        'transaction_date',
        'month',
        'year',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Transaction $transaction): void {
            if ($transaction->transaction_date) {
                $date = Carbon::parse($transaction->transaction_date);
                $transaction->month = (int) $date->format('n');
                $transaction->year = (int) $date->format('Y');
            }
        });
    }

    /**
     * Scope query to filter transactions by month and year.
     *
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    public function scopeForMonthYear(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope query to filter transactions by user ID.
     *
     * @param  Builder<Transaction>  $query
     * @return Builder<Transaction>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function targetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'target_account_id');
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function targetItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'target_item_id');
    }
}
