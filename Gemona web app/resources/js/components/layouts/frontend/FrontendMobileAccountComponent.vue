<template>
    <aside id="mobile-profile-canvas"
        class="fixed inset-0 z-30 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xs h-dvh overflow-x-hidden overflow-y-auto bg-white duration-500 transition-all ltr:-translate-x-full rtl:translate-x-full">
            <div class="py-4 flex items-center justify-between px-4 border-b border-slate-100">
                <router-link :to="{ name: 'frontend.home' }"
                    class="router-link-active router-link-exact-active flex-shrink-0">
                    <img class="w-28 sm:w-32" :src="setting.theme_logo" alt="logo">
                </router-link>

                <button type="button">
                    <i @click.prevent="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        class="lab-line-circle-cross text-xl text-danger"></i>
                </button>
            </div>

            <div class="py-6 lg:rounded-2xl lg:shadow-card bg-white">
                <div class="flex flex-col items-center justify-center mb-5">
                    <img :src="profile?.image" alt="avatar" class="w-20 h-20 mb-3 rounded-full border border-primary">
                    <h3 class="capitalize text-lg font-semibold text-center mb-0.5">{{ textShortener(profile.name, 20)
                        }}
                    </h3>
                    <p v-if="profile.phone" class="text-center text-text" dir="ltr">{{ profile.country_code }}{{
                        profile.phone }}</p>
                </div>
                <nav class="flex flex-col">
                    <router-link v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        v-if="profile.role_id !== enums.roleEnum.CUSTOMER && Object.keys(authDefaultPermission).length > 0"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500"
                        :to="{ name: 'admin.dashboard' }">
                        <i
                            class="lab-fill-dashboard text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.dashboard') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/overview') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500"
                        :to="{ name: 'frontend.account.overview' }">
                        <i
                            class="lab lab-fill-dashboard text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.overview') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/order-history') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.account.orderHistory' }"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i class="lab-fill-bag text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.order_history') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/return-orders') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.account.returnOrders' }"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i
                            class="lab-fill-refresh text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.return_orders') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/account-info') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.account.accountInfo' }"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i
                            class="lab-fill-user text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.account_info') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/change-password') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.account.changePassword' }"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i class="lab-fill-key text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.change_password') }}</span>
                    </router-link>

                    <router-link :class="checkIsPathAndRoutePathSame('/account/address') ? '!text-primary' : ''"
                        v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.account.address' }"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i
                            class="lab-fill-location text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('menu.address') }}</span>
                    </router-link>

                    <button v-on:click="hideTarget('mobile-profile-canvas', 'canvas-active')" @click.prevent="logout()"
                        class="profile-link font-medium flex items-center gap-4 capitalize py-3 px-4 group hover:text-primary transition-all duration-500">
                        <i
                            class="lab-fill-logout text-[#A0A3BD] group-hover:text-primary transition-all duration-500"></i>
                        <span>{{ $t('button.logout') }}</span>
                    </button>
                </nav>
            </div>
        </div>
    </aside>
</template>

<script>
import targetService from "../../../services/targetService";
import appService from "../../../services/appService";
import activityEnum from "../../../enums/modules/activityEnum";
import roleEnum from "../../../enums/modules/roleEnum";

export default {
    name: "FrontendMobileAccountComponent",
    data() {
        return {
            enums: {
                activityEnum: activityEnum,
                roleEnum: roleEnum
            },
            currentRoute: "",
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        profile: function () {
            return this.$store.getters.authInfo;
        },
        authDefaultPermission: function () {
            return this.$store.getters.authDefaultPermission;
        },
    },
    methods: {
        hideTarget: function (id, cClass) {
            targetService.hideTarget(id, cClass);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        checkIsPathAndRoutePathSame(path) {
            if (this.currentRoute === path) {
                return true;
            }
        },
        logout: function () {
            this.$store.dispatch("logout").then(res => {
                this.$router.push({ name: "frontend.home" });
            }).catch();
        }
    },
    watch: {
        $route(to, from) {
            this.currentRoute = to.path;
        },
    }
}
</script>