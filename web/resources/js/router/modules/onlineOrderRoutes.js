const OnlineOrderComponent = () => import("../../components/admin/onlineOrders/OnlineOrderComponent");
const OnlineOrderListComponent = () => import("../../components/admin/onlineOrders/OnlineOrderListComponent");
const OnlineOrderShowComponent = () => import("../../components/admin/onlineOrders/OnlineOrderShowComponent");

export default [
    {
        path: '/admin/online-orders',
        component: OnlineOrderComponent,
        name: 'admin.order',
        redirect: {name: 'admin.order.list'},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'online-orders',
            breadcrumb: 'online_orders'
        },
        children: [
            {
                path: '',
                component: OnlineOrderListComponent,
                name: 'admin.order.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'online-orders',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: OnlineOrderShowComponent,
                name: "admin.order.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "online-orders",
                    breadcrumb: "view",
                },
            }
        ]
    }
]
