<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('menu.sales_report') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('salesreport-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                            <PdfComponent :method="downloadPdf" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-filter-div" id="salesreport-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="order_id" class="db-field-title after:hidden">{{ $t('label.order_id') }}</label>
                            <input id="order_id" v-model="props.search.order_serial_no" type="text"
                                class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{
                                $t('label.status')
                                }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.orderStatusEnum.PENDING, name: $t('label.pending') },
                                    { id: enums.orderStatusEnum.CONFIRMED, name: $t('label.confirmed') },
                                    { id: enums.orderStatusEnum.ON_THE_WAY, name: $t('label.on_the_way') },
                                    { id: enums.orderStatusEnum.DELIVERED, name: $t('label.delivered') },
                                    { id: enums.orderStatusEnum.CANCELED, name: $t('label.canceled') },
                                    { id: enums.orderStatusEnum.REJECTED, name: $t('label.rejected') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{
                                $t('label.date')
                                }}</label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                                @update:modelValue="handleDate" v-model="modelValue" :range="true">
                            </Datepicker>
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{
                                $t('label.paid_status')
                                }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.payment_status" :options="[
                                    { id: enums.paymentStatusEnum.PAID, name: $t('label.paid') },
                                    { id: enums.paymentStatusEnum.UNPAID, name: $t('label.unpaid') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">{{
                                $t('label.payment_type')
                                }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.payment_method" :options="paymentGateways" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="source" class="db-field-title after:hidden">{{
                                $t('label.source')
                                }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="source"
                                v-model="props.search.source" :options="enums.sourceObject" label-by="name"
                                value-by="value" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t('button.search') }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t('button.clear') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row px-5 mt-5 mb-5">
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-box text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">
                                {{ $t('label.total_orders') }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ salesReportOverview.total_orders }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-dollar-circle text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{
                                $t('label.total_earnings') }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ salesReportOverview.total_earnings }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-ticket-discount text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{
                                $t('label.total_discounts')
                            }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ salesReportOverview.total_discounts }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-moneys text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">
                                {{ $t('label.total_shipping_charges') }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ salesReportOverview.total_shipping_charges
                                }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.order_id') }}</th>
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.total') }}</th>
                            <th class="db-table-head-th">{{ $t('label.discount') }}</th>
                            <th class="db-table-head-th">{{ $t('label.shipping_charge') }}</th>
                            <th class="db-table-head-th">{{ $t('label.payment_type') }}</th>
                            <th class="db-table-head-th">{{ $t('label.payment_status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="salesReports.length > 0">
                        <tr class="db-table-body-tr" v-for="salesReport in salesReports" :key="salesReport">
                            <td class="db-table-body-td">{{ salesReport.order_serial_no }}</td>
                            <td class="db-table-body-td">{{ salesReport.order_datetime }}</td>
                            <td class="db-table-body-td">{{ salesReport.total_amount_price }}</td>
                            <td class="db-table-body-td">{{ salesReport.discount_amount_price }}</td>
                            <td class="db-table-body-td">{{ salesReport.shipping_amount_price }}</td>
                            <td class="db-table-body-td">{{ salesReport.order_type ==
                                enums.orderTypeEnum.POS ?
                                salesReport.pos_payment_method_name : salesReport.payment_method_name }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(salesReport.payment_status)">
                                    {{ enums.paymentStatusEnumArray[salesReport.payment_status] }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="7">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full" :src="ENV.API_URL+'/images/default/not-found/not_found.png'" alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="salesReports.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import paymentStatusEnum from "../../../enums/modules/paymentStatusEnum";
import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import PdfComponent from "../components/buttons/export/PdfComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import sourceEnum from "../../../enums/modules/sourceEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum";
import ENV from "../../../config/env";

export default {
    name: "SalesReportListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        LoadingComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        Datepicker,
        SmIconViewComponent,
        PdfComponent
    },

    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                paymentStatusEnum: paymentStatusEnum,
                orderStatusEnum: orderStatusEnum,
                sourceEnum: sourceEnum,
                orderTypeEnum: orderTypeEnum,
                paymentStatusEnumArray: {
                    [paymentStatusEnum.PAID]: this.$t("label.paid"),
                    [paymentStatusEnum.UNPAID]: this.$t("label.unpaid")
                },
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.CONFIRMED]: this.$t("label.confirmed"),
                    [orderStatusEnum.ON_THE_WAY]: this.$t("label.on_the_way"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected")
                },
                sourceObject: [
                    {
                        name: this.$t("label.web"),
                        value: sourceEnum.WEB,
                    },
                    {
                        name: this.$t("label.app"),
                        value: sourceEnum.APP,
                    },
                    {
                        name: this.$t("label.pos"),
                        value: sourceEnum.POS,
                    },
                ],
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.sales_report')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    payment_status: null,
                    payment_method: null,
                    order_serial_no: "",
                    status: null,
                    from_date: "",
                    to_date: "",
                    source: null,
                }
            },
            modelValue: null,
            paymentGateways: [],
            ENV:ENV
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch("paymentGateway/lists", {
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        }).then((res) => {
            this.paymentGateways = res.data.data;
            this.paymentGateways = [
                { id: - posPaymentMethodEnum.CASH, name: this.$t("label.cash") },
                { id: - posPaymentMethodEnum.CARD, name: this.$t("label.card") },
                { id: - posPaymentMethodEnum.MOBILE_BANKING, name: this.$t("label.mobile_banking") },
                { id: - posPaymentMethodEnum.OTHER, name: this.$t("label.other") },
                ...this.paymentGateways
            ];
        });
    },
    computed: {
        salesReports: function () {
            return this.$store.getters['salesReport/lists'];
        },
        pagination: function () {
            return this.$store.getters['salesReport/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['salesReport/page'];
        },
        salesReportOverview: function () {
            return this.$store.getters['salesReport/salesReportOverview'];
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        search: function () {
            this.list();
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date = null;

            }
        },

        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.order_serial_no = "";
            this.props.search.payment_status = null;
            this.props.search.payment_method = null;
            this.props.search.status = null;
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.props.search.source = null;
            this.modelValue = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('salesReport/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });

            this.$store.dispatch('salesReport/salesReportOverview', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },

        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('salesReport/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.sales_report");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },

        downloadPdf: function () {
            this.loading.isActive = true;
            this.$store.dispatch("salesReport/exportPdf", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = "Sales-report.pdf";
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
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