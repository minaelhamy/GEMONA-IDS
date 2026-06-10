const ReturnAndRefundComponent = () =>  import("../../components/admin/returnAndRefunds/ReturnAndRefundComponent");
const ReturnAndRefundListComponent = () =>  import("../../components/admin/returnAndRefunds/ReturnAndRefundListComponent");
const ReturnAndRefundShowComponent = () =>  import("../../components/admin/returnAndRefunds/ReturnAndRefundShowComponent");

export default [
    {
        path: '/admin/return-and-refunds',
        component: ReturnAndRefundComponent,
        name: 'admin.returnAndRefund',
        redirect: {name: 'admin.returnAndRefund.list'},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'return-and-refunds',
            breadcrumb: 'return_and_refunds'
        },
        children: [
            {
                path: '',
                component: ReturnAndRefundListComponent,
                name: 'admin.returnAndRefund.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'return-and-refunds',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ReturnAndRefundShowComponent,
                name: "admin.returnAndRefund.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "return-and-refunds",
                    breadcrumb: "view",
                },
            }
        ]
    }
]
