<template>
    <div class="row">
        <div class="col-12 lg:col-8">
            <ul v-if="carts.length > 0" class="p-4 mb-11 rounded-2xl shadow-card">
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
                    <span class="font-semibold font-sans">
                        {{ currencyFormat(cart.price, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
                    </span>
                            <del v-if="cart.discount > 0" class="font-semibold font-sans text-[#FF6262]">
                                {{ currencyFormat(cart.old_price, setting.site_digit_after_decimal_point, setting.site_default_currency_symbol, setting.site_currency_position) }}
                            </del>
                        </div>

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-1 w-20 p-1 rounded-full bg-[#F7F7FC]">
                                <button @click.prevent="quantityDecrement(index, cart)" type="button"
                                        :class="cart.quantity === 1 ? 'cursor-not-allowed': ''"
                                        class="lab-fill-circle-minus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
                                <input v-on:keypress="onlyNumber($event)" v-on:keyup="quantityUp(index, cart, $event)"
                                       type="number" :value="cart.quantity"
                                       class="text-center w-full h-5 text-sm font-medium">
                                <button :class="cart.quantity >= cart.stock ? 'cursor-not-allowed': ''"
                                        @click.prevent="quantityIncrement(index, cart)" type="button"
                                        class="lab-fill-circle-plus text-lg leading-none transition-all duration-300 hover:text-primary"></button>
                            </div>
                            <button @click.prevent="removeProduct(index)"
                                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FFF4F4] text-[#E93C3C] transition-all duration-300 hover:bg-[#E93C3C] hover:text-white">
                                <i class="lab-line-trash text-sm"></i>
                                <span class="text-xs font-medium capitalize hidden sm:block">{{ $t('button.remove') }}</span>
                            </button>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="text-right">
                <router-link :to="{name : 'frontend.checkout.checkout'}" class="max-lg:hidden field-button w-fit font-semibold tracking-wide normal-case">{{ $t('button.process_to_checkout') }}</router-link>
            </div>
        </div>

        <div class="col-12 lg:col-4">
            <CouponComponent />
            <SummeryComponent />

            <router-link :to="{ name : 'frontend.checkout.checkout' }" class="max-lg:block hidden field-button mt-6 font-semibold tracking-wide normal-case">{{ $t('button.process_to_checkout') }}</router-link>
        </div>
    </div>
</template>

<script>
import appService from "../../../../services/appService";
import CouponComponent from "../CouponComponent.vue";
import SummeryComponent from "../SummeryComponent.vue";

export default {
    name: "CartListComponent",
    components: {SummeryComponent, CouponComponent},
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        }
    },
    methods: {
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
            this.$store.dispatch('frontendCart/quantity', {id: id, status: quantity}).then().catch();
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
            this.$store.dispatch('frontendCart/quantity', {id: id, status: quantity}).then().catch();
        },
        quantityDecrement: function (id, product) {
            let quantity = product.quantity;
            quantity--;
            if (quantity <= 0) {
                quantity = 1;
            }
            this.$store.dispatch('frontendCart/quantity', {id: id, status: quantity}).then().catch();
        },
        removeProduct: function (id) {
            this.$store.dispatch('frontendCart/remove', {id: id}).then().catch();
        }
    }
}
</script>