import AiAgentComponent from "../../components/admin/settings/AiAgent/AiAgentComponent.vue";

const SettingsComponent = () =>  import("../../components/admin/settings/SettingsComponent");
const CompanyComponent = () =>  import("../../components/admin/settings/Company/CompanyComponent");
const SiteComponent = () =>  import("../../components/admin/settings/Site/SiteComponent");
const MailComponent = () =>  import("../../components/admin/settings/Mail/MailComponent");
const OtpComponent = () =>  import("../../components/admin/settings/Otp/OtpComponent");
const NotificationComponent = () =>  import("../../components/admin/settings/Notification/NotificationComponent");
const SocialMediaComponent = () =>  import("../../components/admin/settings/SocialMedia/SocialMediaComponent");
const SocialLoginComponent = () =>  import("../../components/admin/settings/SocialLogin/SocialLoginComponent");
const LicenseComponent = () =>  import("../../components/admin/settings/License/LicenseComponent");
const CookiesComponent = () =>  import("../../components/admin/settings/Cookies/CookiesComponent");
const AnalyticComponent = () =>  import("../../components/admin/settings/analytics/AnalyticComponent");
const AnalyticListComponent = () =>  import("../../components/admin/settings/analytics/AnalyticListComponent");
const AnalyticShowComponent = () =>  import("../../components/admin/settings/analytics/AnalyticShowComponent");
const ThemeComponent = () =>  import("../../components/admin/settings/Theme/ThemeComponent");
const SliderComponent = () =>  import("../../components/admin/settings/Slider/SliderComponent");
const SliderListComponent = () =>  import("../../components/admin/settings/Slider/SliderListComponent");
const SliderShowComponent = () =>  import("../../components/admin/settings/Slider/SliderShowComponent");
const CurrencyComponent = () =>  import("../../components/admin/settings/Currency/CurrencyComponent");
const CurrencyListComponent = () =>  import("../../components/admin/settings/Currency/CurrencyListComponent");
const ProductCategoryListComponent = () =>  import("../../components/admin/settings/ProductCategory/ProductCateogryListComponent");
const ProductCategoryComponent = () =>  import("../../components/admin/settings/ProductCategory/ProductCategoryComponent");
const ProductCategoryShowComponent = () =>  import("../../components/admin/settings/ProductCategory/ProductCategoryShowComponent");
const ProductAttributeComponent = () =>  import("../../components/admin/settings/ProductAttribute/ProductAttributeComponent");
const ProductAttributeListComponent = () =>  import("../../components/admin/settings/ProductAttribute/ProductAttributeListComponent");
const ProductAttributeShowComponent = () =>  import("../../components/admin/settings/ProductAttribute/ProductAttributeShowComponent");
const TaxComponent = () =>  import("../../components/admin/settings/Tax/TaxComponent");
const TaxListComponent = () =>  import("../../components/admin/settings/Tax/TaxListComponent");
const PageComponent = () =>  import("../../components/admin/settings/Page/PageComponent");
const PageListComponent = () =>  import("../../components/admin/settings/Page/PageListComponent");
const PageShowComponent = () =>  import("../../components/admin/settings/Page/PageShowComponent");
const LanguageComponent = () =>  import("../../components/admin/settings/Language/LanguageComponent");
const LanguageListComponent = () =>  import("../../components/admin/settings/Language/LanguageListComponent");
const LanguageShowComponent = () =>  import("../../components/admin/settings/Language/LanguageShowComponent");
const SmsGatewayComponent = () =>  import("../../components/admin/settings/SmsGateway/SmsGatewayComponent");
const PaymentGatewayComponent = () =>  import("../../components/admin/settings/PaymentGateway/PaymentGatewayComponent");
const RoleComponent = () =>  import("../../components/admin/settings/Role/RoleComponent");
const RoleListComponent = () =>  import("../../components/admin/settings/Role/RoleListComponent");
const RoleShowComponent = () =>  import("../../components/admin/settings/Role/RoleShowComponent");
const ProductBrandComponent = () =>  import("../../components/admin/settings/ProductBrand/ProductBrandComponent");
const ProductBrandListComponent = () =>  import("../../components/admin/settings/ProductBrand/ProductBrandListComponent");
const ProductBrandShowComponent = () =>  import("../../components/admin/settings/ProductBrand/ProductBrandShowComponent");
const BenefitComponent = () =>  import("../../components/admin/settings/Benefit/BenefitComponent");
const BenefitListComponent = () =>  import("../../components/admin/settings/Benefit/BenefitListComponent");
const BenefitShowComponent = () =>  import("../../components/admin/settings/Benefit/BenefitShowComponent");
const UnitComponent = () =>  import("../../components/admin/settings/Unit/UnitComponent");
const UnitListComponent = () =>  import("../../components/admin/settings/Unit/UnitListComponent");
const SupplierComponent = () =>  import("../../components/admin/settings/Supplier/SupplierComponent");
const SupplierListComponent = () =>  import("../../components/admin/settings/Supplier/SupplierListComponent");
const SupplierShowComponent = () =>  import("../../components/admin/settings/Supplier/SupplierShowComponent");
const ShippingSetupComponent = () =>  import("../../components/admin/settings/ShippingSetup/ShippingSetupComponent");
const NotificationAlertComponent = () =>  import("../../components/admin/settings/NotificationAlert/NotificationAlertComponent");
const ReturnReasonComponent = () =>  import("../../components/admin/settings/ReturnReason/ReturnReasonComponent");
const ReturnReasonListComponent = () =>  import("../../components/admin/settings/ReturnReason/ReturnReasonListComponent"); 
const OutletComponent = () =>  import("../../components/admin/settings/Outlet/OutletComponent");
const OutletListComponent = () =>  import("../../components/admin/settings/Outlet/OutletListComponent");
const OutletShowComponent = () =>  import("../../components/admin/settings/Outlet/OutletShowComponent");
const CountryComponent = () =>  import("../../components/admin/settings/countries/CountryComponent");
const CountryListComponent = () =>  import("../../components/admin/settings/countries/CountryListComponent");
const StateComponent = () =>  import("../../components/admin/settings/states/StateComponent");
const StateListComponent = () =>  import("../../components/admin/settings/states/StateListComponent");
const CityComponent = () =>  import("../../components/admin/settings/cities/CityComponent");
const CityListComponent = () =>  import("../../components/admin/settings/cities/CityListComponent");
const LocationSetupComponent = () =>  import("../../components/admin/settings/LocationSetup/LocationSetupComponent");

