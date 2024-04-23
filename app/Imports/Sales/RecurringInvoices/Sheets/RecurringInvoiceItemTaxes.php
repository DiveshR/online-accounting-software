<?php

namespace App\Imports\Sales\RecurringInvoices\Sheets;

use App\Abstracts\Import;
use App\Http\Requests\Document\DocumentItemTax as Request;
use App\Models\Common\Item;
use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use App\Models\Document\DocumentItemTax as Model;

class RecurringInvoiceItemTaxes extends Import
{
    public $request_class = Request::class;

    public $model = Model::class;

    public $columns = [
        'type',
        'document_id',
        'tax_id',
        'name',
        'amount'
    ];

    public function model(array $row)
    {
        if (self::hasRow($row)) {
            return;
        }

        return new Model($row);
    }

    public function map($row): array
    {
        if ($this->isEmpty($row, 'invoice_number')) {
            return [];
        }

        $row['invoice_number'] = (string) $row['invoice_number'];

        $row = parent::map($row);

        $row['document_id'] = (int) Document::where('type', '=', Document::INVOICE_RECURRING_TYPE)
            ->number($row['invoice_number'])
            ->pluck('id')
            ->first();

        if (empty($row['document_item_id']) && !empty($row['item_name'])) {
            $item_id = Item::name($row['item_name'])->pluck('id')->first();

            $row['document_item_id'] = DocumentItem::where('type', '=', Document::INVOICE_RECURRING_TYPE)
                ->where('item_id', $item_id)
                ->pluck('id')
                ->first();
        }

        $row['tax_id'] = $this->getTaxId($row);

        if (empty($row['name']) && !empty($row['item_name'])) {
            $row['name'] = $row['item_name'];
        }

        $row['amount'] = (double) $row['amount'];

        $row['type'] = Document::INVOICE_RECURRING_TYPE;

        return $row;
    }

    public function prepareRules(array $rules): array
    {
        $rules['invoice_number'] = 'required|string';

        unset($rules['invoice_id']);

        return $rules;
    }
}
