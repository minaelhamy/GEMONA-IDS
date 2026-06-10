<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('label.purchases') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('purchaselist-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <router-link @click="reset" v-if="permissionChecker('purchase_create')" to="purchase/add"
                        class="db-btn h-[37px] text-white bg-primary">
                        <i class="lab lab-line-add-circle"></i>
                        <span>{{ $t('button.add_purchase') }}</span>
                    </router-link>
                </div>
            </div>
            <div class="table-filter-div" id="purchaselist-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="supplier" class="db-field-title after:hidden">{{ $t('label.supplier') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="supplier"
                                v-model="props.search.supplier_id" :options="suppliers" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label class="db-field-title after:hidden">{{ $t('label.date') }}</label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" 
                                @update:modelValue="handleEndDate" v-model="props.search.date" :range="false" >
                            </Datepicker>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCode" class="db-field-title after:hidden">{{
                                $t('label.reference_no')
                            }}</label>
                            <input id="searchCode" v-model="props.search.reference_no" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="status" class="db-field-title after:hidden">{{ $t('label.status') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="status" v-model="props.search.status"
                                :options="enums.statusEnumArray" label-by="statusKey" value-by="statusValue"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="total" class="db-field-title after:hidden">{{ $t('label.total') }}</label>
                            <input id="total" v-model="props.search.total" v-on:keypress="floatNumber($event)" type="text"
                                class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="note" class="db-field-title after:hidden">{{ $t('label.note') }} </label>
                            <input id="note" v-model="props.search.note" type="text" class="db-field-control">
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

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.supplier') }}</th>
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reference_no') }}</th>
                            <th class="db-table-head-th">{{ $t('label.status') }}</th>
                            <th class="db-table-head-th">{{ $t('label.total') }}</th>
                            <th class="db-table-head-th">{{ $t('label.payment_status') }}</th>
                            <th v-if="permissionChecker('purchase_show') || permissionChecker('purchase_edit') || permissionChecker('purchase_delete')"
                                class="db-table-head-th hidden-print">{{ $t('label.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body border-b border-gray-200"  v-if="purchases.length > 0">
                        <tr class="db-table-body-tr" v-for="(purchase, index) of purchases" :key="index">
                            <td class="db-table-body-td font-medium">{{ purchase.supplier.name }}</td>
                            <td class="db-table-body-td">{{ purchase.converted_date }}</td>
                            <td class="db-table-body-td">{{ purchase.reference_no }}</td>
                            <td class="db-table-body-td"><span class="db-table-badge"
                                    :class="purchaseStatusClass(purchase.status)">{{
                                        enums.statusEnumObj[purchase.status] }}</span></td>
                            <td class="db-table-body-td">{{ purchase.total_flat_price }}</td>
                            <td class="db-table-body-td">
                                <span class="db-table-badge" :class="purchasePaymentStatusClass(purchase.payment_status)">
                                    {{ enums.purchasePaymentStatusEnumArray[purchase.payment_status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('purchase_show') || permissionChecker('purchase_edit') || permissionChecker('purchase_delete')">
                                <SmIconViewComponent :link="'admin.purchase.show'" :id="purchase.id"
                                    v-if="permissionChecker('purchase_show')" />
                                <SmIconEditComponent @click="edit(purchase)" :link="'admin.purchase.edit'" :id="purchase.id"
                                    v-if="permissionChecker('purchase_edit')" />
                                <SmIconDeleteComponent @click="destroy(purchase.id)"
                                    v-if="permissionChecker('purchase_delete')" />
                                <button type="button" data-modal="#purchasePayment" @click="addPayment(purchase.id)"
                                    class="db-table-action">
                                    <i class="lab lab-line-card text-blue-500 bg-blue-100"></i>
                                    <span class="db-tooltip">{{ $t('button.add_payment') }}</span>
                                </button>
                                <button type="button" data-modal="#purchasePaymentList" @click="paymentList(purchase.id)"
                                    class="db-table-action">
                                    <i class="lab lab lab-line-menu text-cyan-500 bg-cyan-100"></i>
                                    <span class="db-tooltip">{{ $t('button.view_payments') }}</span>
                                </button>

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
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="purchases.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>

        <div id="purchasePayment" class="modal">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $t("menu.add_payment") }}</h3>
                    <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                        @click="reset"></button>
                </div>
                <PurchasePaymentCreateComponent :search="props.search" v-if="show" />
            </div>
        </div>
        <div id="purchasePaymentList" class="modal">
            <div class="modal-dialog max-w-3xl">
                <div class="modal-header border-b-0">
                    <h3 class="modal-title">{{ $t("menu.purchase_payments") }}</h3>
                    <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                        @click="reset"></button>
                </div>
                <PurchasePaymentListComponent v-if="show" />
            </div>
        </div>
    </div>
</template>


<script lang="js">
import LoadingComponent from "../components/LoadingComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconEditComponent from '../components/buttons/SmIconEditComponent';
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import purchaseStatusEnum from "../../../enums/modules/purchaseStatusEnum";
import purchasePaymentStatusEnum from "../../../enums/modules/purchasePaymentStatusEnum";
import alertService from "../../../services/alertService";
import PurchasePaymentCreateComponent from "./PurchasePaymentCreateComponent";
import PurchasePaymentListComponent from "./PurchasePaymentListComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ENV from "../../../config/env";

export default {
    name: 'PurchaseListComponent',
    components: {
        PaginationBox,
        PaginationSMBox,
        PaginationTextComponent,
        TableLimitComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        ExportComponent,
        Datepicker,
        SmIconViewComponent,
        SmIconDeleteComponent,
        LoadingComponent,
        SmIconSidebarModalEditComponent,
        SmIconEditComponent,
        PurchasePaymentCreateComponent,
        PurchasePaymentListComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t('menu.purchase')
            },
            enums: {
                purchasePaymentStatusEnum: purchasePaymentStatusEnum,
                statusEnumArray: [
                    { statusValue: purchaseStatusEnum.PENDING, statusKey: this.$t('label.pending') },
                    { statusValue: purchaseStatusEnum.ORDERED, statusKey: this.$t('label.ordered') },
                    { statusValue: purchaseStatusEnum.RECEIVED, statusKey: this.$t('label.received') },
                ],
                statusEnumObj: {
                    [purchaseStatusEnum.PENDING]: this.$t("label.pending"),
                    [purchaseStatusEnum.ORDERED]: this.$t("label.ordered"),
                    [purchaseStatusEnum.RECEIVED]: this.$t("label.received"),
                },
                purchasePaymentStatusEnumArray: {
                    [purchasePaymentStatusEnum.PENDING]: this.$t("label.pending"),
                    [purchasePaymentStatusEnum.PARTIAL_PAID]: this.$t("label.partial_paid"),
                    [purchasePaymentStatusEnum.FULLY_PAID]: this.$t("label.fully_paid"),
                }
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    supplier_id: null,
                    date: "",
                    reference_no: "",
                    status: null,
                    total: null,
                    note: ""
                }
            },
            items: [],
            ENV: ENV
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch('supplier/lists', { page: 1 });
    },
    computed: {
        purchases: function () {
            return this.$store.getters['purchase/lists'];
        },
        pagination: function () {
            return this.$store.getters['purchase/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['purchase/page'];
        },
        suppliers: function () {
            return this.$store.getters['supplier/lists'];
        },
        show: function () {
            return this.$store.getters["purchase/temp"].temp_id;
        }
    },
    methods: {
        addPayment: function (id) {
            appService.modalShow('#purchasePayment');
            this.loading.isActive = true;
            this.$store.dispatch("purchase/payment", id);
            this.loading.isActive = false;
        },
        paymentList: function (id) {
            appService.modalShow('#purchasePaymentList');
            this.loading.isActive = true;
            this.$store.dispatch("purchase/payment", id);
            this.loading.isActive = false;
        },
        purchasePaymentStatusClass: function (status) {
            return appService.purchasePaymentStatusClass(status);
        },
        purchaseStatusClass: function (status) {
            return appService.purchaseStatusClass(status);
        },
        textShortener: function (text, number = 30) {
            text = appService.htmlTagRemover(text);
            return appService.textShortener(text, number);
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        search: function () {
            this.list();
        },
        handleEndDate: function (e) {
            if (e) {
                this.props.search.date = e;
            } else {
                this.props.search.date = null;

            }
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        decimalPoint: function (num) {
            return appService.decimalPoint(num);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        edit: function (purchase) {
            this.$store.dispatch('product/edit', purchase.id);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('purchase/lists', this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                })
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("purchase/destroy", {
                        id: id,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            null,
                            this.$t("menu.purchase")
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
        clear: function () {
            this.props.search = {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: 'id',
                order_type: 'desc',
                supplier_id: null,
                date: "",
                reference_no: "",
                status: null,
                total: null,
                note: ""
            },
                this.list();
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('purchase/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.purchase");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        reset: function () {
            this.$store.dispatch('purchase/reset').then().catch();
            appService.modalHide();
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
