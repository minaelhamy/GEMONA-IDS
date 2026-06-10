const ProductComponent = ()=> import("../../components/admin/products/ProductComponent");
const ProductListComponent = ()=> import("../../components/admin/products/ProductListComponent");
const ProductShowComponent = ()=> import("../../components/admin/products/ProductShowComponent");

export default [
    {
        path: '/admin/products',
        component: ProductComponent,
        name: 'admin.products',
        redirect: { name: 'admin.products.list' },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'products',
            breadcrumb: 'products'
        },
        children: [
            {
                path: '',
                component: ProductListComponent,
                name: 'admin.products.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'products',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ProductShowComponent,
                name: "admin.product.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "products",
                    breadcrumb: "view",
                },
            }
        ]
    }
]
