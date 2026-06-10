const ProductsReportComponent = () =>   import("../../components/admin/productsReport/ProductsReportComponent");
const ProductsReportListComponent = () =>   import("../../components/admin/productsReport/ProductsReportListComponent");
export default [
    {
        path: "/admin/products-report",
        component: ProductsReportComponent,
        name: "admin.products-report",
        redirect: { name: "admin.products-report.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "products-report",
            breadcrumb: "products_report",
        },
        children: [
            {
                path: "",
                component: ProductsReportListComponent,
                name: "admin.products-report.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "products-report",
                    breadcrumb: "",
                },
            },
        ],
    },
];
