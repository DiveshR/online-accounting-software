<?php

namespace App\Traits;

use App\Http\Requests\Banking\Account as AccountRequest;
use App\Http\Requests\Common\Contact as ContactRequest;
use App\Http\Requests\Common\Item as ItemRequest;
use App\Http\Requests\Setting\Category as CategoryRequest;
use App\Http\Requests\Setting\Currency as CurrencyRequest;
use App\Http\Requests\Setting\Tax as TaxRequest;
use App\Jobs\Banking\CreateAccount;
use App\Jobs\Common\CreateContact;
use App\Jobs\Common\CreateItem;
use App\Jobs\Setting\CreateCategory;
use App\Jobs\Setting\CreateCurrency;
use App\Jobs\Setting\CreateTax;
use App\Models\Banking\Account;
use App\Models\Banking\Transaction;
use App\Models\Common\Contact;
use App\Models\Common\Item;
use App\Models\Document\Document;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Models\Setting\Tax;
use App\Traits\Jobs;
use App\Traits\Sources;
use Illuminate\Support\Facades\Validator;

trait Import
{
    use Jobs, Sources;

    public function getAccountId($row)
    {
        $id = isset($row['account_id']) ? $row['account_id'] : null;

        if (empty($id) && !empty($row['account_name'])) {
            $id = $this->getAccountIdFromName($row);
        }

        if (empty($id) && !empty($row['account_number'])) {
            $id = $this->getAccountIdFromNumber($row);
        }

        if (empty($id) && !empty($row['currency_code'])) {
            $id = $this->getAccountIdFromCurrency($row);
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getCategoryId($row, $type = null)
    {
        $id = isset($row['category_id']) ? $row['category_id'] : null;

        $type = !empty($type) ? $type : (!empty($row['type']) ? $row['type'] : 'income');

        if (empty($id) && !empty($row['category_name'])) {
            $id = $this->getCategoryIdFromName($row, $type);
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getCategoryType($type)
    {
        return array_key_exists($type, config('type.category')) ? $type : 'other';
    }

    public function getContactId($row, $type = null)
    {
        $id = isset($row['contact_id']) ? $row['contact_id'] : null;

        $type = !empty($type) ? $type : (!empty($row['type']) ? (($row['type'] == 'income') ? 'customer' : 'vendor') : 'customer');

        if (empty($row['contact_id']) && !empty($row['contact_email'])) {
            $id = $this->getContactIdFromEmail($row, $type);
        }

        if (empty($id) && !empty($row['contact_name'])) {
            $id = $this->getContactIdFromName($row, $type);
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getCurrencyCode($row)
    {
        $currency = Currency::where('code', $row['currency_code'])->first();

        if (!empty($currency)) {
            return $currency->code;
        }

        $data = [
            'company_id'    => company_id(),
            'code'          => $row['currency_code'],
            'name'          => isset($row['currency_name']) ? $row['currency_name'] : currency($row['currency_code'])->getName(),
            'rate'          => isset($row['currency_rate']) ? $row['currency_rate'] : 1,
            'symbol'        => isset($row['currency_symbol']) ? $row['currency_symbol'] : currency($row['currency_code'])->getSymbol(),
            'precision'     => isset($row['currency_precision']) ? $row['currency_precision'] : currency($row['currency_code'])->getPrecision(),
            'decimal_mark'  => isset($row['currency_decimal_mark']) ? $row['currency_decimal_mark'] : currency($row['currency_code'])->getDecimalMark(),
            'created_from'  => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'    => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new CurrencyRequest)->rules());

        $currency = $this->dispatch(new CreateCurrency($data));

        return $currency->code;
    }

    public function getCreatedById($row)
    {
        if (empty($row['created_by'])) {
            return $this->user?->id;
        }

        $user = user_model_class()::where('email', $row['created_by'])->first();

        if (! empty($user)) {
            return $user->id;
        }

        return $this->user->id;
    }

    public function getDocumentId($row)
    {
        $id = isset($row['document_id']) ? $row['document_id'] : null;

        if (empty($id) && !empty($row['document_number'])) {
            $id = Document::number($row['document_number'])->pluck('id')->first();
        }

        if (empty($id) && !empty($row['invoice_number'])) {
            $id = Document::invoice()->number($row['invoice_number'])->pluck('id')->first();
        }

        if (empty($id) && !empty($row['bill_number'])) {
            $id = Document::bill()->number($row['bill_number'])->pluck('id')->first();
        }

        if (empty($id) && !empty($row['invoice_bill_number'])) {
            if ($row['type'] == 'income') {
                $id = Document::invoice()->number($row['invoice_bill_number'])->pluck('id')->first();
            } else {
                $id = Document::bill()->number($row['invoice_bill_number'])->pluck('id')->first();
            }
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getParentId($row)
    {
        $id = isset($row['parent_id']) ? $row['parent_id'] : null;

        if (empty($row['parent_number']) && empty($row['parent_name'])){
            return null;
        }

        if (empty($id) && (!empty($row['document_number']) || !empty($row['invoice_number']) || !empty($row['bill_number']))) {
            $id = Document::number($row['parent_number'])->pluck('id')->first();
        }

        if (empty($id) && isset($row['number'])) {
            $id = Transaction::number($row['parent_number'])->pluck('id')->first();
        }

        if (empty($id) && isset($row['parent_name'])) {
            $id = Category::type($row['type'])->withSubCategory()->where('name', $row['parent_name'])->pluck('id')->first();
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getItemId($row, $type = null)
    {
        $id = isset($row['item_id']) ? $row['item_id'] : null;

        $type = !empty($type) ? $type : (!empty($row['item_type']) ? $row['item_type'] : 'product');

        if (empty($id) && !empty($row['item_name'])) {
            $id = $this->getItemIdFromName($row, $type);
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getTaxId($row)
    {
        $id = isset($row['tax_id']) ? $row['tax_id'] : null;

        if (empty($id) && !empty($row['tax_name'])) {
            $id = Tax::name($row['tax_name'])->pluck('id')->first();
        }

        if (empty($id) && !empty($row['tax_rate'])) {
            $id = $this->getTaxIdFromRate($row);
        }

        return is_null($id) ? $id : (int) $id;
    }

    public function getAccountIdFromCurrency($row)
    {
        $account_id = Account::where('currency_code', $row['currency_code'])->pluck('id')->first();

        if (!empty($account_id)) {
            return $account_id;
        }

        $data = [
            'company_id'        => company_id(),
            'type'              => !empty($row['account_type']) ? $row['account_type'] : 'bank',
            'currency_code'     => $row['currency_code'],
            'name'              => !empty($row['account_name']) ? $row['account_name'] : $row['currency_code'],
            'number'            => !empty($row['account_number']) ? $row['account_number'] : (string) rand(1, 10000),
            'opening_balance'   => !empty($row['opening_balance']) ? $row['opening_balance'] : 0,
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new AccountRequest)->rules());

        $account = $this->dispatch(new CreateAccount($data));

        return $account->id;
    }

    public function getAccountIdFromName($row)
    {
        $account_id = Account::where('name', $row['account_name'])->pluck('id')->first();

        if (!empty($account_id)) {
            return $account_id;
        }

        $data = [
            'company_id'        => company_id(),
            'type'              => !empty($row['account_type']) ? $row['account_type'] : 'bank',
            'name'              => $row['account_name'],
            'number'            => !empty($row['account_number']) ? $row['account_number'] : (string) rand(1, 10000),
            'currency_code'     => !empty($row['currency_code']) ? $row['currency_code'] : default_currency(),
            'opening_balance'   => !empty($row['opening_balance']) ? $row['opening_balance'] : 0,
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new AccountRequest)->rules());

        $account = $this->dispatch(new CreateAccount($data));

        return $account->id;
    }

    public function getAccountIdFromNumber($row)
    {
        $account_id = Account::where('account_number', $row['account_number'])->pluck('id')->first();

        if (!empty($account_id)) {
            return $account_id;
        }

        $data = [
            'company_id'        => company_id(),
            'type'              => !empty($row['account_type']) ? $row['account_type'] : 'bank',
            'number'            => $row['account_number'],
            'name'              => !empty($row['account_name']) ? $row['account_name'] : $row['account_number'],
            'currency_code'     => !empty($row['currency_code']) ? $row['currency_code'] : default_currency(),
            'opening_balance'   => !empty($row['opening_balance']) ? $row['opening_balance'] : 0,
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new AccountRequest)->rules());

        $account = $this->dispatch(new CreateAccount($data));

        return $account->id;
    }

    public function getCategoryIdFromName($row, $type)
    {
        $category_id = Category::type($type)->withSubCategory()->where('name', $row['category_name'])->pluck('id')->first();

        if (!empty($category_id)) {
            return $category_id;
        }

        $data = [
            'company_id'        => company_id(),
            'name'              => $row['category_name'],
            'type'              => $type,
            'color'             => !empty($row['category_color']) ? $row['category_color'] : '#' . dechex(rand(0x000000, 0xFFFFFF)),
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new CategoryRequest)->rules());

        $category = $this->dispatch(new CreateCategory($data));

        return $category->id;
    }

    public function getContactIdFromEmail($row, $type)
    {
        $contact_id = Contact::type($type)->where('email', $row['contact_email'])->pluck('id')->first();

        if (!empty($contact_id)) {
            return $contact_id;
        }

        $data = [
            'company_id'        => company_id(),
            'email'             => $row['contact_email'],
            'type'              => $type,
            'name'              => !empty($row['contact_name']) ? $row['contact_name'] : $row['contact_email'],
            'currency_code'     => !empty($row['contact_currency']) ? $row['contact_currency'] : default_currency(),
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new ContactRequest)->rules());

        $contact = $this->dispatch(new CreateContact($data));

        return $contact->id;
    }

    public function getContactIdFromName($row, $type)
    {
        $contact_id = Contact::type($type)->where('name', $row['contact_name'])->pluck('id')->first();

        if (!empty($contact_id)) {
            return $contact_id;
        }

        $data = [
            'company_id'        => company_id(),
            'name'              => $row['contact_name'],
            'type'              => $type,
            'email'             => !empty($row['contact_email']) ? $row['contact_email'] : null,
            'currency_code'     => !empty($row['contact_currency']) ? $row['contact_currency'] : default_currency(),
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new ContactRequest)->rules());

        $contact = $this->dispatch(new CreateContact($data));

        return $contact->id;
    }

    public function getItemIdFromName($row, $type = null)
    {
        $type = !empty($type) ? $type : (!empty($row['item_type']) ? $row['item_type'] : 'product');

        $item_id = Item::type($type)->where('name', $row['item_name'])->pluck('id')->first();

        if (!empty($item_id)) {
            return $item_id;
        }

        $data = [
            'company_id'        => company_id(),
            'type'              => $type,
            'name'              => $row['item_name'],
            'description'       => !empty($row['item_description']) ? $row['item_description'] : null,
            'sale_price'        => !empty($row['sale_price']) ? $row['sale_price'] : (!empty($row['price']) ? $row['price'] : 0),
            'purchase_price'    => !empty($row['purchase_price']) ? $row['purchase_price'] : (!empty($row['price']) ? $row['price'] : 0),
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new ItemRequest())->rules());

        $item = $this->dispatch(new CreateItem($data));

        return $item->id;
    }

    public function getTaxIdFromRate($row, $type = 'normal')
    {
        $tax_id = Tax::type($type)->where('rate', $row['tax_rate'])->pluck('id')->first();

        if (!empty($tax_id)) {
            return $tax_id;
        }

        $data = [
            'company_id'        => company_id(),
            'rate'              => $row['tax_rate'],
            'type'              => $type,
            'name'              => !empty($row['tax_name']) ? $row['tax_name'] : (string) $row['tax_rate'],
            'enabled'           => 1,
            'created_from'      => !empty($row['created_from']) ? $row['created_from'] : $this->getSourcePrefix() . 'import',
            'created_by'        => !empty($row['created_by']) ? $row['created_by'] : user()?->id,
        ];

        Validator::validate($data, (new TaxRequest())->rules());

        $tax = $this->dispatch(new CreateTax($data));

        return $tax->id;
    }
}
