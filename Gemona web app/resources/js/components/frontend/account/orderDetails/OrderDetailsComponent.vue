<template>
    <LoadingComponent :props="loading" />
    <div class="flex items-center gap-4 mb-7">
        <button @click.prevent="$router.back()" class="lab-line-undo text-xl font-bold text-primary"></button>
        <h2 class="capitalize text-xl font-bold text-primary">{{ $t('label.order_details') }}</h2>
    </div>

    <div class="rounded-2xl shadow-card p-4 sm:p-6 mb-6 bg-white">
        <div class="text-center mb-10">
            <h3 class="text-xl font-semibold capitalize mb-4">{{ $t('message.thank_you') }}</h3>
            <p class="text-sm font-medium mb-3">{{ $t('message.order_status_follows') }}</p>
            <h4 class="text-sm font-semibold">{{ $t('label.order_id') }}: <span class="text-primary">#
                    {{ order.order_serial_no }}
                </span>
            </h4>

            <ul v-if="order.status !== enums.orderStatusEnum.CANCELED && order.status !== enums.orderStatusEnum.REJECTED && order.order_type !== enums.orderTypeEnum.PICK_UP"
                class="w-full flex items-center justify-center pb-12 mt-8">
                <li v-for="track in tracks" class="w-full flex items-center justify-center gap-1 relative">
                    <hr :class="{ '!bg-success': track.step <= order.status }"
                        class="block border-none w-full h-1 rounded-xl bg-gray-200" />
                    <i :class="{ 'lab-fill-save !bg-success text-white': track.step <= order.status }"
                        class="flex-shrink-0 w-7 h-7 leading-7 text-center rounded-full bg-gray-200 lab-font-size-16" />
                    <hr :class="{ '!bg-success': track.step <= order.status }"
                        class="block border-none w-full h-1 rounded-xl bg-gray-200" />
                    <span
                        class="absolute top-10 left-1/2 -translate-x-1/2 w-14 sm:w-20 text-xs sm:text-sm leading-[18px] text-center capitalize">
                        {{ track.title }}</span>
                </li>
            </ul>

            <ul v-if="order.status !== enums.orderStatusEnum.CANCELED && order.status !== enums.orderStatusEnum.REJECTED && order.order_type === enums.orderTypeEnum.PICK_UP"
                class="w-full flex items-center justify-center pb-12 mt-8">
                <li v-for="track in pickupTracks" class="w-full flex items-center justify-center gap-1 relative">
                    <hr :class="{ '!bg-success': track.step <= order.status }"
                        class="block border-none w-full h-1 rounded-xl bg-gray-200" />
                    <i :class="{ 'lab-fill-save !bg-success text-white': track.step <= order.status }"
                        class="flex-shrink-0 w-7 h-7 leading-7 text-center rounded-full bg-gray-200 lab-font-size-16" />
                    <hr :class="{ '!bg-success': track.step <= order.status }"
                        class="block border-none w-full h-1 rounded-xl bg-gray-200" />
                    <span
                        class="absolute top-10 left-1/2 -translate-x-1/2 w-14 sm:w-20 text-xs sm:text-sm leading-[18px] text-center capitalize">
                        {{ track.title }}</span>
                </li>
            </ul>

            <button v-if="order.status === enums.orderStatusEnum.CANCELED" type="button"
                class="flex items-center justify-center gap-2 py-3 sm:py-4 px-7 sm:px-10 mx-auto mt-6 rounded-2xl border border-[#FB4E4E] text-[#FB4E4E] bg-white transition-all duration-500 hover:bg-[#FB4E4E] hover:text-white">
                <i class="lab-fill-close-circle sm:text-xl"></i>
                <span class="sm:text-lg font-bold capitalize whitespace-nowrap">{{ $t('label.order_cancelled') }}</span>
            </button>
            <button v-if="order.status === enums.orderStatusEnum.REJECTED" type="button"
                class="flex items-center justify-center gap-2 py-3 sm:py-4 px-7 sm:px-10 mx-auto mt-6 rounded-2xl border border-[#FB4E4E] text-[#FB4E4E] bg-white transition-all duration-500 hover:bg-[#FB4E4E] hover:text-white">
                <i class="lab-fill-close-circle sm:text-xl"></i>
                <span class="sm:text-lg font-bold capitalize whitespace-nowrap">{{ $t('label.order_rejected') }}</span>
            </button>
        </div>

        <div class="row">
            <div class="col-12 md:col-5">
                <div class="p-4 mb-6 rounded-2xl border border-gray-100">
                    <ul class="flex flex-col gap-2.5">
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.order_id')
                            }}:</span>
                            <span class="text-sm font-semibold capitalize">#{{ order.order_serial_no }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.order_date')
                            }}:</span>
                            <span class="text-sm font-normal capitalize">{{ order.order_datetime }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.order_type')
                            }}:</span>
                            <span>
                                {{ enums.orderTypeEnumArray[order.order_type] }}
                            </span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.order_status')
                            }}:</span>
                            <span class="font-sm capitalize px-2 rounded-3xl" :class="orderStatusClass(order.status)">
                                {{ enums.orderStatusEnumArray[order.status] }}
                            </span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.payment_status')
                            }}:</span>
                            <span class="font-sm capitalize px-2 rounded-3xl"
                                :class="enums.paymentStatusEnum.PAID === order.payment_status ? 'text-[#2AC769] bg-[#E2FFEE]' : 'text-[#FB4E4E] bg-[#FFE8E8]'">
                                {{ enums.paymentStatusEnumArray[order.payment_status] }}
                            </span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.payment_method')
                            }}:</span>
                            <span class="text-sm font-normal capitalize">
                                {{ order.payment_method_name }}
                            </span>
                        </li>
                        <li v-if="order.status === enums.orderStatusEnum.REJECTED" class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-28 flex-shrink-0">{{
                                $t('label.reason')
                            }}:</span>
                            <span class="text-sm font-normal capitalize">
                                {{ order.reason }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-gray-100"
                    v-if="order.order_type === enums.orderTypeEnum.DELIVERY && orderAddress.length > 0"
                    v-for="address in orderAddress" :key="address"
                    :class="address.address_type === enums.addressTypeEnum.SHIPPING ? 'mb-6' : ''">
                    <h3 class="p-4 capitalize font-bold text-center"
                        v-if="address.address_type === enums.addressTypeEnum.SHIPPING">{{ $t('label.shipping_address')
                        }}
                    </h3>
                    <h3 class="p-4 capitalize font-bold text-center"
                        v-if="address.address_type === enums.addressTypeEnum.BILLING">{{ $t('label.billing_address') }}
                    </h3>
                    <ul class="p-4 flex flex-col gap-2.5 border-t border-dashed border-gray-100">
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.name')
                                }}:</span>
                            <span class="text-sm font-normal capitalize">{{ address.full_name }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.phone')
                                }}:</span>
                            <span class="text-sm font-normal capitalize" dir="ltr">{{ address.country_code }} {{
                                address.phone
                            }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2" v-if="address.email">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.email')
                                }}:</span>
                            <span class="text-sm font-normal">{{ address.email }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">
                                {{ $t('label.address') }}:</span>
                            <span class="text-sm font-normal capitalize">
                                <span v-if="address.address">{{ address.address }}</span>
                                <span class="block" :class="address.address ? 'mt-2' : ''">
                                    <span v-if="address.city">{{ address.city }},</span>
                                    <span v-if="address.state">{{ address.state }},</span>
                                    <span v-if="address.country">{{ address.country }},</span>
                                    <span v-if="address.zip_code">{{ address.zip_code }}</span>
                                </span>
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-gray-100"
                    v-if="order.order_type === enums.orderTypeEnum.PICK_UP && Object.keys(outletAddress).length > 0">
                    <h3 class="p-4 capitalize font-bold text-center">
                        {{ $t('label.pick_up_address') }}
                    </h3>
                    <ul class="p-4 flex flex-col gap-2.5 border-t border-dashed border-gray-100">
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.name')
                                }}:</span>
                            <span class="text-sm font-normal capitalize">{{ outletAddress.name }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2" v-if="outletAddress.phone">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.phone')
                                }}:</span>
                            <span class="text-sm font-normal capitalize" dir="ltr">{{ outletAddress.country_code }}
                                {{ outletAddress.phone }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2" v-if="outletAddress.email">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">{{ $t('label.email')
                                }}:</span>
                            <span class="text-sm font-normal">{{ outletAddress.email }}</span>
                        </li>
                        <li class="flex flex-wrap sm:flex-nowrap gap-2">
                            <span class="text-sm font-semibold capitalize w-20 flex-shrink-0">
                                {{ $t('label.address') }}:</span>
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
            <div class="col-12 md:col-7">
                <div class="rounded-2xl border border-gray-100">
                    <h3 class="p-4 capitalize font-bold text-center">{{ $t('label.order_summary') }}</h3>
                    <ul class="border-b border-t border-dashed border-gray-100">
                        <li v-for="orderProduct in orderProducts" :key="orderProduct"
                            class="py-4 mx-4 flex gap-3 border-b last:border-0 border-dashed border-gray-100">
                            <img :src="orderProduct.product_image" alt="product"
                                class="w-14 h-14 object-cover rounded-md flex-shrink-0" />
                            <div class="flex-auto overflow-hidden">
                                <h4 :class="!orderProduct.variation_names ? 'mb-4' : ''"
                                    class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                                    {{ orderProduct.product_name }}</h4>
                                <p class="text-sm overflow-hidden">{{ orderProduct.variation_names }}</p>
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-center gap-8">
                                        <span class="text-sm font-semibold">
                                            {{ orderProduct.subtotal_currency_price }}
                                        </span>
                                        <span class="text-sm font-medium">
                                            {{ $t('label.quantity') }}: {{ orderProduct.quantity }}
                                        </span>
                                    </div>
                                    <router-link
                                        v-if="order.status === enums.orderStatusEnum.DELIVERED && !orderProduct.product_user_review"
                                        :to="{ name: 'frontend.account.productReview', params: { slug: orderProduct.product_slug } }"
                                        class="text-sm font-semibold capitalize py-1 px-3 rounded-full whitespace-nowrap bg-[#FFF4F1] text-primary transition-all duration-500 hover:text-white hover:bg-primary">
                                        {{ $t('button.write_review') }}
                                    </router-link>

                                    <router-link
                                        v-if="order.status === enums.orderStatusEnum.DELIVERED && orderProduct.product_user_review"
                                        :to="{ name: 'frontend.account.productReview.edit', params: { slug: orderProduct.product_slug, id: orderProduct.product_user_review_id } }"
                                        class="text-sm font-semibold capitalize py-1 px-3 rounded-full whitespace-nowrap bg-[#FFF4F1] text-primary transition-all duration-500 hover:text-white hover:bg-primary">
                                        {{ $t('button.edit_review') }}
                                    </router-link>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <ul class="flex flex-col gap-2 py-4 mx-4 border-b border-dashed border-[#EFF0F6]">
                        <li class="flex items-center justify-between">
                            <span class="capitalize">{{ $t('label.subtotal') }}</span>
                            <span class="font-medium">{{ order.subtotal_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="capitalize">{{ $t('label.tax_fee') }}</span>
                            <span class="font-medium">{{ order.tax_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="capitalize">{{ $t('label.shipping_charge') }}</span>
                            <span class="font-medium">{{ order.shipping_charge_currency_price }}</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="capitalize">{{ $t('label.discount') }}</span>
                            <span class="font-medium">{{ order.discount_currency_price }}</span>
                        </li>
                    </ul>
                    <div class="flex items-center justify-between py-3 px-4">
                        <span class="capitalize font-bold">{{ $t('label.total') }}</span>
                        <span class="capitalize font-bold">{{ order.total_currency_price }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="order.status !== enums.orderStatusEnum.CANCELED" class="flex flex-wrap gap-6 mobile:mb-20">
        <OrderReceiptComponent :order="order" :orderProducts="orderProducts" :orderUser="orderUser"
            :orderAddress="orderAddress[0]" />
        <button v-if="order.status === enums.orderStatusEnum.PENDING"
            @click="changeStatus(enums.orderStatusEnum.CANCELED)" type="button"
            class="px-6 py-3 capitalize rounded-full whitespace-nowrap text-center font-semibold border border-danger text-danger bg-white">
            {{ $t('button.cancel_order') }}
        </button>
        <router-link v-if="order.status === enums.orderStatusEnum.DELIVERED && order.return_and_refund"
            :to="{ name: 'frontend.account.returnOrder.request', params: { id: order.id } }"
            class="px-6 py-3 capitalize rounded-full whitespace-nowrap text-center font-semibold border border-danger text-danger bg-white">
            {{ $t('button.return_request') }}</router-link>
    </div>

    <div id="payment-modal"
        :class="Object.keys(route.query).length > 0 && route.query.status === 'success' && cart.length > 0 ? 'modal-active' : ''"
        class=" fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-[360px]">
            <div class="px-4 py-5 relative">
                <button @click.prevent="reset" type="button"
                    class="lab-line-circle-cross text-lg text-[#E93C3C] absolute top-3 right-3"></button>
                <h3 class="font-medium text-center mb-5">{{ $t('message.thank_you_for_your_order') }}</h3>
                <img :src="setting.image_confirm" alt="confirm-image" class="w-[120px] mx-auto mb-5" />
                <h4 class="font-semibold text-center mb-5">{{ $t('message.your_order_is_successfully_placed') }}</h4>
                <button type="button" @click.prevent="reset" class="field-button font-semibold normal-case">{{
                    $t('button.see_your_order_details') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum";
import addressTypeEnum from "../../../../enums/modules/addressTypeEnum";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import targetService from "../../../../services/targetService";
import { useRoute } from 'vue-router'
import OrderReceiptComponent from "./OrderReceiptComponent";
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";

export default {
    name: "OrderDetailsComponent",
    components: { LoadingComponent, OrderReceiptComponent },
    setup() {
        const route = useRoute();
        return { route: route };
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            tracks: [
                { step: 1, title: this.$t('label.order_pending') },
                { step: 5, title: this.$t('label.order_confirmed') },
                { step: 7, title: this.$t('label.order_on_the_way') },
                { step: 10, title: this.$t('label.order_delivered') },
            ],
            pickupTracks: [
                { step: 1, title: this.$t('label.order_pending') },
                { step: 5, title: this.$t('label.order_confirmed') },
                { step: 10, title: this.$t('label.order_delivered') },
            ],
            enums: {
                orderStatusEnum: orderStatusEnum,
                paymentStatusEnum: paymentStatusEnum,
                addressTypeEnum: addressTypeEnum,
                orderTypeEnum: orderTypeEnum,
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
                    [orderTypeEnum.PICK_UP]: this.$t("label.pick_up")
                }
            },

        };
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        order: function () {
            return this.$store.getters['frontendOrder/show'];
        },
        orderProducts: function () {
            return this.$store.getters['frontendOrder/orderProducts'];
        },
        orderUser: function () {
            return this.$store.getters['frontendOrder/orderUser'];
        },
        orderAddress: function () {
            return this.$store.getters['frontendOrder/orderAddress'];
        },
        outletAddress: function () {
            return this.$store.getters['frontendOrder/outletAddress'];
        },
        cart: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        paymentMethod: function () {
            return this.$store.getters['frontendCart/paymentMethod'];
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOrder/show", this.$route.params.id).then(res => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
        showTarget: function (id, cClass) {
            targetService.showTarget(id, cClass);
        },
        reset: function () {
            if (this.cart.length > 0 && Object.keys(this.paymentMethod).length > 0 && this.paymentMethod.slug === 'credit') {
                this.$store.dispatch("profile").then().catch();
            }
            this.$store.dispatch("frontendCart/resetCart").then().catch();
        },
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        changeStatus: function (status) {
            appService.cancelOrder().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("frontendOrder/changeStatus", {
                        id: this.$route.params.id,
                        status: status,
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
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    }
}
</script>