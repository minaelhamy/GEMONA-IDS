<template>
    <aside id="cart-canvas" @click="closeBackdrop" class="fixed inset-0 z-30 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-md h-dvh overflow-x-hidden overflow-y-auto bg-white duration-500 transition-all ms-auto ltr:translate-x-full rtl:-translate-x-full">
            <div class="py-5 flex items-center justify-between px-4 border-b border-slate-100">
                <h3 class="text-[22px] font-bold capitalize">{{ $t('label.shopping_cart') }}</h3>
                <button type="button" class="lab-line-circle-cross text-lg text-danger"
                    @click.prevent="closeCanvas('cart-canvas')"></button>
            </div>
            
            <div v-if="carts.length === 0" class="h-[calc(100vh_-_218px)] flex flex-col items-center justify-center">
                <img class="w-52" :src="setting.image_cart" alt="empty">
                <p class="text-sm">{{ $t('message.empty_cart') }}</p>
            </div>

            <ul v-if="carts.length > 0" class="px-4 pt-4 pb-10 sm:pb-4 sm:h-[calc(100vh_-_218px)] sm:overflow-y-auto">
                <li v-for="(cart, index) in carts"
                    class="flex items-start gap-3 pb-4 mb-4 border-b last:mb-0 last:pb-0 last:border-none border-gray-100">
                    <img :src="cart.image" alt="products" class="w-28 rounded-lg flex-shrink-0" />

                    <div class="relative w-full overflow-hidden">
                        <h4 class="font-semibold capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-1">
                            {{ cart.name }}
                        </h4>
                        <div v-if="cart.variation_id > 0" class="flex flex-wrap mb-2">
                            <span class="text-xs capitalize inline-flex items-center">{{ cart.variation_names }}</span>
                        </div>
                        <div class="flex flex-wrap gap-3 mb-3">
                            <span class="font-semibold font-sans">{{ currencyFormat(cart.price,
                                setting.site_digit_after_decimal_point,
                                setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
                            <del v-if="cart.discount > 0" class="font-semibold font-sans text-[#FF6262]">
                                {{ currencyFormat(cart.old_price, setting.site_digit_after_decimal_point,
                                    setting.site_default_currency_symbol, setting.site_currency_position) }}
                            </del>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-1 w-20 p-1 rounded-full bg-[#F7F7FC]">
                                <button @click.prevent="quantityDecrement(index, cart)" type="button"
                                    :class="cart.quantity === 1 ? 'cursor-not-allowed' : ''"
                                    class="lab-fill-circle-minus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
                                <input v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp(index, cart, $event)"
                                    type="number" v-model="cart.quantity"
                                    class="text-center w-full h-5 text-sm font-medium">
                                <button
                                    :class="cart.quantity >= cart.stock ? 'cursor-not-allowed' : cart.quantity >= cart.maximum_purchase_quantity ? 'cursor-not-allowed' : ''"
                                    @click.prevent="quantityIncrement(index, cart)" type="button"
                                    class="lab-fill-circle-plus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
                            </div>
                            <button @click.prevent="removeProduct(index)"
                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FFF4F4] text-[#E93C3C] transition-all duration-300 hover:bg-[#E93C3C] hover:text-white">
                                <i class="lab-line-trash text-sm"></i>
                                <span class="text-xs font-medium capitalize hidden sm:block">{{ $t('button.remove')
                                    }}</span>
                            </button>
                        </div>
                    </div>
                </li>
            </ul>

            <div v-if="carts.length > 0" class="p-4 border-t border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold capitalize">{{ $t('label.subtotal') }}</h3>
                    <h4 class="font-semibold capitalize font-sans">{{ currencyFormat(subtotal,
                        setting.site_digit_after_decimal_point,
                        setting.site_default_currency_symbol, setting.site_currency_position) }} </h4>
                </div>
                <router-link :to="{ name: 'frontend.checkout' }" v-on:click="closeCanvas('cart-canvas')"
                    class="text-center w-full mb-3 h-12 leading-12 px-12 font-semibold tracking-wide rounded-full whitespace-nowrap text-white bg-primary">
                    {{ $t('button.process_to_checkout') }}
                </router-link>
                <p class="pb-12 sm:pb-0 capitalize text-xs text-center font-medium">{{ $t('message.checkout_guide') }}
                </p>
            </div>
        </div>
    </aside>
</template>

<script>
import targetService from "../../../services/targetService";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";
import {useCanvas} from "../../../composables/canvas";

export default {
    name: "FrontendCartComponent",
    data() {
        return {}
    },
    setup() {
        const {closeCanvas, closeBackdrop} = useCanvas();

        return {
            closeCanvas,
            closeBackdrop
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        cartCoupon: function () {
            return this.$store.getters['frontendCart/coupon'];
        }

    },
    methods: {
        hideTarget: function (id, cClass) {
            targetService.hideTarget(id, cClass);
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        quantityUp: function (id, product, e) {
            let quantity = e.target.value;

            if (quantity === 0) {
                quantity = 1;
            }
            if (quantity > product.stock) {
                quantity = product.stock
            }
            if (quantity > product.maximum_purchase_quantity) {
                alertService.error(this.$t('message.purchase_limit_exceeded'));
                quantity = product.maximum_purchase_quantity
            }
            this.$store.dispatch('frontendCart/quantity', { id: id, status: quantity }).then().catch();
        },
        quantityIncrement: function (id, product) {
            let quantity = product.quantity;
            quantity++;
            if (quantity <= 0) {
                quantity = 1;
            }

            if (quantity > product.stock) {
                quantity--;
            }
            if (quantity > product.maximum_purchase_quantity) {
                quantity--;
            }
            this.$store.dispatch('frontendCart/quantity', { id: id, status: quantity }).then().catch();
        },
        quantityDecrement: function (id, product) {
            let quantity = product.quantity;
            quantity--;
            if (quantity <= 0) {
                quantity = 1;
            }
            this.$store.dispatch('frontendCart/quantity', { id: id, status: quantity }).then().catch();
        },
        removeProduct: function (id) {
            this.$store.dispatch('frontendCart/remove', { id: id }).then().catch();
            if (Object.keys(this.cartCoupon).length !== 0) {
                this.$store.dispatch("frontendCart/destroyCoupon").then().catch();
                alertService.warning(this.$t('message.coupon_remove'));
            }
        }
    }
}
</script>