export default [
    {
        path: "/admin/settings",
        component: SettingsComponent,
        name: "admin.settings",
        redirect: { name: "admin.settings.company" },
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: "settings",
            breadcrumb: "settings",
        },
        children: [
            {
                path: "company",
                component: CompanyComponent,
                name: "admin.settings.company",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "company",
                },
            },
            {
                path: "site",
                component: SiteComponent,
                name: "admin.settings.site",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "site",
                },
            },
            {
                path: "mail",
                component: MailComponent,
                name: "admin.settings.mail",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "mail",
                },
            },
            {
                path: "shipping-setup",
                component: ShippingSetupComponent,
                name: "admin.settings.shippingSetup",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "shipping_setup",
                },
            },
            {
                path: "otp",
                component: OtpComponent,
                name: "admin.settings.otp",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "otp",
                },
            },
            {
                path: "notification",
                component: NotificationComponent,
                name: "admin.settings.notification",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "notification",
                },
            },
            {
                path: "social-media",
                component: SocialMediaComponent,
                name: "admin.settings.socialMedia",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "social_media",
                },
            },
            {
                path: "social-login",
                component: SocialLoginComponent,
                name: "admin.settings.socialLogin",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "social_login",
                },
            },
            {
                path: "cookies",
                component: CookiesComponent,
                name: "admin.settings.cookies",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "cookies",
                },
            },
            {
                path: "analytics",
                component: AnalyticComponent,
                name: "admin.settings.analytic",
                redirect: { name: "admin.settings.analytic.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "analytics",
                },
                children: [
                    {

                        path: "list",
                        component: AnalyticListComponent,
                        name: "admin.settings.analytic.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: AnalyticShowComponent,
                        name: "admin.settings.analytic.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ]
            },
            {
                path: "theme",
                component: ThemeComponent,
                name: "admin.settings.theme",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "theme",
                },
            },
            {
                path: "sliders",
                component: SliderComponent,
                name: "admin.settings.slider",
                redirect: { name: "admin.settings.slider.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "sliders",
                },
                children: [
                    {
                        path: "list",
                        component: SliderListComponent,
                        name: "admin.settings.slider.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: SliderShowComponent,
                        name: "admin.settings.slider.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "currencies",
                component: CurrencyComponent,
                name: "admin.settings.currency",
                redirect: { name: "admin.settings.currency.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "currencies",
                },
                children: [
                    {
                        path: "list",
                        component: CurrencyListComponent,
                        name: "admin.settings.currency.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                ],
            },
            {
                path: "product-categories",
                component: ProductCategoryComponent,
                name: "admin.settings.productCategory",
                redirect: { name: "admin.settings.productCategory.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "product_categories",
                },
                children: [
                    {
                        path: "list",
                        component: ProductCategoryListComponent,
                        name: "admin.settings.productCategory.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: ProductCategoryShowComponent,
                        name: "admin.settings.productCategory.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "product-brands",
                component: ProductBrandComponent,
                name: "admin.settings.productBrand",
                redirect: { name: "admin.settings.productBrand.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "product_brands",
                },
                children: [
                    {
                        path: "list",
                        component: ProductBrandListComponent,
                        name: "admin.settings.productBrand.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: ProductBrandShowComponent,
                        name: "admin.settings.productBrand.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },

            {
                path: "suppliers",
                component: SupplierComponent,
                name: "admin.settings.supplier",
                redirect: { name: "admin.settings.supplier.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "suppliers",
                },
                children: [
                    {
                        path: "list",
                        component: SupplierListComponent,
                        name: "admin.settings.supplier.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: SupplierShowComponent,
                        name: "admin.settings.supplier.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },

            {
                path: "product-attributes",
                component: ProductAttributeComponent,
                name: "admin.settings.productAttribute",
                redirect: { name: "admin.settings.productAttribute.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "product_attributes",
                },
                children: [
                    {
                        path: "list",
                        component: ProductAttributeListComponent,
                        name: "admin.settings.productAttribute.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: ProductAttributeShowComponent,
                        name: "admin.settings.productAttribute.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "benefits",
                component: BenefitComponent,
                name: "admin.settings.benefit",
                redirect: { name: "admin.settings.benefit.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "benefits",
                },
                children: [
                    {
                        path: "list",
                        component: BenefitListComponent,
                        name: "admin.settings.benefit.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: BenefitShowComponent,
                        name: "admin.settings.benefit.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "units",
                component: UnitComponent,
                name: "admin.settings.unit",
                redirect: { name: "admin.settings.unit.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "units",
                },
                children: [
                    {
                        path: "list",
                        component: UnitListComponent,
                        name: "admin.settings.unit.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                ],
            },
            {
                path: "taxes",
                component: TaxComponent,
                name: "admin.settings.tax",
                redirect: { name: "admin.settings.tax.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "taxes",
                },
                children: [
                    {
                        path: "list",
                        component: TaxListComponent,
                        name: "admin.settings.tax.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                ],
            },
            {
                path: "pages",
                component: PageComponent,
                name: "admin.settings.page",
                redirect: { name: "admin.settings.page.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "pages",
                },
                children: [
                    {
                        path: "list",
                        component: PageListComponent,
                        name: "admin.settings.page.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: PageShowComponent,
                        name: "admin.settings.page.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "languages",
                component: LanguageComponent,
                name: "admin.settings.language",
                redirect: { name: "admin.settings.language.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "languages",
                },
                children: [
                    {
                        path: "list",
                        component: LanguageListComponent,
                        name: "admin.settings.language.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: LanguageShowComponent,
                        name: "admin.settings.language.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "ai-agent",
                component: AiAgentComponent,
                name: "admin.settings.aiAgent",
                meta: {
                    template: "admin",
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "ai_agent"
                },
            },
            {
                path: "sms-gateway",
                component: SmsGatewayComponent,
                name: "admin.settings.smsGateway",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "sms_gateway",
                },
            },
            {
                path: "payment-gateway",
                component: PaymentGatewayComponent,
                name: "admin.settings.paymentGateway",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "payment_gateway",
                },
            },
            {
                path: "role",
                component: RoleComponent,
                name: "admin.settings.role",
                redirect: { name: "admin.settings.role.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "role_permissions",
                },
                children: [
                    {
                        path: "list",
                        component: RoleListComponent,
                        name: "admin.settings.role.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: RoleShowComponent,
                        name: "admin.settings.role.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "notification-alert",
                component: NotificationAlertComponent,
                name: "admin.settings.notificationAlert",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "notification_alert",
                }
            },
            {
                path: "return-reasons",
                component: ReturnReasonComponent,
                name: "admin.settings.returnReason",
                redirect: { name: "admin.settings.returnReason.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "return_reasons",
                },
                children: [
                    {
                        path: "list",
                        component: ReturnReasonListComponent,
                        name: "admin.settings.returnReason.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    }
                ],
            },
            {
                path: "outlets",
                component: OutletComponent,
                name: "admin.settings.outlet",
                redirect: { name: "admin.settings.outlet.list" },
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "outlets",
                },
                children: [
                    {
                        path: "list",
                        component: OutletListComponent,
                        name: "admin.settings.outlet.list",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "show/:id",
                        component: OutletShowComponent,
                        name: "admin.settings.outlet.show",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "view",
                        },
                    },
                ],
            },
            {
                path: "license",
                component: LicenseComponent,
                name: "admin.settings.license",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "license",
                }
            },
            {
                path: "location-setup",
                component: LocationSetupComponent,
                name: "admin.settings.locationSetup",
                redirect :{name: "admin.settings.locationSetup.countries"},
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "settings",
                    breadcrumb: "location_setup",
                },
                children: [
                    {
                        path: "countries",
                        component: CountryListComponent,
                        name: "admin.settings.locationSetup.countries",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "states",
                        component: StateListComponent,
                        name: "admin.settings.locationSetup.states",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                    {
                        path: "cities",
                        component: CityListComponent,
                        name: "admin.settings.locationSetup.cities",
                        meta: {
                            isFrontend: false,
                            auth: true,
                            permissionUrl: "settings",
                            breadcrumb: "",
                        },
                    },
                ],
            },
            
        ],
    },
];
