import { createStore } from "vuex";

import createPersistedState from "vuex-persistedstate";
import { administrator } from "./modules/administrator";
import { administratorAddress } from "./modules/administratorAddress";
import { analytic } from "./modules/analytic";
import { analyticSection } from "./modules/analyticSection";
import { auth } from "./modules/auth";
import { barcode } from "./modules/barcode";
import { benefit } from "./modules/benefit";
import { city } from "./modules/city";
import { company } from "./modules/company";
import { cookies } from "./modules/cookies";
import { country } from "./modules/country";
import { countryCode } from "./modules/countryCode";
import { coupon } from "./modules/coupon";
import { creditBalanceReport } from "./modules/creditBalanceReport";
import { currency } from "./modules/currency";
import { customer } from "./modules/customer";
import { customerAddress } from "./modules/customerAddress";
import { damage } from "./modules/damage";
import { dashboard } from "./modules/dashboard";
import { employee } from "./modules/employee";
import { employeeAddress } from "./modules/employeeAddress";
import { frontendAddress } from "./modules/frontend/frontendAddress";
import { frontendBenefit } from "./modules/frontend/frontendBenefit";
import { frontendCart } from "./modules/frontend/frontendCart";
import { frontendCountryCode } from "./modules/frontend/frontendCountryCode";
import { frontendCountryStateCity } from "./modules/frontend/frontendCountryStateCity";
import { frontendCoupon } from "./modules/frontend/frontendCoupon";
import { frontendEditProfile } from "./modules/frontend/frontendEditProfile";
import { frontendLanguage } from "./modules/frontend/frontendLanguage";
import { frontendOrder } from "./modules/frontend/frontendOrder";
import { frontendOrderArea } from "./modules/frontend/frontendOrderArea";
import { frontendOutlet } from "./modules/frontend/frontendOutlet";
import { frontendOverview } from "./modules/frontend/frontendOverview";
import { frontendPage } from "./modules/frontend/frontendPage";
import { frontendPaymentGateway } from "./modules/frontend/frontendPaymentGateway";
import { frontendProduct } from "./modules/frontend/frontendProduct";
import { frontendProductBrand } from "./modules/frontend/frontendProductBrand";
import { frontendProductCategory } from "./modules/frontend/frontendProductCategory";
import { frontendProductReview } from "./modules/frontend/frontendProductReview";
import { frontendProductSection } from "./modules/frontend/frontendProductSection";
import { frontendProductVariation } from "./modules/frontend/frontendProductVariation";
import { frontendPromotion } from "./modules/frontend/frontendPromotion";
import { frontendReturnAndRefund } from "./modules/frontend/frontendReturnAndRefund";
import { frontendReturnReason } from "./modules/frontend/frontendReturnReason";
import { frontendSetting } from "./modules/frontend/frontendSetting";
import { frontendSignup } from "./modules/frontend/frontendSignup";
import { frontendSlider } from "./modules/frontend/frontendSlider";
import { frontendWishlist } from "./modules/frontend/frontendWishlist";
import { globalState } from "./modules/frontend/globalState";
import { frontendSocialLogin } from "./modules/frontend/frontendSocialLogin";
import { language } from "./modules/language";
import { license } from "./modules/license";
import { mail } from "./modules/mail";
import { menuSection } from "./modules/menuSection";
import { menuTemplate } from "./modules/menuTemplate";
import { myOrderDetails } from "./modules/myOrderDetails";
import { notification } from "./modules/notification";
import { notificationAlert } from "./modules/notificationAlert";
import { onlineOrder } from "./modules/onlineOrder";
import { orderArea } from "./modules/orderArea";
import { otp } from "./modules/otp";
import { outlet } from "./modules/outlet";
import { page } from "./modules/page";
import { paymentGateway } from "./modules/paymentGateway";
import { permission } from "./modules/permission";
import { posCart } from "./modules/posCart";
import { posOrder } from "./modules/posOrder";
import { posProduct } from "./modules/posProduct";
import { posProductCategory } from "./modules/posProductCategory";
import { posProductVariation } from "./modules/posProductVariation";
import { product } from "./modules/product";
import { productAttribute } from "./modules/productAttribute";
import { productAttributeOption } from "./modules/productAttributeOption";
import { productBrand } from "./modules/productBrand";
import { productCategory } from "./modules/productCategory";
import { productSection } from "./modules/productSection";
import { productSectionProduct } from "./modules/productSectionProduct";
import { productSeo } from "./modules/productSeo";
import { productsReport } from "./modules/productsReport";
import { productVariation } from "./modules/productVariation";
import { productVideo } from "./modules/productVideo";
import { promotion } from "./modules/promotion";
import { promotionProduct } from "./modules/promotionProduct";
import { purchase } from "./modules/purchase";
import { pushNotification } from "./modules/pushNotification";
import { returnAndRefund } from "./modules/returnAndRefund";
import { returnOrder } from "./modules/returnOrder";
import { returnReason } from "./modules/returnReason";
import { review } from "./modules/review";
import { role } from "./modules/role";
import { salesReport } from "./modules/salesReport";
import { shippingSetup } from "./modules/shippingSetup";
import { site } from "./modules/site";
import { slider } from "./modules/slider";
import { smsGateway } from "./modules/smsGateway";
import { socialMedia } from "./modules/socialMedia";
import { socialLogin } from "./modules/socialLogin";
import { state } from "./modules/state";
import { stock } from "./modules/stock";
import { subscriber } from "./modules/subscriber";
import { supplier } from "./modules/supplier";
import { tax } from "./modules/tax";
import { theme } from "./modules/theme";
import { timezone } from "./modules/timezone";
import { transaction } from "./modules/transaction";
import { unit } from "./modules/unit";
import { user } from "./modules/user";
import { aiAgent } from "./modules/aiAgent";
import { ai } from "./modules/ai";

