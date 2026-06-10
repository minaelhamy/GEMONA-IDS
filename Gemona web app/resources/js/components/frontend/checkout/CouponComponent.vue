<template>
    <LoadingComponent :props="loading" />
    <div v-if="Object.keys(cartCoupon).length !== 0"
        class="mb-6 rounded-2xl border border-success flex items-center gap-3 p-4 cursor-pointer">
        <div class="relative flex-shrink-0">
            <i class="lab-fill-shape lab-font-size-2xl opacity-[0.3] text-success"></i>
            <i
                class="lab-line-percent lab-font-size-8 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-success"></i>
        </div>
        <div class="flex-auto overflow-hidden">
            <h4
                class="font-semibold leading-5 mb-1 whitespace-nowrap overflow-hidden text-ellipsis capitalize text-success">
                {{ $t('message.coupon_applied') }}</h4>
            <h5 class="text-xs font-normal whitespace-nowrap overflow-hidden text-ellipsis">
                {{ $t('message.you_saved', { amount: cartCoupon.currency_discount }) }}
            </h5>
        </div>
        <button @click.prevent="destroyCoupon" class="lab-line-trash lab-font-size-xl text-danger"></button>
    </div>

    <div v-else @click.prevent="showTarget('coupon-modal', 'modal-active')"
        class="mb-6 rounded-2xl border border-focus flex items-center gap-3 p-4 cursor-pointer">
        <div class="relative flex-shrink-0">
            <i class="lab lab-fill-shape lab-font-size-2xl opacity-[0.3] text-focus"></i>
            <i
                class="lab lab-line-percent lab-font-size-8 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-focus"></i>
        </div>
        <div class="flex-auto overflow-hidden">
            <h4
                class="font-semibold leading-5 mb-1 whitespace-nowrap overflow-hidden text-ellipsis capitalize text-focus">
                {{ $t('message.apply_coupon') }}</h4>
            <h5 class="text-xs font-normal whitespace-nowrap overflow-hidden text-ellipsis">
                {{ $t('message.get_discount_with_your_order') }}
            </h5>
        </div>
        <i class="lab lab-line-chevron-right rtl:rotate-180 lab-font-size-2xl text-focus"></i>
    </div>

    <div id="coupon-modal"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-[360px]">
            <div class="flex items-center justify-between gap-2 py-4 px-4 border-b border-slate-100">
                <h3 class="text-lg font-bold capitalize"> {{ $t('label.coupon_code') }}</h3>
                <button @click.prevent="hideTarget('coupon-modal', 'modal-active')" type="button"
                    class="lab-line-circle-cross text-lg text-[#E93C3C]"></button>
            </div>
            <form @submit.prevent="couponChecking" class="w-full flex items-center px-4 mt-4">
                <input :class="error ? 'invalid' : ''" type="text" v-model="code"
                    class="h-11 w-full px-3 ltr:rounded-tl-lg rtl:rounded-tr-lg ltr:rounded-bl-lg rtl:rounded-br-lg border ltr:border-r-0 rtl:border-l-0 border-[#D9DBE9]">
                <button type="submit" class="h-11 px-4 leading-11 ltr:rounded-tr-lg rtl:rounded-tl-lg rtl:rounded-bl-lg ltr:rounded-br-lg rtl:rounded-br-0 rtl:rounded-tr-0
                capitalize font-semibold text-white bg-[#007FE3]">
                    {{ $t('button.apply') }}
                </button>
            </form>
            <small class="w-full px-4 pt-0 db-field-alert" v-if="error">{{ error }}</small>

            <div v-if="coupons.length > 0" class="p-4 pt-4 flex flex-col gap-4">
                <div v-for="coupon in coupons" :key="coupon" class="bg-[#EEF7FF] p-4 relative rounded-xl">
                    <h3 class="py-1 px-2 rounded font-medium text-xs w-fit mb-2 bg-[#FFDB1F]">
                        {{ $t('label.code') }}: {{ coupon.code }}
                    </h3>
                    <h4 class="text-sm font-medium mb-3">
                        {{ coupon.description }}
                    </h4>
                    <p class="text-xs text-text">{{ coupon.convert_start_date }} - {{ coupon.convert_end_date }}</p>
                    <button @click.prevent="appCouponButton(coupon)" type="button"
                        class="absolute bottom-0 ltr:right-0 rtl:left-0 text-sm font-semibold capitalize py-1.5 px-3 rounded-br-xl rounded-tl-xl text-white bg-primary">
                        {{ $t('button.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import targetService from "../../../services/targetService";
import alertService from "../../../services/alertService";
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
    name: "CouponComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            code: null,
            error: ""
        }
    },
    computed: {
        coupons: function () {
            return this.$store.getters['frontendCoupon/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        cartCoupon: function () {
            return this.$store.getters['frontendCart/coupon'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendCoupon/lists", {}).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        showTarget: function (id, cClass) {
            targetService.showTarget(id, cClass);
        },
        hideTarget: function (id, cClass) {
            this.code = null;
            this.error = "";
            targetService.hideTarget(id, cClass);
        },
        appCouponButton(coupon) {
            this.code = coupon.code;
        },
        couponChecking() {
            this.loading.isActive = true;
            this.$store.dispatch('frontendCoupon/checking', {
                total: this.subtotal,
                code: this.code
            }).then(res => {
                this.error = "";
                this.$store.dispatch("frontendCart/coupon", res.data.data);
                this.loading.isActive = false;
                this.hideTarget('coupon-modal', 'modal-active');
                alertService.success(this.$t('message.coupon_add'));
            }).catch((err) => {
                this.loading.isActive = false;
                this.error = err.response.data.message;
            });
        },
        destroyCoupon() {
            this.loading.isActive = true;
            this.$store.dispatch("frontendCart/destroyCoupon").then(res => {
                this.code = null;
                this.loading.isActive = false;
                alertService.success(this.$t('message.coupon_delete'));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err);
            });
        }
    }
}
</script>