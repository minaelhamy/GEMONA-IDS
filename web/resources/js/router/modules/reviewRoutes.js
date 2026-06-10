const ReviewComponent = () =>
    import("../../components/admin/reviews/ReviewComponent");
const ReviewListComponent = () =>
    import("../../components/admin/reviews/ReviewListComponent");
const ReviewShowComponent = () =>
    import("../../components/admin/reviews/ReviewShowComponent");

export default [
    {
        path: "/admin/reviews",
        component: ReviewComponent,
        name: "admin.review",
        redirect: { name: "admin.review" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "reviews",
            breadcrumb: "reviews",
        },
        children: [
            {
                path: "",
                component: ReviewListComponent,
                name: "admin.review.list",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "reviews",
                    breadcrumb: "",
                },
            },
            {
                path: "show/:id",
                component: ReviewShowComponent,
                name: "admin.review.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "reviews",
                    breadcrumb: "view",
                },
            },
        ],
    },
];
