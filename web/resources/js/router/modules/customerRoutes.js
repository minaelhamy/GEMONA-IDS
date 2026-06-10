const CustomerComponent = () => import("../../components/admin/customers/CustomerComponent");
const CustomerListComponent = () => import("../../components/admin/customers/CustomerListComponent");
const CustomerShowComponent = () => import("../../components/admin/customers/CustomerShowComponent");
const CustomerOrderDetailsComponent = () => import("../../components/admin/customers/CustomerOrderDetailsComponent");


export default [
    {
        path: "/admin/customers",
        component: CustomerComponent,
        name: "admin.customers",
        redirect: {name: "admin.customers.list"},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "customers",
            breadcrumb: "customers",
        },
        children: [
            {
                path: "",
                component: CustomerListComponent,
                name: "admin.customers.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: "",
                }
            },
            {
                path: "show/:id",
                component: CustomerShowComponent,
                name: "admin.customers.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: "view",
                }
            },
            {
                path: "show/:id/:orderId",
                component: CustomerOrderDetailsComponent,
                name: "admin.customers.order.details",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "customers",
                    breadcrumb: "order_details",
                }
            },
        ],
    },
];
