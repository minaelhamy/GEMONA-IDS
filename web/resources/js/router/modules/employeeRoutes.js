const EmployeeComponent = () => import("../../components/admin/employees/EmployeeComponent");
const EmployeeListComponent = () => import("../../components/admin/employees/EmployeeListComponent");
const EmployeeShowComponent = () => import("../../components/admin/employees/EmployeeShowComponent");
const EmployeeOrderDetailsComponent = () => import("../../components/admin/employees/EmployeeOrderDetailsComponent");

export default [
    {
        path: "/admin/employees",
        component: EmployeeComponent,
        name: "admin.employees",
        redirect: {name: "admin.employees.list"},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "employees",
            breadcrumb: "employees",
        },
        children: [
            {
                path: "",
                component: EmployeeListComponent,
                name: "admin.employees.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: "",
                },
            },
            {
                path: "show/:id",
                component: EmployeeShowComponent,
                name: "admin.employees.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: "view",
                },
            },
            {
                path: "show/:id/:orderId",
                component: EmployeeOrderDetailsComponent,
                name: "admin.employees.order.details",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "employees",
                    breadcrumb: "order_details",
                },
            },
        ],
    },
];
