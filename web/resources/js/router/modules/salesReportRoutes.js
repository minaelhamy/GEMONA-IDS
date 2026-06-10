const SalesReportComponent = () => import("../../components/admin/salesReport/SalesReportComponent");
const SalesReportListComponent = () => import("../../components/admin/salesReport/SalesReportListComponent");
export default [
    {
        path: "/admin/sales-report",
        component: SalesReportComponent,
        name: "admin.sales-report",
        redirect: { name: "admin.sales-report.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "sales-report",
            breadcrumb: "sales_report",
        },
        children: [
            {
                path: "",
                component: SalesReportListComponent,
                name: "admin.sales-report.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "sales-report",
                    breadcrumb: "",
                },
            },
        ],
    },
];
