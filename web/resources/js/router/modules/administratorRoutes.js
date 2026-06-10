const AdministratorComponent = () => import("../../components/admin/administrators/AdministratorComponent");
const AdministratorListComponent = () => import("../../components/admin/administrators/AdministratorListComponent");
const AdministratorShowComponent = () => import("../../components/admin/administrators/AdministratorShowComponent");
const AdministratorOrderDetailsComponent = () =>     import("../../components/admin/administrators/AdministratorOrderDetailsComponent");

export default [
    {
        path: "/admin/administrators",
        component: AdministratorComponent,
        name: "admin.administrators",
        redirect: { name: "admin.administrators.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "administrators",
            breadcrumb: "administrators",
        },
        children: [
            {
                path: "",
                component: AdministratorListComponent,
                name: "admin.administrators.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: "",
                }
            },
            {
                path: "show/:id",
                component: AdministratorShowComponent,
                name: "admin.administrators.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: "view",
                }
            },
            {
                path: "show/:id/:orderId",
                component: AdministratorOrderDetailsComponent,
                name: "admin.administrators.order.details",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "administrators",
                    breadcrumb: "order_details",
                },
            },
        ],
    },
];
