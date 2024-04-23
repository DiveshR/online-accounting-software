<?php

namespace App\Exports\Purchases\RecurringBills\Sheets;

use App\Abstracts\Export;
use App\Models\Document\DocumentTotal as Model;

class RecurringBillTotals extends Export
{
    public function collection()
    {
        return Model::with('document')->billRecurring()->collectForExport($this->ids, null, 'document_id');
    }

    public function map($model): array
    {
        $document = $model->document;

        if (empty($document)) {
            return [];
        }

        $model->bill_number = $document->document_number;

        return parent::map($model);
    }

    public function fields(): array
    {
        return [
            'bill_number',
            'code',
            'name',
            'amount',
            'sort_order',
        ];
    }
}
