<?php

namespace App\Observers;

use App\Models\Item;
use App\Models\Transaction;
use App\Services\FinancialCalculatorService;

class TransactionObserver
{
    public function __construct(
        protected FinancialCalculatorService $calculatorService
    ) {}

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $this->recalculateAffectedItems($transaction);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $this->recalculateAffectedItems($transaction, true);
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $this->recalculateAffectedItems($transaction);
    }

    /**
     * Recalculate status for all items affected by the transaction.
     */
    protected function recalculateAffectedItems(Transaction $transaction, bool $checkOriginal = false): void
    {
        $itemIds = array_filter([
            $transaction->item_id,
            $transaction->target_item_id,
        ]);

        if ($checkOriginal) {
            $originalItemId = $transaction->getOriginal('item_id');
            $originalTargetItemId = $transaction->getOriginal('target_item_id');

            if ($originalItemId) {
                $itemIds[] = $originalItemId;
            }
            if ($originalTargetItemId) {
                $itemIds[] = $originalTargetItemId;
            }
        }

        $uniqueItemIds = array_unique($itemIds);

        if (empty($uniqueItemIds)) {
            return;
        }

        $items = Item::whereIn('id', $uniqueItemIds)->get();

        foreach ($items as $item) {
            $this->calculatorService->recalculateItemStatus($item);
        }
    }
}
