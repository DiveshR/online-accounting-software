<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invalid search string handling
    |--------------------------------------------------------------------------
    |
    | - all-results: (Default) Silently fail with a query containing everything.
    | - no-results: Silently fail with a query containing nothing.
    | - exceptions: Throw an `InvalidSearchStringException`.
    |
    */

    'fail' => 'all-results',

    /*
    |--------------------------------------------------------------------------
    | Default options
    |--------------------------------------------------------------------------
    |
    | When options are missing from your models, this array will be used
    | to fill the gaps. You can also define a set of options specific
    | to a model, using its class name as a key, e.g. 'App\User'.
    |
    */

    'default' => [
        'keywords' => [
            'order_by' => 'sort',
            'select' => 'fields',
            'limit' => 'limit',
            'offset' => 'page',
        ],
        'columns' => [
            'created_at' => 'date',
            'updated_at' => 'date',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Default operators
    |--------------------------------------------------------------------------
    |
    | When options are missing from your models, this array will be used
    | to fill the gaps. You can also define a set of options specific
    | to a model, using its class name as a key, e.g. 'App\User'.
    |
    */

    /*
    'operators' => [
        'equal' => [
            'enabled'   => true,

            'symbol'    => [
                'sign'  => '=',
                'icon'  => 'drag_handle',
            ],

            'text'      => trans(''),
        ],

        'not_equal' => [
            'enabled'   => true,

            'symbol'    => [
                'sign'  => '=',
                'img'   => 'drag_handle',
            ],

            'text'      => trans(''),
        ],

        'range' => [
            'enabled'   => false,

            'symbol'    => [
                'sign'  => '><',
                'class' => 'transform rotate-90',
                'icon'  => 'height',
            ],

            'text'      => trans(''),
        ],
    ],
    */

    App\Models\Auth\Permission::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'display_name' => ['searchable' => true],
            'description' => ['searchable' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Auth\Role::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'display_name' => ['searchable' => true],
            'description' => ['searchable' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Auth\User::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'email' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'last_logged_in_at' => ['date' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Banking\Account::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'number' => ['searchable' => true],
            'bank_name' => ['searchable' => true],
            'bank_address' => ['searchable' => true],
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Banking\Reconciliation::class => [
        'columns' => [
            'id',
            'account' => ['relationship' => true],
            'closing_balance',
            'reconciled' => ['boolean' => true],
            'started_at' => ['date' => true],
            'ended_at' => ['date' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Banking\Transaction::class => [
        'columns' => [
            'id',
            'number',
            'type' => [
                'values' => [
                    'income' => 'general.incomes',
                    'expense' => 'general.expenses',
                    //'income-transfer' => 'general.income_transfers',
                    //'expense-transfer' => 'general.expense_transfers',
                ],
            ],
            'account_id' => [
                'route' => ['accounts.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'paid_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'document_id',
            'contact_id' => [
                'route' => 'contacts.index',
                'multiple' => true,
            ],
            'description' => ['searchable' => true],
            'payment_method',
            'reference',
            'category_id' => [
                'route' => ['categories.index', 'search=type:income,expense enabled:1'],
                'fields' => [
                    'key' => 'id',
                    'value' => 'display_name',
                ],
                'multiple' => true,
            ],
            'parent_id',
            'recurring' => [
                'key' => 'recurring',
                'foreign_key' => '',
                'relationship' => true,
                'boolean' => true,
            ],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Banking\Transfer::class => [
        'columns' => [
            'id',
            'expense_account' => [
                'relationship' => true,
                'route' => ['accounts.index', 'search=enabled:1'],
            ],
            'income_account' => [
                'relationship' => true,
                'route' => ['accounts.index', 'search=enabled:1'],
            ],
            'created_at' => ['date' => true],
        ],
    ],

    App\Models\Common\Company::class => [
        'columns' => [
            'id',
            'domain' => ['searchable' => true],
            'settings.value' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Common\Dashboard::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Common\Item::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'description' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'category_id' => [
                'route' => ['categories.index', 'search=type:item enabled:1'],
                'fields' => [
                    'key' => 'id',
                    'value' => 'name',
                ],
                'multiple' => true,
            ],
            'sale_price',
            'purchase_price',
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Common\Contact::class => [
        'columns' => [
            'id',
            'type',
            'name' => ['searchable' => true],
            'email' => ['searchable' => true],
            'tax_number' => ['searchable' => true],
            'phone' => ['searchable' => true],
            'address' => ['searchable' => true],
            'website' => ['searchable' => true],
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'reference',
            'user_id',
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    'App\Models\Purchase\Vendor' => [
        'columns' => [
            'id',
            'type',
            'name' => ['searchable' => true],
            'email' => ['searchable' => true],
            'tax_number' => ['searchable' => true],
            'phone' => ['searchable' => true],
            'address' => ['searchable' => true],
            'website' => ['searchable' => true],
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'reference',
            'user_id',
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    'App\Models\Sale\Customer' => [
        'columns' => [
            'id',
            'type',
            'name' => ['searchable' => true],
            'email' => ['searchable' => true],
            'tax_number' => ['searchable' => true],
            'phone' => ['searchable' => true],
            'address' => ['searchable' => true],
            'website' => ['searchable' => true],
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'reference',
            'user_id',
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Document\Document::class => [
        'columns' => [
            'id',
            'type' => ['searchable' => true],
            'document_number' => ['searchable' => true],
            'order_number' => ['searchable' => true],
            'status' => [
                'multiple' => true,
            ],
            'issued_at' => [
                'key' => '/^(invoiced_at|billed_at)$/',
                'date' => true,
            ],
            'due_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'contact_id',
            'contact_name' => ['searchable' => true],
            'contact_email' => ['searchable' => true],
            'contact_tax_number',
            'contact_phone' => ['searchable' => true],
            'contact_address' => ['searchable' => true],
            'category_id' => [
                'route' => ['categories.index', 'search=type:income,expense enabled:1'],
                'multiple' => true,
            ],
            'parent_id',
            'recurring' => [
                'key' => 'recurring',
                'relationship' => true,
                'boolean' => true,
            ],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    'App\Models\Purchase\Bill' => [
        'columns' => [
            'id',
            'document_number' => ['searchable' => true],
            'order_number' => ['searchable' => true],
            'status' => [
                'values' => [
                    'received,partial' => 'documents.statuses.unpaid',
                    'paid' => 'documents.statuses.paid',
                    'partial' => 'documents.statuses.partial',
                    'received' => 'documents.statuses.received',
                    'cancelled' => 'documents.statuses.cancelled',
                    'draft' => 'documents.statuses.draft',
                    'partial,received due_at<=today' => 'documents.statuses.overdue',
                ],
                'multiple' => true,
            ],
            'issued_at' => [
                'key' => 'billed_at',
                'date' => true,
            ],
            'due_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'contact_id' => [
                'route' => ['vendors.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'contact_name' => ['searchable' => true],
            'contact_email' => ['searchable' => true],
            'contact_tax_number',
            'contact_phone' => ['searchable' => true],
            'contact_address' => ['searchable' => true],
            'category_id' => [
                'route' => ['categories.index', 'search=type:expense enabled:1'],
                'fields' => [
                    'key' => 'id',
                    'value' => 'name',
                ],
                'multiple' => true,
            ],
            'parent_id',
            'recurring' => [
                'key' => 'recurring',
                'relationship' => true,
                'boolean' => true,
            ],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    'App\Models\Sale\Invoice' => [
        'columns' => [
            'id',
            'document_number' => ['searchable' => true],
            'order_number' => ['searchable' => true],
            'status' => [
                'values' => [
                    'sent,viewed,partial' => 'documents.statuses.unpaid',
                    'paid' => 'documents.statuses.paid',
                    'partial' => 'documents.statuses.partial',
                    'sent' => 'documents.statuses.sent',
                    'viewed' => 'documents.statuses.viewed',
                    'cancelled' => 'documents.statuses.cancelled',
                    'draft' => 'documents.statuses.draft',
                    'partial,sent,viewed due_at<=today' => 'documents.statuses.overdue',
                ],
                'multiple' => true,
            ],
            'issued_at' => [
                'key' => 'invoiced_at',
                'date' => true,
            ],
            'due_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => ['currencies.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'contact_id' => [
                'route' => ['customers.index', 'search=enabled:1'],
                'multiple' => true,
            ],
            'contact_name' => ['searchable' => true],
            'contact_email' => ['searchable' => true],
            'contact_tax_number',
            'contact_phone' => ['searchable' => true],
            'contact_address' => ['searchable' => true],
            'category_id' => [
                'route' => ['categories.index', 'search=type:income enabled:1'],
                'fields' => [
                    'key' => 'id',
                    'value' => 'name',
                ],
                'multiple' => true,
            ],
            'parent_id',
            'recurring' => [
                'key' => 'recurring',
                'relationship' => true,
                'boolean' => true,
            ],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Setting\Category::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'type' => [
                'values' => [
                    'income' => 'general.incomes',
                    'expense' => 'general.expenses',
                    'item' => 'general.items',
                    'other' => 'general.others',
                ],
                'multiple' => true,
            ],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Setting\Currency::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'code' => ['searchable' => true],
            'rate' => ['searchable' => true],
            'enabled' => ['boolean' => true],
            'precision',
            'symbol',
            'symbol_first' => [
                'boolean' => true,
                'translation' => [
                    0 => 'currencies.symbol.after',
                    1 => 'currencies.symbol.before',
                ]
            ],
            'decimal_mark',
            'thousands_separator',
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Setting\EmailTemplate::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'subject' => ['searchable' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Setting\Tax::class => [
        'columns' => [
            'id',
            'name' => ['searchable' => true],
            'type' => [
                'multiple' => true,
            ],
            'rate',
            'enabled' => ['boolean' => true],
            'created_at' => ['date' => true],
            'updated_at' => ['date' => true],
        ],
    ],

    App\Models\Portal\Sale\Invoice::class => [
        'columns' => [
            'id',
            'document_number' => ['searchable' => true],
            'order_number' => ['searchable' => true],
            'status' => [
                'multiple' => true,
            ],
            'issued_at' => [
                'key' => 'invoiced_at',
                'date' => true,
            ],
            'due_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => 'portal.payment.currencies',
                'multiple' => true,
            ],
            'parent_id',
        ],
    ],

    App\Models\Portal\Banking\Transaction::class => [
        'columns' => [
            'id',
            'paid_at' => ['date' => true],
            'amount',
            'currency_code' => [
                'route' => 'portal.payment.currencies',
                'multiple' => true,
            ],
            'document_id',
            'description' => ['searchable' => true],
            'payment_method',
            'reference',
            'parent_id',
        ],
    ],

];
