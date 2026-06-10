<template>
    <div class="bg-white rounded-2xl shadow-card">
        <div class="p-4 border-b border-[#EFF0F6]">
            <h3 class="text-lg font-semibold capitalize">{{ $t('label.order_summery') }}</h3>
        </div>

        <ul class="flex flex-col gap-3 p-4 border-b border-[#EFF0F6]">
            <li class="flex items-center justify-between">
                <span class="capitalize">{{ $t('label.subtotal') }}</span>
                <span class="font-medium">{{ currencyFormat(subtotal, setting.site_digit_after_decimal_point,
                    setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
            </li>
            <li class="flex items-center justify-between">
                <span class="capitalize">{{ $t('label.tax') }}</span>
                <span class="font-medium">{{ currencyFormat(totalTax, setting.site_digit_after_decimal_point,
                    setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
            </li>
            <li class="flex items-center justify-between">
                <span class="capitalize">{{ $t('label.shipping_charge') }}</span>
                <span class="font-medium">{{ currencyFormat(shippingCharge, setting.site_digit_after_decimal_point,
                    setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
            </li>
            <li class="flex items-center justify-between">
                <span class="capitalize">{{ $t('label.discount') }}</span>
                <span class="font-medium">{{ currencyFormat(discount, setting.site_digit_after_decimal_point,
                    setting.site_default_currency_symbol, setting.site_currency_position) }}</span>
            </li>
        </ul>
        <div class="p-4">
            <dl class="flex items-center justify-between">
                <dt class="font-semibold capitalize">{{ $t('label.total') }}</dt>
                <dd class="font-semibold">{{ currencyFormat(total, setting.site_digit_after_decimal_point,
                    setting.site_default_currency_symbol, setting.site_currency_position) }}</dd>
            </dl>
        </div>
    </div>
</template>

<script>
import appService from "../../../services/appService";

export default {
    name: "SummeryComponent",
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        subtotal: function () {
            return this.$store.getters['frontendCart/subtotal'];
        },
        discount: function () {
            return this.$store.getters['frontendCart/discount'];
        },
        totalTax: function () {
            return this.$store.getters['frontendCart/totalTax'];
        },
        shippingCharge: function () {
            return this.$store.getters['frontendCart/shippingCharge'];
        },
        total: function () {
            return this.$store.getters['frontendCart/total'];
        }
    },
    methods: {
        currencyFormat(amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        }
    }
}
</script>