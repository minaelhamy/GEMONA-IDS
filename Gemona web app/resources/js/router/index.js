import { createRouter, createWebHistory } from "vue-router";
import DashboardComponent from "../components/admin/dashboard/DashboardComponent";
import ExceptionComponent from "../components/exception/ExceptionComponent.vue";
import NotFoundComponent from "../components/exception/NotFoundComponent.vue";
import ENV from "../config/env";
import appService from "../services/appService";
import store from "../store";
import administratorRoutes from "./modules/administratorRoutes";
import authRoutes from "./modules/authRoutes";
import couponRoutes from "./modules/couponRoutes";
import creditBalanceReportRoutes from "./modules/creditBalanceReportRoutes";
import customerRoutes from "./modules/customerRoutes";
import damageRoutes from "./modules/damageRoutes";
import employeeRoutes from "./modules/employeeRoutes";
import frontendRoutes from "./modules/frontendRoutes";
import onlineOrderRoutes from "./modules/onlineOrderRoutes";
import posOrderRoutes from "./modules/posOrderRoutes";
import posRoutes from "./modules/posRoutes";
import ProductSectionRoutes from "./modules/ProductSectionRoutes";
import productsReportRoutes from "./modules/productsReportRoutes";
import productsRoutes from "./modules/productsRoutes";
import profileRoutes from "./modules/profileRoutes";
import PromotionRoutes from "./modules/PromotionRoutes";
import purchaseRoutes from "./modules/purchaseRoutes";
import pushNotificationRoutes from "./modules/pushNotificationRoutes";
import returnAndRefundRoutes from "./modules/returnAndRefundRoutes";
import returnOrderRoutes from "./modules/returnOrderRoutes";
import reviewRoutes from "./modules/reviewRoutes";
import salesReportRoutes from "./modules/salesReportRoutes";
import settingRoutes from "./modules/settingRoutes";
import stockRoutes from "./modules/stockRoutes";
import subscriberRoutes from "./modules/subscriberRoutes";
import transactionRoutes from "./modules/transactionRoutes";

const baseRoutes = [
    {
        path: "/",
        redirect: { name: "frontend.home" },
        name: "root",
    },
    {
        path: "/:pathMatch(.*)*",
        name: "route.notFound",
        component: NotFoundComponent,
        meta: {
            isFrontend: true,
        },
    },
    {
        path: "/exception",
        name: "route.exception",
        component: ExceptionComponent,
    },
    {
        path: "/admin/dashboard",
        component: DashboardComponent,
        name: "admin.dashboard",
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "dashboard",
            breadcrumb: "dashboard",
        },
    },
];

const routes = baseRoutes.concat(
    frontendRoutes,
    authRoutes,
    settingRoutes,
    profileRoutes,
    productsRoutes,
    administratorRoutes,
    customerRoutes,
    employeeRoutes,
    transactionRoutes,
    salesReportRoutes,
    creditBalanceReportRoutes,
    pushNotificationRoutes,
    productsRoutes,
    couponRoutes,
    PromotionRoutes,
    ProductSectionRoutes,
    purchaseRoutes,
    stockRoutes,
    returnOrderRoutes,
    damageRoutes,
    onlineOrderRoutes,
    productsReportRoutes,
    posOrderRoutes,
    posRoutes,
    returnAndRefundRoutes,
    subscriberRoutes,
    reviewRoutes
);

const permission = store.getters.authPermission;
appService.recursiveRouter(routes, permission);

const API_URL = ENV.API_URL;
const router = createRouter({
    linkActiveClass: "active",
    mode: "history",
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { left: 0, top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    if (to.meta.auth === true) {
        if (!store.getters.authStatus) {
            next({ name: "auth.login" });
        } else {
            if (to.meta.isFrontend === false) {
                if (to.meta.access === false) {
                    next({
                        name: "route.exception",
                    });
                } else {
                    next();
                }
            } else {
                next();
            }
        }
    } else if (
        (to.name === "auth.login" ||
            to.name === "auth.signup" ||
            to.name === "auth.forgotPassword") &&
        store.getters.authStatus
    ) {
        next({ name: "frontend.home" });
    } else {
        next();
    }
});
export default router;
