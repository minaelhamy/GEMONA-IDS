<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card p-4">
            <div class="flex flex-wrap gap-y-5 items-end justify-between">
                <div>
                    <div class="flex flex-wrap items-start gap-y-2 gap-x-6 mb-5">
                        <p class="text-2xl font-medium">{{ $t('label.order_id') }}:
                            <span class="text-heading">
                                #{{ order.order_serial_no }}
                            </span>
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span
                                :class="'text-sm capitalize h-5 leading-5 px-2 rounded-3xl text-[#FB4E4E] bg-[#FFDADA]' + statusClass(order.payment_status)">
                                {{ paymentStatusEnumArray[order.payment_status] }}
                            </span>
                            <span :class="'text-sm capitalize px-2 rounded-3xl ' + orderStatusClass(order.status)">
                                {{ orderStatusEnumArray[order.status] }}
                            </span>
                        </div>
                    </div>
                    <ul class="flex flex-col gap-2">
                        <li class="flex items-center gap-2">
                            <i class="lab lab-line-calendar lab-font-size-16"></i>
                            <span class="text-xs">{{ order.order_datetime }}</span>
                        </li>
                        <li class="text-xs">
                            {{ $t('label.payment_type') }}:
                            <span class="text-heading">
                                {{ order.payment_method_name }}
                            </span>
                        </li>
                        <li class="text-xs">
                            {{ $t('label.order_type') }}:
                            <span class="text-heading">
                                {{ orderTypeEnumArray[order.order_type] }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="flex flex-wrap gap-3" v-if="order.status === enums.orderStatusEnum.PENDING">
                    <OnlineOrderReasonComponent />
                    <button type="button" @click="changeStatus(enums.orderStatusEnum.CONFIRMED)"
                        class="flex items-center justify-center text-white gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                        <i class="lab lab-fill-save"></i>
                        <span class="text-sm capitalize text-white">{{ $t('button.accept') }}</span>
                    </button>
                </div>

                <div class="flex flex-wrap gap-3"
                    v-else-if="order.status !== enums.orderStatusEnum.REJECTED && order.status !== enums.orderStatusEnum.CANCELED">
                    <div class="relative">
                        <select v-model="payment_status" @change="changePaymentStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="paymentStatus in paymentStatusObject" :value="paymentStatus.value">
                                {{ paymentStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <div class="relative" v-if="order.order_type === enums.orderTypeEnum.DELIVERY">
                        <select v-model="order_status" @change="orderStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="orderStatus in orderStatusObject" :value="orderStatus.value">
                                {{ orderStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <div class="relative" v-if="order.order_type === enums.orderTypeEnum.PICK_UP">
                        <select v-model="order_status" @change="orderStatus($event)"
                            class="text-sm capitalize appearance-none pl-4 pr-10 h-[38px] rounded border border-primary bg-white text-primary">
                            <option v-for="orderStatus in orderStatusPickupObject" :value="orderStatus.value">
                                {{ orderStatus.name }}
                            </option>
                        </select>
                        <i
                            class="lab lab-line-chevron-down lab-font-size-16 absolute top-1/2 right-3.5 -translate-y-1/2 text-primary"></i>
                    </div>

                    <OnlineOrderReceiptComponent :order="order" :orderProducts="orderProducts" :orderUser="orderUser"
                        :orderAddress="orderAddress" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 sm:col-6">
        <div class="row">
            <div class="col-12">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.order_details') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <div class="pl-3">
                            <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2"
                                v-if="orderProducts.length > 0" v-for="product in orderProducts" :key="product">
                                <div class="flex items-center gap-3 relative">
                                    <h3
                                        class="absolute top-5 ltr:-left-3 rtl:-right-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
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
                                                    product.subtotal_currency_price
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="order.status === enums.orderStatusEnum.REJECTED">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">{{ $t('label.reason') }}</h3>
                    </div>
                    <div class="db-card-body">
                        <p>{{ order.reason }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-12 sm:col-6">
        <div class="row">
            <div class="col-12">
                <div class="db-card p-1">
                    <ul class="flex flex-col gap-2 p-3 border-b border-dashed border-[#EFF0F6]">
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.subtotal') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.subtotal_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.tax_fee') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.tax_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.discount') }}</span>
                            <span class="text-sm leading-6 capitalize">{{ order.discount_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between text-heading">
                            <span class="text-sm leading-6 capitalize">{{ $t('label.shipping_charge') }}</span>
                            <span class="text-sm leading-6 capitalize font-semibold text-[#1AB759]">
                                {{ order.shipping_charge_currency_price }}
                            </span>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between p-3">
                        <h4 class="text-sm leading-6 font-bold capitalize">{{ $t('label.total') }}</h4>
                        <h5 class="text-sm leading-6 font-bold capitalize">
                            {{ order.total_currency_price }}
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="order.order_type === enums.orderTypeEnum.DELIVERY && orderAddress.length > 0"
                v-for="address in orderAddress">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title" v-if="address.address_type === enums.addressTypeEnum.SHIPPING">
                            {{ $t('label.shipping_address') }}
                        </h3>
                        <h3 class="db-card-title" v-if="address.address_type === enums.addressTypeEnum.BILLING">
                            {{ $t('label.billing_address') }}
                        </h3>
                    </div>
                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 rounded-full" alt="avatar" :src="orderUser.image">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(address.full_name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="address.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ address.email }}</span>
                            </li>
                            <li class="flex items-center gap-2.5" v-if="address.phone">
                                <i class="lab lab-line-call-calling lab-font-size-14"></i>
                                <span class="text-xs" dir="ltr">{{ address.country_code + '' + address.phone }}</span>
                            </li>

                            <li class="flex items-center gap-2.5">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs">
                                    <span v-if="address.address">{{ address.address }},</span>
                                    <span v-if="address.city">{{ address.city }},</span>
                                    <span v-if="address.state">{{ address.state }},</span>
                                    <span v-if="address.country">{{ address.country }},</span>
                                    <span v-if="address.zip_code">{{ address.zip_code }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-12" v-else-if="order.order_type === enums.orderTypeEnum.PICK_UP">
                <div class="db-card">
                    <div class="db-card-header">
                        <h3 class="db-card-title">
                            {{ $t('label.pick_up_address') }}
                        </h3>
                    </div>

                    <div class="db-card-body">
                        <div class="flex items-center gap-3 mb-4">
                            <img class="w-8 rounded-full" alt="avatar" :src="orderUser.image">
                            <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                {{ textShortener(orderUser.name, 20) }}
                            </h4>
                        </div>
                        <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                            <li v-if="orderUser.email" class="flex items-center gap-2.5">
                                <i class="lab lab-line-mail lab-font-size-14"></i>
                                <span class="text-xs">{{ orderUser.email }}</span>
                            </li>
                            <li v-if="orderUser.phone" class="flex items-center gap-2.5">
                                <i class="lab lab-line-call-calling lab-font-size-14"></i>
                                <span class="text-xs" dir="ltr">{{ orderUser.country_code + '' + orderUser.phone
                                    }}</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <i class="lab lab-line-location lab-font-size-14"></i>
                                <span class="text-xs">
                                    <span v-if="outletAddress.address">{{ outletAddress.address }},</span>
                                    <span v-if="outletAddress.city">{{ outletAddress.city }},</span>
                                    <span v-if="outletAddress.state">{{ outletAddress.state }},</span>
                                    <span v-if="outletAddress.country">{{ outletAddress.country }},</span>
                                    <span v-if="outletAddress.zip_code">{{ outletAddress.zip_code }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import appService from "../../../services/appService";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";
import addressTypeEnum from "../../../enums/modules/addressTypeEnum";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import alertService from "../../../services/alertService";
import OnlineOrderReasonComponent from "./OnlineOrderReasonComponent";
import OnlineOrderReceiptComponent from "./OnlineOrderReceiptComponent";

export default {
    name: "OnlineOrderShowComponent",
    components: {
        OnlineOrderReceiptComponent,
        LoadingComponent,
        OnlineOrderReasonComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            payment_status: null,
            delivery_boy: null,
            order_status: null,
            enums: {
                paymentStatusEnum: paymentStatusEnum,
                addressTypeEnum: addressTypeEnum,
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
            }
        }
    },
    computed: {
        order: function () {
            return this.$store.getters['onlineOrder/show'];
        },
        orderProducts: function () {
            return this.$store.getters['onlineOrder/orderProducts'];
        },
        orderUser: function () {
            return this.$store.getters['onlineOrder/orderUser'];
        },
        orderAddress: function () {
            return this.$store.getters['onlineOrder/orderAddress'];
        },
        outletAddress: function () {
            return this.$store.getters['onlineOrder/outletAddress'];
        },

        paymentStatusEnumArray: function() {
            return {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
            }
        },
        paymentStatusObject: function() {
            return [
                    {
                        name: this.$t("label.paid"),
                        value: paymentStatusEnum.PAID
                    },
                    {
                        name: this.$t("label.unpaid"),
                        value: paymentStatusEnum.UNPAID,
                    },
            ]
        },
        orderStatusEnumArray: function() {
            return  {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.CONFIRMED]: this.$t("label.confirmed"),
                    [orderStatusEnum.ON_THE_WAY]: this.$t("label.on_the_way"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                }
        },
        orderStatusObject: function(){
          return   [
                    {
                        name: this.$t("label.confirmed"),
                        value: orderStatusEnum.CONFIRMED,
                    },
                    {
                        name: this.$t("label.on_the_way"),
                        value: orderStatusEnum.ON_THE_WAY,
                    },
                    {
                        name: this.$t("label.delivered"),
                        value: orderStatusEnum.DELIVERED,
                    },
                ]
        },
        orderStatusPickupObject: function(){
            return [
                    {
                        name: this.$t("label.confirmed"),
                        value: orderStatusEnum.CONFIRMED,
                    },
                    {
                        name: this.$t("label.delivered"),
                        value: orderStatusEnum.DELIVERED,
                    },
                ]
        },
        orderTypeEnumArray: function () {
            return {
                    [orderTypeEnum.DELIVERY]: this.$t("label.delivery"),
                    [orderTypeEnum.PICK_UP]: this.$t("label.pick_up")
                }
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('onlineOrder/show', this.$route.params.id).then(res => {
            this.payment_status = res.data.data.payment_status;
            this.order_status = res.data.data.status;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        changeStatus: function (status) {
            appService.acceptOrder().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("onlineOrder/changeStatus", {
                        id: this.$route.params.id,
                        status: status,
                    }).then((res) => {
                        this.order_status = res.data.data.status;
                        this.loading.isActive = false;
                        alertService.successFlip(
                            1,
                            this.$t("label.status")
                        );
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        changePaymentStatus: function (e) {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("onlineOrder/changePaymentStatus", {
                    id: this.$route.params.id,
                    payment_status: e.target.value,
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        1,
                        this.$t("label.status")
                    );
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
        orderStatus: function (e) {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("onlineOrder/changeStatus", {
                    id: this.$route.params.id,
                    status: e.target.value,
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        1,
                        this.$t("label.status")
                    );
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
    }

}
</script>