export default new createStore({
    state: {},
    mutations: {},
    actions: {},
    modules: {
        auth,
        company,
        countryCode,
        mail,
        otp,
        notification,
        socialMedia,
        socialLogin,
        license,
        cookies,
        page,
        analytic,
        analyticSection,
        theme,
        slider,
        currency,
        site,
        productCategory,
        tax,
        returnReason,
        globalState,
        menuSection,
        menuTemplate,
        language,
        smsGateway,
        productAttribute,
        paymentGateway,
        timezone,
        productAttributeOption,
        role,
        permission,
        product,
        administrator,
        administratorAddress,
        customer,
        customerAddress,
        employee,
        employeeAddress,
        unit,
        productBrand,
        barcode,
        transaction,
        salesReport,
        creditBalanceReport,
        productVariation,
        pushNotification,
        user,
        productVideo,
        productSeo,
        promotion,
        promotionProduct,
        productSection,
        productSectionProduct,
        benefit,
        purchase,
        damage,
        returnOrder,
        supplier,
        outlet,
        coupon,
        frontendSetting,
        frontendLanguage,
        frontendEditProfile,
        frontendCountryCode,
        frontendPage,
        frontendSlider,
        frontendProductCategory,
        frontendProduct,
        frontendBenefit,
        frontendPromotion,
        frontendProductSection,
        frontendWishlist,
        frontendProductVariation,
        frontendAddress,
        frontendSignup,
        frontendCart,
        frontendCoupon,
        frontendSocialLogin,
        stock,
        shippingSetup,
        orderArea,
        notificationAlert,
        frontendPaymentGateway,
        frontendOrder,
        frontendOrderArea,
        dashboard,
        frontendReturnAndRefund,
        frontendReturnReason,
        frontendOverview,
        onlineOrder,
        productsReport,
        myOrderDetails,
        frontendProductReview,
        posOrder,
        posProductVariation,
        posProductCategory,
        posProduct,
        posCart,
        returnAndRefund,
        frontendProductBrand,
        frontendOutlet,
        subscriber,
        frontendCountryStateCity,
        country,
        state,
        city,
        review,
        aiAgent,
        ai
    },
    plugins: [
        createPersistedState({
            paths: ["auth", "globalState", "frontendCart", "posCart"],
        }),
    ],
});
