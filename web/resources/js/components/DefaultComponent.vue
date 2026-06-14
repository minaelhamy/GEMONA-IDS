<template>
    <div v-if="theme === 'loading'">
        <LoadingComponent :props="{isActive:true}" />
    </div>

    <div v-if="theme === 'frontend'">
        <FrontendNavbarComponent />
        <FrontendCartComponent />
        <router-view></router-view>
        <FrontendMobileSideBarComponent />
        <FrontendMobileNavBarComponent />
        <FrontendMobileCategoryComponent />
        <FrontendMobileAccountComponent />
        <FrontendCookiesComponent />
        <FrontendFooterComponent />
    </div>

    <div v-if="theme === 'backend'">
        <main class="db-main" v-if="logged">
            <BackendNavbarComponent />
            <BackendMenuComponent />
            <router-view></router-view>
            <BackendAiSidebarComponent />
        </main>
        <div v-if="!logged">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import BackendNavbarComponent from "./layouts/backend/BackendNavbarComponent";
import BackendMenuComponent from "./layouts/backend/BackendMenuComponent";
import FrontendNavbarComponent from "./layouts/frontend/FrontendNavBarComponent";
import FrontendFooterComponent from "./layouts/frontend/FrontendFooterComponent";
import FrontendCartComponent from "./layouts/frontend/FrontendCartComponent";
import FrontendMobileNavBarComponent from "./layouts/frontend/FrontendMobileNavBarComponent";
import FrontendMobileCategoryComponent from "./layouts/frontend/FrontendMobileCategoryComponent";
import FrontendMobileAccountComponent from "./layouts/frontend/FrontendMobileAccountComponent";
import FrontendMobileSideBarComponent from "./layouts/frontend/FrontendMobileSideBarComponent";
import FrontendCookiesComponent from "./layouts/frontend/FrontendCookiesComponent";
import DisplayModeEnum from "../enums/modules/displayModeEnum";
import LoadingComponent from "../components/frontend/components/LoadingComponent.vue";
import BackendAiSidebarComponent from "./layouts/backend/BackendAiSidebarComponent.vue";

export default {
    name: "DefaultComponent",
    components: {
        FrontendMobileSideBarComponent,
        FrontendMobileAccountComponent,
        FrontendMobileCategoryComponent,
        FrontendMobileNavBarComponent,
        FrontendCartComponent,
        FrontendNavbarComponent,
        FrontendFooterComponent,
        BackendNavbarComponent,
        BackendMenuComponent,
        FrontendCookiesComponent,
        BackendAiSidebarComponent,
        LoadingComponent
    },
    data() {
        return {
            theme: "loading",
        }
    },
    beforeMount() {
        this.themeDefine(this.$route);
        this.displayModeDefine();
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.$store.dispatch("globalState/init", {
                language_id: res.data.data.site_default_language,
                search_restaurant: "",
                location: null,
                latitude: null,
                longitude: null
            });
        }).catch();

    },
    computed: {
        logged: function () {
            return this.$store.getters.authStatus;
        },
        displayMode: function () {
            return this.$store.getters['globalState/lists'].display_mode;
        },
    },
    methods: {
        themeDefine: function (route) {
            this.theme = route.meta.isFrontend === true ? "frontend" : "backend";
        },
        displayModeDefine: function () {
            let dir = "ltr";
            const attributes = {
                dir: "ltr",
            };
            if (this.$store.getters['globalState/lists'].display_mode === DisplayModeEnum.LTR) {
                dir = "ltr";
            } else {
                dir = "rtl";
            }
            Object.keys(attributes).forEach(attr => {
                document.documentElement.setAttribute(attr, dir);
            });
        }
    },

    watch: {
        $route(e) {
            this.themeDefine(e);
        },
        displayMode() {
            this.displayModeDefine();
        }
    },
}
</script>
