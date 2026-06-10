<template>
    <section class="col-12 pt-4 pb-4">
        <router-link to="" @click="$router.go(-1)" class="mb-3 inline-flex items-center gap-2 text-primary">
            <i class="lab lab-line-undo lab-font-size-16"></i>
            <span class="text-xs font-medium leading-6">{{ $t('label.back_to_orders') }}</span>
        </router-link>
        <div class="flex items-start flex-col md:flex-row gap-6">
            <div class="w-full">
                <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                    <h3 class="text-sm leading-6 mb-1 font-medium">{{ $t("label.order_id") }}: <span
                            class="text-[#008BBA]">#{{ order.order_serial_no }}</span></h3>
                    <p class="text-xs font-light mb-3">{{ $t('label.order_date') }}: {{ order.order_datetime }}
                    </p>
                    <p class="text-xs font-light mb-3">{{
                        $t('label.order_type')
                    }}: {{ enums.orderTypeEnumArray[order.order_type] }}</p>
                    <p class="text-xs font-light mb-3">
                        {{
                            $t('label.order_status')
                        }}:
                        <span :class="orderStatusClass(order.status)">
                            {{ enums.orderStatusEnumArray[order.status] }}
                        </span>
                    </p>

                    <OrderStatusComponent :order="order" />
                    <div class="my-4" v-if="parseInt(order.status) === parseInt(enums.orderStatusEnum.REJECTED)">
                        <h3 class="capitalize font-medium text-sm leading-6 mb-2">{{ $t("label.reason") }}:</h3>
                        <p class="text-sm text-heading mb-2">{{ order.reason }}</p>
                    </div>
                </div>

                <div v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                    <div v-for="orderAddress in orderAddresses" class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-sm leading-6 font-medium mb-2"
                            v-if="orderAddress.address_type === enums.addressTypeEnum.SHIPPING">
                            {{ $t("label.shipping_address") }}
                        </h3>
                        <h3 class="text-sm leading-6 font-medium mb-2" v-else>
                            {{ $t("label.billing_address") }}
                        </h3>
                        <div class="flex items-start justify-start gap-2.5">
                            <ul class="flex flex-col gap-2.5 border-t border-dashed border-gray-100">
                                <li class="flex flex-wrap sm:flex-nowrap gap-2 pt-4">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.name')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">{{ orderAddress.full_name }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.phone')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">{{ orderAddress.country_code }}{{
                                        orderAddress.phone
                                    }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.email')
                                    }}</span>
                                    <span class="text-sm font-normal">{{ orderAddress.email }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.address')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">
                                        <span v-if="orderAddress.address">{{ orderAddress.address }},</span>
                                        <span v-if="orderAddress.state">{{ orderAddress.state }},</span>
                                        <span v-if="orderAddress.city">{{ orderAddress.city }},</span>
                                        <span v-if="orderAddress.country">{{ orderAddress.country }},</span>
                                        <span v-if="orderAddress.zip_code">{{ orderAddress.zip_code }}</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div v-if="order.order_type === enums.orderTypeEnum.PICK_UP">
                    <div class="p-4 mb-6 rounded-2xl shadow-xs bg-white">
                        <h3 class="text-sm leading-6 font-medium mb-2">
                            {{ $t('label.pick_up_address') }}
                        </h3>
                        <div class="flex items-start justify-start gap-2.5">
                            <ul class="flex flex-col gap-2.5 border-t border-dashed border-gray-100">
                                <li class="flex flex-wrap sm:flex-nowrap gap-2 pt-4">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.name')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">{{ outletAddress.name }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2" v-if="outletAddress.phone">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.phone')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">{{ outletAddress.phone }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2" v-if="outletAddress.email">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{
                                        $t('label.email')
                                    }}</span>
                                    <span class="text-sm font-normal capitalize">{{ outletAddress.email }}</span>
                                </li>
                                <li class="flex flex-wrap sm:flex-nowrap gap-2">
                                    <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">
                                        {{ $t('label.address') }}
                                    </span>
                                    <span class="text-sm font-normal capitalize">
                                        <span v-if="outletAddress.address">{{ outletAddress.address }}</span>
                                        <span class="block" :class="outletAddress.address ? 'mt-2' : ''">
                                            <span v-if="outletAddress.city">{{ outletAddress.city }},</span>
                                            <span v-if="outletAddress.state">{{ outletAddress.state }},</span>
                                            <span v-if="outletAddress.zip_code">{{ outletAddress.zip_code }}</span>
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div v-if="parseInt(order.status) !== parseInt(enums.orderStatusEnum.REJECTED) && parseInt(order.status) !== parseInt(enums.orderStatusEnum.CANCELED)"
                    class="p-4 rounded-2xl shadow-xs bg-white">
                    <h3 class="capitalize font-medium text-sm leading-6 mb-2">{{ $t("label.payment_info") }}</h3>

                    <ul class="flex flex-col gap-2 border-t border-dashed border-gray-100">
                        <li class="flex items-center gap-2 pt-4">
                            <span class="capitalize text-sm leading-6">{{ $t("label.method") }}:</span>
                            <span class="capitalize text-sm leading-6">
                                {{ order.payment_method_name }}
                            </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="capitalize text-sm leading-6">{{ $t("label.status") }}:</span>
                            <span class="capitalize text-sm leading-6"
                                :class="enums.paymentStatusEnum.PAID === order.payment_status ? 'text-[#2AC769]' : 'text-[#FB4E4E]'">
                                {{ enums.paymentStatusEnumArray[order.payment_status] }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="w-full rounded-2xl shadow-xs bg-white">
                <div class="p-4 border-b">
                    <h3 class="font-medium text-sm leading-6 capitalize mb-4">{{ $t('label.order_details') }}</h3>
                    <div class="pl-3">
                        <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2"
                            v-if="orderProducts.length > 0" v-for="product in orderProducts" :key="product">
                            <div class="flex items-center gap-3 relative">
                                <h3
                                    class="absolute top-5 -left-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                    {{ product.quantity }}</h3>
                                <img class="w-16 h-16 rounded-lg flex-shrink-0" :src="product.product_image"
                                    alt="thumbnail">
                                <div class="flex-auto overflow-hidden">
                                    <h4 :class="!product.variation_names ? 'mb-4' : ''"
                                        class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                                        {{ product.product_name }}</h4>
                                    <p class="text-sm overflow-hidden">{{ product.variation_names }}</p>
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex items-center gap-8">
                                            <span class="text-sm font-semibold">{{
                                                product.total_currency_price
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div class="rounded-xl border border-[#EFF0F6]">
                        <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                            <li class="flex items-center justify-between">
                                <span class="text-sm leading-6 capitalize">{{ $t("label.subtotal") }}</span>
                                <span class="text-sm leading-6 capitalize">
                                    {{ order.subtotal_currency_price }}
                                </span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-sm leading-6 capitalize">{{ $t('label.tax_fee') }}</span>
                                <span class="text-sm leading-6 capitalize">{{ order.tax_currency_price }}</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-sm leading-6 capitalize">{{ $t("label.shipping_charge") }}</span>
                                <span class="text-sm leading-6 capitalize font-medium text-[#1AB759]">
                                    {{ order.shipping_charge_currency_price }}</span>
                            </li>
                            <li class="flex items-center justify-between">
                                <span class="text-sm leading-6 capitalize">{{ $t("label.discount") }}</span>
                                <span class="text-sm leading-6 capitalize">
                                    {{ order.discount_currency_price }}
                                </span>
                            </li>

                        </ul>
                        <div class="flex items-center justify-between p-3">
                            <h4 class="text-sm leading-6 font-semibold capitalize">{{ $t("label.total") }}</h4>
                            <h5 class="text-sm leading-6 font-semibold capitalize">
                                {{ order.total_currency_price }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>

import LoadingComponent from "./LoadingComponent";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import addressTypeEnum from "../../../enums/modules/addressTypeEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";
import OrderStatusComponent from "./OrderStatusComponent";
import appService from "../../../services/appService";

export default {
    name: "OrderDetailsComponent",
    components: { LoadingComponent, OrderStatusComponent },
    props: {
        order: Object,
        orderProducts: Object,
        orderAddresses: Object,
        outletAddress: Object,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                paymentStatusEnum: paymentStatusEnum,
                addressTypeEnum: addressTypeEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.CONFIRMED]: this.$t("label.confirmed"),
                    [orderStatusEnum.ON_THE_WAY]: this.$t("label.on_the_way"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                orderTypeEnumArray: {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.PICK_UP]: this.$t("label.pick_up"),
                    [orderTypeEnum.POS]: this.$t("label.pos")
                }
            },
        };
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);

        }
    }
}
</script>
