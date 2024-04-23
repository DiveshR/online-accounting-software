<?php

namespace App\BulkActions\Purchases;

use App\Abstracts\BulkAction;
use App\Events\Document\DocumentCancelled;
use App\Events\Document\DocumentReceived;
use App\Exports\Purchases\Bills\Bills as Export;
use App\Jobs\Banking\CreateBankingDocumentTransaction;
use App\Jobs\Document\CreateDocumentHistory;
use App\Jobs\Document\DeleteDocument;
use App\Jobs\Document\UpdateDocument;
use App\Models\Document\Document;

class Bills extends BulkAction
{
    public $model = Document::class;

    public $text = 'general.bills';

    public $path = [
        'group' => 'purchases',
        'type' => 'bills',
    ];

    public $actions = [
        'edit' => [
            'icon'          => 'edit',
            'name'          => 'general.edit',
            'message'       => '',
            'permission'    => 'update-purchases-bills',
            'type'          => 'modal',
            'handle'        => 'update',
        ],
        'received'  => [
            'icon'          => 'send',
            'name'          => 'bills.mark_received',
            'message'       => 'bulk_actions.message.received',
            'permission'    => 'update-purchases-bills',
        ],
        'cancelled' => [
            'icon'          => 'cancel',
            'name'          => 'documents.actions.cancel',
            'message'       => 'bulk_actions.message.cancelled',
            'permission'    => 'update-purchases-bills',
        ],
        'delete'    => [
            'icon'          => 'delete',
            'name'          => 'general.delete',
            'message'       => 'bulk_actions.message.delete',
            'permission'    => 'delete-purchases-bills',
        ],
        'export'    => [
            'icon'          => 'file_download',
            'name'          => 'general.export',
            'message'       => 'bulk_actions.message.export',
            'type'          => 'download',
        ],
    ];

    public function edit($request)
    {
        $selected = $this->getSelectedInput($request);

        return $this->response('bulk-actions.purchases.bills.edit', compact('selected'));
    }

    public function update($request)
    {
        $bills = $this->getSelectedRecords($request);

        foreach ($bills as $bill) {
            try {
                $discount = $bill->totals->where('code', 'discount')->makeHidden('title')->pluck('amount')->first();

                // for extra total rows..
                $totals = $bill->totals()->whereNotIn('code', ['sub_total', 'total', 'tax', 'discount'])->get()->toArray();

                $request->merge([
                    'items' => $bill->items->toArray(),
                    'uploaded_attachment' => $bill->attachment,
                    'category_id' => ($request->get('category_id')) ?? $bill->category_id,
                    'discount' => $discount,
                    'totals' => $totals,
                ])->except([

                ]);

                $this->dispatch(new UpdateDocument($bill, $this->getUpdateRequest($request)));
            } catch (\Exception $e) {
                flash($e->getMessage())->error()->important();
            }
        }
    }

    public function received($request)
    {
        $bills = $this->getSelectedRecords($request);

        foreach ($bills as $bill) {
            if ($bill->status == 'received') {
                continue;
            }

            event(new DocumentReceived($bill));
        }
    }

    public function cancelled($request)
    {
        $bills = $this->getSelectedRecords($request);

        foreach ($bills as $bill) {
            if (in_array($bill->status, ['cancelled', 'draft'])) {
                continue;
            }

            event(new DocumentCancelled($bill));
        }
    }

    public function duplicate($request)
    {
        $bills = $this->getSelectedRecords($request);

        foreach ($bills as $bill) {
            $clone = $bill->duplicate();

            $description = trans('messages.success.added', ['type' => $clone->document_number]);

            $this->dispatch(new CreateDocumentHistory($clone, 0, $description));
        }
    }

    public function destroy($request)
    {
        $bills = $this->getSelectedRecords($request, [
            'items', 'item_taxes', 'histories', 'transactions', 'recurring', 'totals'
        ]);

        foreach ($bills as $bill) {
            try {
                $this->dispatch(new DeleteDocument($bill));
            } catch (\Exception $e) {
                flash($e->getMessage())->error()->important();
            }
        }
    }

    public function export($request)
    {
        $selected = $this->getSelectedInput($request);

        return $this->exportExcel(new Export($selected), trans_choice('general.bills', 2));
    }
}
