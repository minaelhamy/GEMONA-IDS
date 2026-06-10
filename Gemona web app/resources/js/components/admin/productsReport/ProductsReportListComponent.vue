<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border">
                <h3 class="db-card-title">{{ $t('menu.products_report') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('productreport-filter')" />
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
            <div class="table-filter-div" id="productreport-filter">
                <form class="p-4 sm:p-5 mb-5 w-full d-block" @submit.prevent="search">
                    <div class="row">

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="product_category_id" class="db-field-title">{{
                                $t("label.category")
                                }}</label>

                            <vue-select class="db-field-control f-b-custom-select" id="product_category_id"
                                v-model="props.search.product_category_id" :options="productCategories"
                                label-by="option" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title">{{
                                $t("label.name")
                                }}</label>

                            <vue-select class="db-field-control f-b-custom-select" id="name" v-model="props.search.name"
                                :options="products" label-by="name" value-by="name" :closeOnSelect="true"
                                :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{
                                $t('label.date')
                                }}</label>


                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                                @update:modelValue="handleDate" v-model="modelValue" :range="true">
                            </Datepicker>

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
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-4">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-3d-square text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">
                                {{ $t('label.total_products') }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ productsReportOverview.total_products }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-4">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-variation text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{
                                $t('label.total_categories') }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ productsReportOverview.total_categories }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-4">
                    <div class="border flex items-center gap-4 p-4 rounded-lg">
                        <div class="bg-[#F7F7F7] w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="lab-fill-bag-tick text-[#6E7191] text-2xl lab-font-size-24"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{
                                $t('label.total_sold_quantity')
                            }}
                            </h3>
                            <h4 class="font-bold text-lg text-[#6E7191]">{{ productsReportOverview.total_sold_quantity
                                }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.sold_quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="productsReports.length > 0">
                        <tr class="db-table-body-tr" v-for="productsReport in productsReports" :key="productsReport">
                            <td class="db-table-body-td">{{ productsReport.name }}</td>
                            <td class="db-table-body-td">{{ productsReport.category_name }}</td>
                            <td class="db-table-body-td">{{ productsReport.order }}</td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="3">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full" :src="ENV.API_URL+'/images/default/not-found/not_found.png'" alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot class="db-table-foot border-t" v-if="productsReports.length > 0">
                        <tr>
                            <td class="db-table-body-td">{{ $t('label.total') }}</td>
                            <td></td>
                            <td class="db-table-body-td"> {{ subTotal(productsReports) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="productsReports.length > 0">
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
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import PdfComponent from "../components/buttons/export/PdfComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ENV from "../../../config/env";

export default {
    name: "ProductsReportListComponent",
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
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.products_report')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    name: null,
                    product_category_id: null,
                    from_date: "",
                    to_date: "",
                }
            },
            productSearch: {
                paginate: 0,
                page: 1,
                order_column: 'id',
            },
            modelValue: null,
            productCategories: [],
            ENV:ENV
        }
    },
    mounted() {
        this.list();
        this.loading.isActive = true;
        this.props.search.page = 1;
        this.$store.dispatch('product/getSimpleProduct', this.productSearch).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
        this.$store.dispatch('productCategory/depthTrees').then((res) => {
            this.productCategories = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        productsReports: function () {
            return this.$store.getters['productsReport/lists'];
        },
        productsReportOverview: function () {
            return this.$store.getters['productsReport/productsReportOverview'];
        },
        products: function () {
            return this.$store.getters['product/simpleList'];
        },
        pagination: function () {
            return this.$store.getters['productsReport/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['productsReport/page'];
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
        subTotal(products) {
            return products.reduce((acc, ele) => {
                return acc + parseInt(ele.order);
            }, 0);
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.name = null;
            this.props.search.product_category_id = null;
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.modelValue = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('productsReport/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.$store.dispatch('productsReport/productsReportOverview', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },

        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('productsReport/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.products_report");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        downloadPdf: function () {
            this.loading.isActive = true;
            this.$store.dispatch("productsReport/exportPdf", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = "Product-report.pdf";
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