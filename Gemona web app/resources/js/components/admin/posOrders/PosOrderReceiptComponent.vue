<template>
    <div id="receiptModal" class="modal">
        <div class="modal-dialog max-w-[302px] rounded-none" id="print">
            <div class="modal-body">
                <div class="text-center pb-3.5 border-b border-dashed border-gray-400">
                    <h3 class="font-bold mb-1">{{ company.company_name }}</h3>
                    <h4 class="text-sm font-normal">{{ company.company_address }}</h4>
                    <h5 class="text-sm font-normal">{{ $t('label.tel') }}: {{ company.company_calling_code }} {{
                        company.company_phone
                    }}</h5>
                </div>

                <table class="w-full my-1.5">
                    <tbody>
                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">{{ $t('label.order_id') }}
                                #{{ order.order_serial_no }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-xs text-left py-0.5 text-heading">{{ order.order_date }}</td>
                            <td class="text-xs text-right py-0.5 text-heading">{{ order.order_time }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="w-full">
                    <thead class="border-t border-b border-dashed border-gray-400">
                        <tr>
                            <th scope="col" class="py-1 font-normal text-xs capitalize text-left text-heading w-8">
                                {{ $t('label.qty') }}
                            </th>
                            <th scope="col"
                                class="py-1 font-normal text-xs capitalize flex items-center justify-between text-heading">
                                <span>{{ $t('label.product_description') }}</span>
                                <span>{{ $t('label.price') }}</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="border-b border-dashed border-gray-400">
                        <tr v-if="orderProducts.length > 0" v-for="product in orderProducts" :key="product">
                            <td class="text-left font-normal align-top py-1">
                                <p class="text-xs leading-5 text-heading">{{ product.quantity }}</p>
                            </td>
                            <td class="text-left font-normal align-top py-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs leading-5 text-heading">{{ product.product_name }}
                                    </p>
                                    <p class="text-xs leading-5 text-heading">{{ product.subtotal_currency_price }}
                                    </p>
                                </div>
                                <p v-if="product.variation_names" class="text-xs leading-5 text-heading max-w-[200px]">
                                    {{ product.variation_names }}
                                </p>
                                <p class="text-xs leading-5 text-heading" v-if="product.product_tax.length > 0"
                                    v-for="tax in product.product_tax" :key="tax">
                                    {{ tax.tax_name }} ({{ tax.tax_rate }}%)
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="py-2 pl-7">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.subtotal') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">
                                    {{ order.subtotal_currency_price }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">
                                    {{ $t('label.tax_fee') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">
                                    {{ order.tax_currency_price }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs text-left py-0.5 uppercase text-heading">{{ $t('label.discount') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 text-heading">{{ order.discount_currency_price }}
                                </td>
                            </tr>

                            <tr>
                                <td class="text-xs text-left py-0.5 font-bold uppercase text-heading">
                                    {{ $t('label.total') }}:
                                </td>
                                <td class="text-xs text-right py-0.5 font-bold text-heading">
                                    {{ order.total_currency_price }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-xs py-2 border-t border-b border-dashed border-gray-400 text-heading">
                    {{ $t('label.payment_type') }}: {{ order.pos_payment_method_name }}
                </p>
                <div class="text-center pt-2 pb-4">
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.thank_you') }}
                    </p>
                    <p class="text-[11px] leading-[14px] capitalize text-heading">
                        {{ $t('message.please_come_again') }}
                    </p>
                </div>
                <div class="flex flex-col items-end">
                    <h5 class="text-[8px] font-normal text-left w-[46px] leading-[10px]">
                        {{ $t('label.powered_by') }}
                    </h5>
                    <h6 class="text-xs font-normal leading-4">{{ company.company_name }}</h6>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "PosOrderReceiptComponent",
    props: {
        order: Object
    },
    computed: {
        company: function () {
            return this.$store.getters['company/lists'];
        },
        orderProducts: function () {
            return this.$store.getters['posOrder/orderProducts'];
        },
    },
    mounted() {
        this.$store.dispatch("company/lists").then().catch();
    }
}
</script>