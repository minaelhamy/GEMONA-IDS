<template>
    <LoadingComponent :props="loading" />
    <section class="mb-28 sm:mb-20">
        <div class="container">
            <!--  Header Route Start -->
            <div class="flex items-start gap-4 mb-7">
                <button @click.prevent="goBack"
                    class="lab lab-line-undo lab-font-size-20 !text-xl !font-bold text-primary"></button>
                <router-view name="header" />
            </div>

            <!--  Header Route Close -->

            <!--  Checkbox Start -->
            <ul class="multi-step w-full max-w-lg mx-auto my-12 pt-2 pb-5 px-4 flex items-center justify-center">
                <li class="list-none w-full flex after:content-[''] after:w-full after:h-1 last:after:hidden last:w-fit"
                    :class="currentRoute === '/checkout/checkout' || currentRoute === '/checkout/payment' ? 'after:bg-success' : 'after:bg-[#EFF0F6]'">
                    <router-link :to="{ name: 'frontend.checkout.cartList' }"
                        class="flex flex-col items-center gap-4 -mt-[13px] relative">
                        <i v-if="currentRoute === '/checkout/checkout' || currentRoute === '/checkout/payment'"
                            class="lab lab-fill-save text-lg w-[30px] h-[30px] leading-[30px] text-center rounded-full text-white bg-success"></i>
                        <span v-else class="w-[30px] h-[30px] border-[4px] rounded-full border-success bg-white"></span>
                        <small :class="currentRoute === '/checkout/cart-list' ? 'text-success' : 'text-secondary'"
                            class="text-sm font-medium capitalize absolute -bottom-8">
                            {{ $t('label.cart') }}
                        </small>
                    </router-link>
                </li>

                <li class="list-none w-full flex after:content-[''] after:w-full after:h-1 last:after:hidden last:w-fit"
                    :class="currentRoute === '/checkout/payment' ? 'after:bg-success' : 'after:bg-[#EFF0F6]'">
                    <router-link :to="{ name: 'frontend.checkout.checkout' }"
                        class="flex flex-col items-center gap-4 -mt-[13px] relative">
                        <i v-if="currentRoute === '/checkout/payment'"
                            class="lab lab-fill-save text-lg w-[30px] h-[30px] leading-[30px] text-center rounded-full text-white bg-success"></i>
                        <span v-else
                            class="w-[30px] h-[30px] border-[4px] rounded-full border-[#D9DBE9] bg-[#D9DBE9]"></span>
                        <small :class="currentRoute === '/checkout/checkout' ? 'text-success' : 'text-secondary'"
                            class="text-sm font-medium capitalize absolute -bottom-8">
                            {{ $t('label.checkout') }}
                        </small>
                    </router-link>
                </li>

                <li
                    class="list-none w-full flex after:content-[''] after:w-full after:h-1 last:after:hidden last:w-fit after:bg-[#EFF0F6]">
                    <router-link :to="{ name: 'frontend.checkout.payment' }"
                        class="flex flex-col items-center gap-4 -mt-[13px] relative">
                        <span class="w-[30px] h-[30px] border-[4px] rounded-full border-[#D9DBE9] bg-[#D9DBE9]"></span>
                        <small :class="currentRoute === '/checkout/payment' ? 'text-success' : 'text-secondary'"
                            class="text-sm font-medium capitalize absolute -bottom-8">
                            {{ $t('label.payment') }}
                        </small>
                    </router-link>
                </li>
            </ul>
            <!--  Checkbox Close -->

            <!-- Default Router -->
            <router-view />
            <!-- Default Router -->
        </div>
    </section>
</template>

<script>
import CartListComponent from "./cartList/CartListComponent.vue";
import router from "../../../router";
import appService from "../../../services/appService";
import CouponComponent from "./CouponComponent.vue";
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
    name: "CheckoutComponent",
    components: { LoadingComponent, CouponComponent, CartListComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            currentRoute: null,
        }
    },
    computed: {
        isList: function () {
            return this.$store.getters['frontendCart/isList'];
        },
    },
    mounted() {
        this.currentRoute = this.$route.path;
        this.$store.dispatch('frontendCart/listChecker').then(res => {
            if (!res.status) {
                this.$router.push({ name: 'frontend.home' });
            }
        }).catch((err) => {
            if (!err.status) {
                this.$router.push({ name: 'frontend.home' });
            }
        })
    },
    methods: {
        goBack: function () {
            router.go(-1)
        }
    },
    watch: {
        $route(to, from) {
            this.currentRoute = to.path;
        },
        isList: {
            deep: true,
            handler(isListObject) {
                if (!isListObject) {
                    this.$router.push({ name: 'frontend.home' });
                }
            }
        }
    }
}
</script>