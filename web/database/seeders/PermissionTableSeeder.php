<?php

namespace Database\Seeders;

use App\Libraries\AppLibrary;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'title'      => 'Dashboard',
                'name'       => 'dashboard',
                'guard_name' => 'sanctum',
                'url'        => 'dashboard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Products',
                'name'       => 'products',
                'guard_name' => 'sanctum',
                'url'        => 'products',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Products Create',
                        'name'       => 'products_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'products/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Products Edit',
                        'name'       => 'products_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'products/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Products Delete',
                        'name'       => 'products_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'products/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Products Show',
                        'name'       => 'products_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'products/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Purchase',
                'name'       => 'purchase',
                'guard_name' => 'sanctum',
                'url'        => 'purchase',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Purchase Create',
                        'name'       => 'purchase_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'purchase/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Purchase Edit',
                        'name'       => 'purchase_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'purchase/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Purchase Delete',
                        'name'       => 'purchase_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'purchase/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Purchase Show',
                        'name'       => 'purchase_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'purchase/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Damages',
                'name'       => 'damages',
                'guard_name' => 'sanctum',
                'url'        => 'damages',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Damage Create',
                        'name'       => 'damage_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'damages/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Damage Edit',
                        'name'       => 'damage_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'damages/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Damage Delete',
                        'name'       => 'damage_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'damages/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Damage Show',
                        'name'       => 'damage_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'damages/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Stock',
                'name'       => 'stock',
                'guard_name' => 'sanctum',
                'url'        => 'stock',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Reviews',
                'name'       => 'reviews',
                'guard_name' => 'sanctum',
                'url'        => 'reviews',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'POS',
                'name'       => 'pos',
                'guard_name' => 'sanctum',
                'url'        => 'pos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'POS Orders',
                'name'       => 'pos-orders',
                'guard_name' => 'sanctum',
                'url'        => 'pos-orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Online Orders',
                'name'       => 'online-orders',
                'guard_name' => 'sanctum',
                'url'        => 'online-orders',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Return And Refunds',
                'name'       => 'return-and-refunds',
                'guard_name' => 'sanctum',
                'url'        => 'return-and-refunds',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Return Orders',
                'name'       => 'return-orders',
                'guard_name' => 'sanctum',
                'url'        => 'return-orders',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Return Order Create',
                        'name'       => 'return_order_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'return-orders/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Return Order Edit',
                        'name'       => 'return_order_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'return-orders/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Return Order Delete',
                        'name'       => 'return_order_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'return-orders/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Return Order Show',
                        'name'       => 'return_order_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'return-orders/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Coupons',
                'name'       => 'coupons',
                'guard_name' => 'sanctum',
                'url'        => 'coupons',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Coupons Create',
                        'name'       => 'coupons_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'coupons/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Coupons Edit',
                        'name'       => 'coupons_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'coupons/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Coupons Delete',
                        'name'       => 'coupons_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'coupons/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Coupons Show',
                        'name'       => 'coupons_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'coupons/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Promotions',
                'name'       => 'promotions',
                'guard_name' => 'sanctum',
                'url'        => 'promotions',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Promotions Create',
                        'name'       => 'promotions_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'promotions/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Promotions Edit',
                        'name'       => 'promotions_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'promotions/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Promotions Delete',
                        'name'       => 'promotions_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'promotions/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Promotions Show',
                        'name'       => 'promotions_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'promotions/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Product Sections',
                'name'       => 'product-sections',
                'guard_name' => 'sanctum',
                'url'        => 'product-sections',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Product Sections Create',
                        'name'       => 'product-sections_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'product-sections/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Product Sections Edit',
                        'name'       => 'product-sections_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'product-sections/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Product Sections Delete',
                        'name'       => 'product-sections_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'product-sections/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Product Sections Show',
                        'name'       => 'product-sections_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'product-sections/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Push Notifications',
                'name'       => 'push-notifications',
                'guard_name' => 'sanctum',
                'url'        => 'push-notifications',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Push Notifications Create',
                        'name'       => 'push-notifications_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'push-notifications/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Push Notifications Edit',
                        'name'       => 'push-notifications_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'push-notifications/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Push Notifications Delete',
                        'name'       => 'push-notifications_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'push-notifications/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Push Notifications Show',
                        'name'       => 'push-notifications_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'push-notifications/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Subscribers',
                'name'       => 'subscribers',
                'guard_name' => 'sanctum',
                'url'        => 'subscribers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Administrators',
                'name'       => 'administrators',
                'guard_name' => 'sanctum',
                'url'        => 'administrators',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Administrators Create',
                        'name'       => 'administrators_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'administrators/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Administrators Edit',
                        'name'       => 'administrators_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'administrators/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Administrators Delete',
                        'name'       => 'administrators_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'administrators/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Administrators Show',
                        'name'       => 'administrators_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'administrators/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Customers',
                'name'       => 'customers',
                'guard_name' => 'sanctum',
                'url'        => 'customers',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Customers Create',
                        'name'       => 'customers_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'customers/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Customers Edit',
                        'name'       => 'customers_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'customers/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Customers Delete',
                        'name'       => 'customers_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'customers/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Customers Show',
                        'name'       => 'customers_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'customers/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Employees',
                'name'       => 'employees',
                'guard_name' => 'sanctum',
                'url'        => 'employees',
                'created_at' => now(),
                'updated_at' => now(),
                'children'   => [
                    [
                        'title'      => 'Employees Create',
                        'name'       => 'employees_create',
                        'guard_name' => 'sanctum',
                        'url'        => 'employees/create',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Employees Edit',
                        'name'       => 'employees_edit',
                        'guard_name' => 'sanctum',
                        'url'        => 'employees/edit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Employees Delete',
                        'name'       => 'employees_delete',
                        'guard_name' => 'sanctum',
                        'url'        => 'employees/delete',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'title'      => 'Employees Show',
                        'name'       => 'employees_show',
                        'guard_name' => 'sanctum',
                        'url'        => 'employees/show',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]
            ],
            [
                'title'      => 'Transactions',
                'name'       => 'transactions',
                'guard_name' => 'sanctum',
                'url'        => 'transactions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Sales Report',
                'name'       => 'sales-report',
                'guard_name' => 'sanctum',
                'url'        => 'sales-report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Products Report',
                'name'       => 'products-report',
                'guard_name' => 'sanctum',
                'url'        => 'products-report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Credit Balance Report',
                'name'       => 'credit-balance-report',
                'guard_name' => 'sanctum',
                'url'        => 'credit-balance-report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'AI Assistant',
                'name'       => 'ai-assistant',
                'guard_name' => 'sanctum',
                'url'        => 'ai-assistant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'Settings',
                'name'       => 'settings',
                'guard_name' => 'sanctum',
                'url'        => 'settings',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $permissions = AppLibrary::associativeToNumericArrayBuilder($permissions);
        Permission::insert($permissions);
    }
}
