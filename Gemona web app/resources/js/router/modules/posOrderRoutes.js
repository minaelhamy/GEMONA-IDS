const PosOrderComponent = () => import("../../components/admin/posOrders/PosOrderComponent");
const PosOrderListComponent = () => import("../../components/admin/posOrders/PosOrderListComponent");
const PosOrderShowComponent = () => import("../../components/admin/posOrders/PosOrderShowComponent");

export default [
    {
        path: "/admin/pos-orders",
        component: PosOrderComponent,
        name: "admin.pos.orders",
        redirect: { name: "admin.pos.orders.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'pos-orders',
            breadcrumb: 'pos_orders'
        },
        children: [
            {
                path: "",
                component: PosOrderListComponent,
                name: "admin.pos.orders.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "pos-orders",
                    breadcrumb: "",
                },
            },
            {
                path: "show/:id",
                component: PosOrderShowComponent,
                name: "admin.pos.orders.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "pos-orders",
                    breadcrumb: "view",
                },
            }
        ],
    },
];
