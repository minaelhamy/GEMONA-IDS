<template>
    <LoadingComponent :props="loading" />
    <h2 class="capitalize text-2xl font-bold mb-2 text-primary">{{ $t('menu.overview') }}</h2>
    <p class="mb-7 font-medium capitalize">{{ $t('label.welcome_back') }}, {{ name }}!</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="p-3 rounded-2xl shadow-xs bg-white">
            <i
                class="lab-fill-bag bg-shopperz-pink shadow-pink w-10 h-10 leading-10 rounded-lg text-lg text-center text-white mb-6"></i>
            <h3 class="text-shopperz-pink text-2xl font-bold mb-1">{{ total_orders }}</h3>
            <p class="font-medium text-text">{{ this.$t('label.total_orders') }}</p>
        </div>
        <div class="p-3 rounded-2xl shadow-xs bg-white">
            <i
                class="lab-fill-bag-check bg-shopperz-orange shadow-orange w-10 h-10 leading-10 rounded-lg text-lg text-center text-white mb-6"></i>
            <h3 class="text-shopperz-orange text-2xl font-bold mb-1">{{ total_completed_orders }}</h3>
            <p class="font-medium text-text">{{ this.$t('label.total_completed') }}</p>
        </div>
        <div class="p-3 rounded-2xl shadow-xs bg-white">
            <i
                class="lab-fill-refresh bg-shopperz-purple shadow-purple w-10 h-10 leading-10 rounded-lg text-lg text-center text-white mb-6"></i>
            <h3 class="text-shopperz-purple text-2xl font-bold mb-1">{{ total_returned_orders }}</h3>
            <p class="font-medium text-text">{{ this.$t('label.total_returned') }}</p>
        </div>
        <div class="p-3 rounded-2xl shadow-xs bg-white">
            <i
                class="lab-fill-wallet bg-shopperz-blue shadow-blue w-10 h-10 leading-10 rounded-lg text-lg text-center text-white mb-6"></i>
            <h3 class="text-shopperz-blue text-2xl font-bold mb-1">{{ wallet_balance }}</h3>
            <p class="font-medium text-text">{{ this.$t('label.wallet_balance') }}</p>
        </div>
    </div>
    <div class="flex items-center justify-between mb-5">
        <h4 class="text-xl font-bold capitalize">{{ $t('label.order_history') }}</h4>
        <RouterLink :to="{ name: 'frontend.account.orderHistory' }"
            class="py-1 px-4 text-sm sm:py-2 sm:px-5 rounded-3xl capitalize sm:text-base font-semibold whitespace-nowrap bg-primary-slate text-primary transition-all duration-300 hover:bg-primary hover:text-white">
            {{ $t('label.show_full_history') }}
        </RouterLink>
    </div>
    <div class="rounded-2xl shadow-card bg-white mobile:mb-20">
        <div class="max-md:overflow-x-auto">
            <table class="w-full text-left text-sm capitalize">
                <thead class="font-semibold border-b-2 border-gray-200">
                    <tr>
                        <th class="p-4">{{ $t('label.order_id') }}</th>
                        <th class="p-4">{{ $t('label.products') }}</th>
                        <th class="p-4">{{ $t('label.status') }}</th>
                        <th class="p-4">{{ $t('label.payment') }}</th>
                        <th class="p-4">{{ $t('label.amount') }}</th>
                        <th class="p-4">{{ $t('label.action') }}</th>
                    </tr>
                </thead>
                <tbody class="font-medium" v-if="orders.length > 0">
                    <tr v-for="order in orders" :key="order">
                        <td class="p-4 border-t border-gray-100">
                            <h5 class="font-semibold mb-1">{{ order.order_serial_no }}</h5>
                            <p class="text-xs text-text">{{ order.order_datetime }}</p>
                        </td>
                        <td class="p-4 border-t border-gray-100">
                            {{ order.order_items }} {{ $t('label.product') }}
                        </td>
                        <td class="p-4 border-t border-gray-100">
                            <span class="db-table-badge" :class="orderStatusClass(order.status)">
                                {{ enums.orderStatusEnumArray[order.status] }}
                            </span>
                        </td>
                        <td class="p-4 border-t border-gray-100">
                            <span class="ext-xs px-2 py-1 rounded-full"
                                :class="enums.paymentStatusEnum.PAID === order.payment_status ? 'text-[#2AC769] bg-[#E2FFEE]' : 'text-[#FB4E4E] bg-[#FFE8E8]'">
                                {{ enums.paymentStatusEnumArray[order.payment_status] }}
                            </span>
                        </td>
                        <td class="p-4 border-t border-gray-100">{{ order.total_currency_price }}</td>
                        <td class="p-4 border-t border-gray-100">
                            <div class="relative group w-fit">
                                <button type="button">
                                    <i
                                        class="lab-fill-more-circle w-[30px] h-[30px] leading-[30px] text-center rounded-lg text-white bg-primary shadow-action lab-font-size-16"></i>
                                </button>
                                <nav
                                    class="rounded-lg shadow-md absolute top-8 ltr:right-0 rtl:left-0 z-30 border border-gray-100 bg-white transition-all duration-300 origin-top scale-y-0 group-hover:scale-y-100">
                                    <RouterLink
                                        :to="{ name: 'frontend.account.orderDetails', params: { id: order.id } }"
                                        class="block w-full text-left capitalize rounded-lg text-sm py-2 pl-3 pr-5 border-b last:border-b-0 border-gray-100 transition-all duration-500 hover:bg-gray-50 hover:text-primary">
                                        {{ $t('label.view') }}</RouterLink>
                                    <button type="button" @click.prevent="handleOrderReceiptModal(order)"
                                        class="block w-full text-left capitalize rounded-lg text-sm py-2 pl-3 pr-5 border-b last:border-b-0 border-gray-100 transition-all duration-500 hover:bg-gray-50 hover:text-primary">{{
                                            $t('label.download') }}</button>
                                </nav>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full" :src="setting.not_found" alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="orderReceiptPrint"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-500 opacity-0 invisible">
        <div class="w-full max-w-[302px] mx-auto bg-white transition-all duration-500" id="print">
            <div class="modal-header hidden-print">
                <button type="button" @click="reset"
                    class="modal-close flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#FB4E4E]">
                    <i class="lab lab-fill-close-circle lab-font-size-16 text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.close') }}</span>
                </button>
                <button type="button" v-print="printObj"
                    class="flex items-center justify-center gap-1.5 py-2 px-4 rounded bg-[#1AB759]">
                    <i class="lab lab-fill-printer lab-font-size-16 text-white"></i>
                    <span class="text-xs leading-5 capitalize text-white">{{ $t('button.print_invoice') }}</span>
                </button>
            </div>
            <OrderReceiptComponent v-if="orderId" :method="reset" :orderId="orderId" />
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import askEnum from "../../../../enums/modules/askEnum";
import appService from "../../../../services/appService";
import targetService from "../../../../services/targetService";
import orderStatusEnum from "../../../../enums/modules/orderStatusEnum";
import paymentStatusEnum from "../../../../enums/modules/paymentStatusEnum";
import OrderReceiptComponent from "../../components/OrderReceiptComponent";
import print from "vue3-print-nb";
export default {
    name: "OverviewComponent",
    components: { LoadingComponent, OrderReceiptComponent },
    directives: {
        print
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.order_receipt"),
            },
            enums: {
                paymentStatusEnum: paymentStatusEnum,
                orderStatusEnum: orderStatusEnum,
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
            },

            search: {
                paginate: 1,
                page: 1,
                per_page: 3,
                order_column: "id",
                order_by: "desc",
                active: askEnum.YES,

            },
            name: '',
            total_orders: '',
            total_completed_orders: '',
            total_returned_orders: '',
            wallet_balance: '',
            orderId: "",
        }
    },
    mounted() {
        const profile = this.$store.getters.authInfo;
        this.name = profile.name;
        this.totalOrders();
        this.totalCompletedOrders();
        this.totalReturnedOrders();
        this.walletBalance();
        this.list();
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        orders: function () {
            return this.$store.getters["frontendOrder/lists"];
        },
    },
    methods: {
        orderStatusClass: function (status) {
            return appService.orderStatusClass(status);
        },
        handleOrderReceiptModal: function (order) {
            this.orderId = order.id;
        },
        reset: function () {
            targetService.hideTarget("orderReceiptPrint", 'modal-active');
            setTimeout(() => {
                this.orderId = "";
            }, 500);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.search.page = page;
            this.$store.dispatch("frontendOrder/lists", {
                search: this.search
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        totalOrders: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOverview/totalOrders").then((res) => {
                this.total_orders = res.data.data.total_orders;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },

        totalCompletedOrders: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOverview/totalCompletedOrders").then((res) => {
                this.total_completed_orders = res.data.data.total_completed_orders;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },

        totalReturnedOrders: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOverview/totalReturnedOrders").then((res) => {
                this.total_returned_orders = res.data.data.total_returned_orders;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },

        walletBalance: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOverview/walletBalance").then((res) => {
                this.wallet_balance = res.data.data.wallet_balance;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    }
}
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>