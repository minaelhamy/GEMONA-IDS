
const PurchaseComponent = () => import('../../components/admin/purchase/PurchaseComponent');
const PurchaseListComponent = () => import('../../components/admin/purchase/PurchaseListComponent');
const PurchaseShowComponent = () => import('../../components/admin/purchase/PurchaseShowComponent');
const PurchaseCreateAndEditComponent = () => import('../../components/admin/purchase/PurchaseCreateAndEditComponent.vue');

export default [
    {
        path:'/admin/purchase',
        component: PurchaseComponent,
        name: 'admin.purchase',
        redirect: {name: 'admin.purchase.list'},
        meta: {
            isFrontend:false,
            auth:true,
            permissionUrl: 'purchase',
            breadcrumb:'purchase'
        },
        children: [
            {
                path:'',
                component: PurchaseListComponent,
                name: 'admin.purchase.list',
                meta: {
                    isFrontend:false,
                    auth:true,
                    permissionUrl: 'purchase',
                    breadcrumb: ''
                }

            },
            {
                path: 'add',
                component: PurchaseCreateAndEditComponent,
                name: 'admin.purchase.create',
                meta: {
                    isFrontend:false,
                    auth:true,
                    permissionUrl: 'purchase_create',
                    breadcrumb: 'create'
                }
            },
            {
                path: 'edit/:id',
                component: PurchaseCreateAndEditComponent,
                name: 'admin.purchase.edit',
                meta: {
                    isFrontend:false,
                    auth:true,
                    permissionUrl: 'purchase_edit',
                    breadcrumb: 'edit'
                }
            },
            {
                path: 'show/:id',
                component: PurchaseShowComponent,
                name: 'admin.purchase.show',
                meta: {
                    isFrontend:false,
                    auth:true,
                    permissionUrl: 'purchase_show',
                    breadcrumb: 'view'
                }
            }
        ]
    }
]
