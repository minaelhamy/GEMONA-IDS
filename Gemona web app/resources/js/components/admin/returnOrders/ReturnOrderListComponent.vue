<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.return_orders') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('return-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <router-link @click="reset" v-if="permissionChecker('return_order_create')" to="return-orders/create"
                        class="db-btn h-[37px] text-white bg-primary">
                        <i class="lab lab-line-add-circle"></i>
                        <span>{{ $t('button.add_return') }}</span>
                    </router-link>
                </div>
            </div>
            <div class="table-filter-div" id="return-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="customer" class="db-field-title after:hidden">{{ $t('label.customer') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="customer"
                                v-model="props.search.user_id" :options="customers" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="date" class="db-field-title after:hidden">{{ $t('label.date') }}</label>

                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" 
                                @update:modelValue="handleEndDate" v-model="props.search.date" :range="false" >
                            </Datepicker>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCode" class="db-field-title after:hidden">{{ $t('label.reference_no')
                            }}</label>
                            <input id="searchCode" v-model="props.search.reference_no" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="total" class="db-field-title after:hidden">{{ $t('label.total') }}</label>
                            <input id="total" v-model="props.search.total" v-on:keypress="floatNumber($event)" type="text"
                                class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="reason" class="db-field-title after:hidden">{{ $t('label.reason') }} </label>
                            <input id="reason" v-model="props.search.reason" type="text" class="db-field-control">
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
                            <th class="db-table-head-th">{{ $t('label.customer') }}</th>
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reference_no') }}</th>
                            <th class="db-table-head-th">{{ $t('label.total') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reason') }}</th>
                            <th v-if="permissionChecker('return_order_show') || permissionChecker('return_order_edit') || permissionChecker('return_order_delete')"
                                class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body border-b border-gray-200" v-if="returnOrders.length > 0">
                        <tr class="db-table-body-tr" v-for="(returnOrder, index) of returnOrders" :key="index">
                            <td class="db-table-body-td font-medium">{{ returnOrder.user.name }}</td>
                            <td class="db-table-body-td">{{ returnOrder.converted_date }}</td>
                            <td class="db-table-body-td">{{ returnOrder.reference_no }}</td>
                            <td class="db-table-body-td">{{ returnOrder.total_float_price }}</td>
                            <td class="db-table-body-td"><span v-html="textShortener(returnOrder.reason)"></span></td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('return_order_show') || permissionChecker('return_order_edit') || permissionChecker('return_order_delete')">
                                <SmIconViewComponent :link="'admin.return-order.show'" :id="returnOrder.id"
                                    v-if="permissionChecker('return_order_show')" />
                                <SmIconEditComponent @click="edit(returnOrder)" :link="'admin.return-order.edit'"
                                    :id="returnOrder.id" v-if="permissionChecker('return_order_edit')" />
                                <SmIconDeleteComponent @click="destroy(returnOrder.id)"
                                    v-if="permissionChecker('return_order_delete')" />
                            </td>
                        </tr>

                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">
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
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="returnOrders.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
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
import alertService from "../../../services/alertService";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ENV from "../../../config/env";
export default {
    name: 'ReturnOrderListComponent',
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
        SmIconEditComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t('menu.return_orders')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    user_id: null,
                    date: "",
                    reference_no: "",
                    total: null,
                    reason: ""
                }
            },
            items: [],
            ENV: ENV
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch('user/lists', { vuex: true, order_type: 'asc' });
    },
    computed: {
        returnOrders: function () {
            return this.$store.getters['returnOrder/lists'];
        },
        pagination: function () {
            return this.$store.getters['returnOrder/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['returnOrder/page'];
        },
        customers: function () {
            return this.$store.getters['user/lists'];
        },
    },
    methods: {
        textShortener: function (text, number = 30) {
            text = appService.htmlTagRemover(text);
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        edit: function (damage) {
            this.$store.dispatch('returnOrder/edit', damage.id);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('returnOrder/lists', this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                })
        },
        handleEndDate: function (e) {
            if (e) {
                this.props.search.date = e;
            } else {
                this.props.search.date = null;

            }
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("returnOrder/destroy", {
                        id: id,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            null,
                            this.$t("menu.return_orders")
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
                user_id: null,
                date: "",
                reference_no: "",
                total: null,
                reason: ""
            },
                this.list();
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('returnOrder/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.return_orders");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        reset: function () {
            this.$store.dispatch('returnOrder/reset').then().catch();
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
