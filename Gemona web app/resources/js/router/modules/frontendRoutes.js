import  HomeComponent from "../../components/frontend/home/HomeComponent";
const  WishlistComponent = () => import("../../components/frontend/wishlist/WishlistComponent");
const  OrderHistoryComponent = () => import("../../components/frontend/account/orderHistory/OrderHistoryComponent");
const  ReturnOrdersComponent = () => import("../../components/frontend/account/returnOrders/ReturnOrdersComponent");
const  ReturnOrderDetailsComponent = () => import("../../components/frontend/account/returnOrders/ReturnOrderDetailsComponent");
const  ReturnOrderRequestComponent = () => import("../../components/frontend/account/returnOrders/ReturnOrderRequestComponent");
const  OrderDetailsComponent = () => import("../../components/frontend/account/orderDetails/OrderDetailsComponent");
const  ChangePasswordComponent = () => import("../../components/frontend/account/changePassword/ChangePasswordComponent");
const  AddressComponent = () => import("../../components/frontend/account/address/AddressComponent");
const  PageComponent = () => import("../../components/frontend/page/PageComponent");
const  ProductComponent = () => import("../../components/frontend/product/ProductComponent");
const  ProductDetailsComponent = () => import("../../components/frontend/product/ProductDetailsComponent");
const  PromotionProductComponent = () => import("../../components/frontend/product/PromotionProductComponent");
const  ProductSectionProductComponent = () => import("../../components/frontend/product/ProductSectionProductComponent");
const  FlashSaleProductComponent = () => import("../../components/frontend/product/FlashSaleProductComponent");
const  OfferProductComponent = () => import("../../components/frontend/product/OfferProductComponent");
const  OverviewComponent = () => import("../../components/frontend/account/overview/OverviewComponent");
const  AccountComponent = () => import("../../components/frontend/account/AccountComponent");
const  AccountInfoComponent = () => import("../../components/frontend/account/accountInfo/AccountInfoComponent");
const  CheckoutComponent = () => import("../../components/frontend/checkout/CheckoutComponent");
const  CheckoutCartListComponent = () => import("../../components/frontend/checkout/cartList/CartListComponent");
const  CartListHeaderComponent = () => import("../../components/frontend/checkout/cartList/HeaderComponent");
const  CheckoutCheckoutComponent = () => import("../../components/frontend/checkout/checkout/CheckoutComponent");
const  CheckoutHeaderComponent = () => import("../../components/frontend/checkout/checkout/HeaderComponent");
const  CheckoutPaymentComponent = () => import("../../components/frontend/checkout/payment/PaymentComponent");
const  PaymentHeaderComponent = () => import("../../components/frontend/checkout/payment/HeaderComponent");
const  ProductReviewComponent = () => import("../../components/frontend/account/review/ProductReviewComponent");
const  MostPopularProductComponent = () => import("../../components/frontend/product/MostPopularProductComponent.vue");

export default [
    {
        path: "/home",
        component: HomeComponent,
        name: "frontend.home",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/product",
        component: ProductComponent,
        name: "frontend.product",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/product/:slug",
        component: ProductDetailsComponent,
        name: "frontend.product.details",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/offers",
        component: OfferProductComponent,
        name: "frontend.offers",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/promotion/:slug",
        component: PromotionProductComponent,
        name: "frontend.promotion.products",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/product-section/:slug",
        component: ProductSectionProductComponent,
        name: "frontend.productSection.products",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/most-popular",
        component: MostPopularProductComponent,
        name: "frontend.mostPopular.products",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },

    {
        path: "/flash-sale",
        component: FlashSaleProductComponent,
        name: "frontend.flashSale.products",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/wishlist",
        component: WishlistComponent,
        name: "frontend.wishlist",
        meta: {
            isFrontend: true,
            auth: true,
        },
    },
    {
        path: "/page/:slug",
        component: PageComponent,
        name: "frontend.page",
        meta: {
            isFrontend: true,
            auth: false,
        },
    },
    {
        path: "/account",
        component: AccountComponent,
        name: "frontend.account",
        redirect: {name: "frontend.account.overview"},
        meta: {
            isFrontend: true,
            auth: true,
        },
        children: [
            {
                path: "overview",
                component: OverviewComponent,
                name: "frontend.account.overview",
                meta: {
                    isFrontend: true,
                    auth: true
                }
            },
            {
                path: "order-history",
                component: OrderHistoryComponent,
                name: "frontend.account.orderHistory",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "return-orders",
                component: ReturnOrdersComponent,
                name: "frontend.account.returnOrders",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "return-order-details/:id",
                component: ReturnOrderDetailsComponent,
                name: "frontend.account.returnOrder.details",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "return-request/:id",
                component: ReturnOrderRequestComponent,
                name: "frontend.account.returnOrder.request",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "write-review/:slug",
                component: ProductReviewComponent,
                name: "frontend.account.productReview",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "edit-review/:slug/:id",
                component: ProductReviewComponent,
                name: "frontend.account.productReview.edit",
                meta: {
                    isFrontend: true,
                    auth: true,
                }
            },
            {
                path: "order-details/:id",
                component: OrderDetailsComponent,
                name: "frontend.account.orderDetails",
                meta: {
                    isFrontend: true,
                    auth: true,
                },
            },
            {
                path: "account-info",
                component: AccountInfoComponent,
                name: "frontend.account.accountInfo",
                meta: {
                    isFrontend: true,
                    auth: true,
                },
            },
            {
                path: "change-password",
                component: ChangePasswordComponent,
                name: "frontend.account.changePassword",
                meta: {
                    isFrontend: true,
                    auth: true,
                },
            },
            {
                path: "address",
                component: AddressComponent,
                name: "frontend.account.address",
                meta: {
                    isFrontend: true,
                    auth: true,
                },
            }
        ]
    },
    {
        path: "/checkout",
        component: CheckoutComponent,
        name: "frontend.checkout",
        redirect: {name: "frontend.checkout.checkout"},
        meta: {
            isFrontend: true,
            auth: true,
        },
        children: [
            {
                path: "cart-list",
                components: {default : CheckoutCartListComponent, header: CartListHeaderComponent},
                name: "frontend.checkout.cartList",
                meta: {
                    isFrontend: true,
                    auth: true
                }
            },
            {
                path: "checkout",
                components: {default: CheckoutCheckoutComponent, header: CheckoutHeaderComponent},
                name: "frontend.checkout.checkout",
                meta: {
                    isFrontend: true,
                    auth: true
                }
            },
            {
                path: "payment",
                components: {default: CheckoutPaymentComponent, header: PaymentHeaderComponent},
                name: "frontend.checkout.payment",
                meta: {
                    isFrontend: true,
                    auth: true
                }
            }
        ]
    }
];
