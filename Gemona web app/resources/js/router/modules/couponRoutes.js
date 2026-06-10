const  CouponComponent = () => import("../../components/admin/coupons/CouponComponent");
const  CouponListComponent = () => import("../../components/admin/coupons/CouponListComponent");
const  CouponShowComponent = () => import("../../components/admin/coupons/CouponShowComponent");

export default [
    {
        path: "/admin/coupons",
        component: CouponComponent,
        name: "admin.coupons",
        redirect: { name: "admin.coupons.list" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "coupons",
            breadcrumb: "coupons",
        },
        children: [
            {
                path: "",
                component: CouponListComponent,
                name: "admin.coupons.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "coupons",
                    breadcrumb: "",
                },
            },
            {
                path: "show/:id",
                component: CouponShowComponent,
                name: "admin.coupon.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "coupons",
                    breadcrumb: "view",
                },
            },
        ],
    },
];
