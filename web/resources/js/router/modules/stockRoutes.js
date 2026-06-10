const StockComponent = () => import("../../components/admin/stock/StockComponent");
const StockListComponent = () => import("../../components/admin/stock/StockListComponent");

export default [
    {
        path: '/admin/stock',
        component: StockComponent,
        name: 'admin.stock',
        redirect: { name: 'admin.stock.list' },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'stock',
            breadcrumb: 'stock'
        },
        children: [
            {
                path: '',
                component: StockListComponent,
                name: 'admin.stock.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'stock',
                    breadcrumb: ''
                },
            }
        ]
    }
]
