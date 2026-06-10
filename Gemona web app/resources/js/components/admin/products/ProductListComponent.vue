<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.products') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('product-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <div class="dropdown-group">
                        <ImportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <SampleFileComponent :method="download" />
                            <UploadFileComponent props="productFileUloadModal" />
                        </div>
                    </div>
                    <ProductCreateComponent :props="props" v-if="permissionChecker('products_create')" />
                </div>
            </div>

            <div class="table-filter-div" id="product-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchName" class="db-field-title after:hidden">
                                {{ $t("label.name") }}
                            </label>
                            <input id="searchName" v-model="props.search.name" type="text" class="db-field-control" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchSku" class="db-field-title after:hidden">
                                {{ $t("label.sku") }}
                            </label>
                            <input id="searchSku" v-model="props.search.sku" type="text" class="db-field-control" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchBuyingPrice" class="db-field-title after:hidden">
                                {{ $t("label.buying_price") }}
                            </label>
                            <input id="searchBuyingPrice" v-on:keypress="numberOnly($event)"
                                v-model="props.search.buying_price" type="text" class="db-field-control" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchSellingPrice" class="db-field-title after:hidden">
                                {{ $t("label.selling_price") }}
                            </label>
                            <input id="searchSellingPrice" v-on:keypress="numberOnly($event)"
                                v-model="props.search.selling_price" type="text" class="db-field-control" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCategory" class="db-field-title">
                                {{ $t("label.category") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchCategory"
                                v-model="props.search.product_category_id" :options="productCategories"
                                label-by="option" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchBrand" class="db-field-title">
                                {{ $t("label.brand") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchBrand"
                                v-model="props.search.product_brand_id" :options="productBrands" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchBarcode" class="db-field-title">
                                {{ $t("label.barcode") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchBarcode"
                                v-model="props.search.barcode_id" :options="barcodes" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchTax" class="db-field-title">
                                {{ $t("label.tax") }}
                            </label>

                            <vue-select class="db-field-control f-b-custom-select" id="searchTax"
                                v-model="props.search.tax_id" :options="taxes" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchUnit" class="db-field-title">
                                {{ $t("label.unit") }}
                            </label>

                            <vue-select class="db-field-control f-b-custom-select" id="searchUnit"
                                v-model="props.search.unit_id" :options="units" label-by="name_code" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStatus" class="db-field-title after:hidden">
                                {{ $t("label.status") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                                v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchPurchasable" class="db-field-title after:hidden">
                                {{ $t("label.purchasable") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchPurchasable"
                                v-model="props.search.can_purchasable" :options="[
                                    { id: enums.askEnum.YES, name: $t('label.yes') },
                                    { id: enums.askEnum.NO, name: $t('label.no') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchIsStockOut" class="db-field-title after:hidden">
                                {{ $t("label.show_stock_out") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchIsStockOut"
                                v-model="props.search.show_stock_out" :options="[
                                    { id: enums.activityEnum.ENABLE, name: $t('label.enable') },
                                    { id: enums.activityEnum.DISABLE, name: $t('label.disable') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchRefundable" class="db-field-title after:hidden">
                                {{ $t("label.refundable") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="searchRefundable"
                                v-model="props.search.refundable" :options="[
                                    { id: enums.askEnum.YES, name: $t('label.yes') },
                                    { id: enums.askEnum.NO, name: $t('label.no') },
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>

                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-4">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search lab-font-size-16"></i>
                                    <span>{{ $t("button.search") }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" @click="clear">
                                    <i class="lab lab-line-cross lab-font-size-22"></i>
                                    <span>{{ $t("button.clear") }}</span>
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
                            <th class="db-table-head-th">
                                {{ $t('label.name') }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t('label.category') }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t('label.buying_price') }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t('label.selling_price') }}
                            </th>
                            <th class="db-table-head-th">
                                {{ $t('label.status') }}
                            </th>
                            <th class="db-table-head-th hidden-print"
                                v-if="permissionChecker('products_show') || permissionChecker('products_edit') || permissionChecker('products_delete')">
                                {{ $t('label.action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="products.length > 0">
                        <tr class="db-table-body-tr" v-for="product in products" :key="product">
                            <td class="db-table-body-td">
                                {{ textShortener(product.name, 40) }}
                            </td>
                            <td class="db-table-body-td">{{ product.category_name }}</td>
                            <td class="db-table-body-td">{{ product.flat_buying_price }}</td>
                            <td class="db-table-body-td">{{ product.flat_selling_price }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(product.status)">
                                    {{ enums.statusEnumArray[product.status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('products_show') || permissionChecker('products_edit') || permissionChecker('products_delete')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.product.show'" :id="product.id"
                                        v-if="permissionChecker('products_show')" />
                                    <SmIconSidebarModalEditComponent @click="edit(product)"
                                        v-if="permissionChecker('products_edit')" />
                                    <SmIconDeleteComponent @click="destroy(product.id)"
                                        v-if="permissionChecker('products_delete')" />
                                </div>
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

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="products.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>
    <ProductFileUploadComponent />
</template>
<script>
import LoadingComponent from "../components/LoadingComponent";
import ProductCreateComponent from "./ProductCreateComponent";
import alertService from "../../../services/alertService";
import statusEnum from "../../../enums/modules/statusEnum";
import askEnum from "../../../enums/modules/askEnum";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import TableLimitComponent from "../components/TableLimitComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import activityEnum from "../../../enums/modules/activityEnum";
import _ from "lodash";
import ImportComponent from "../components/buttons/import/ImportComponent";
import ProductFileUploadComponent from "./ProductFileUploadComponent";
import SampleFileComponent from "../components/buttons/import/SampleFileComponent";
import UploadFileComponent from "../components/buttons/import/UploadFileComponent";
import ENV from "../../../config/env";

export default {
    name: "ProductListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ProductCreateComponent,
        LoadingComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        SmIconViewComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        ProductFileUploadComponent,
        ImportComponent,
        SampleFileComponent,
        UploadFileComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                askEnum: askEnum,
                activityEnum: activityEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t("menu.products"),
            },
            searchProps: {
                paginate: 0,
                order_column: 'id',
                order_type: 'asc'
            },
            props: {
                form: {
                    name: "",
                    sku: "",
                    product_category_id: null,
                    barcode_id: null,
                    buying_price: "",
                    selling_price: "",
                    tax_id: [],
                    product_brand_id: null,
                    status: statusEnum.ACTIVE,
                    can_purchasable: askEnum.NO,
                    show_stock_out: activityEnum.DISABLE,
                    refundable: askEnum.NO,
                    maximum_purchase_quantity: "",
                    low_stock_quantity_warning: "",
                    unit_id: null,
                    weight: "",
                    warranty: "",
                    tags: "",
                    convertTags: [],
                    description: "",
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    name: "",
                    sku: "",
                    buying_price: "",
                    selling_price: "",
                    product_category_id: null,
                    product_brand_id: null,
                    barcode_id: null,
                    tax_id: null,
                    unit_id: null,
                    status: null,
                    can_purchasable: null,
                    show_stock_out: null,
                    refundable: null
                }
            },
            productCategories: [],
            ENV: ENV
        }
    },
    computed: {
        products: function () {
            return this.$store.getters['product/lists'];
        },
        pagination: function () {
            return this.$store.getters['product/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['product/page'];
        },
        productBrands: function () {
            return this.$store.getters['productBrand/lists'];
        },
        taxes: function () {
            return this.$store.getters['tax/lists'];
        },
        units: function () {
            return this.$store.getters['unit/lists'];
        },
        barcodes: function () {
            return this.$store.getters['barcode/lists'];
        }
    },
    mounted() {
        this.list();
        this.$store.dispatch('productCategory/depthTrees').then((res) => {
            this.productCategories = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
        this.$store.dispatch('productBrand/lists', this.searchProps);
        this.$store.dispatch('tax/lists', this.searchProps);
        this.$store.dispatch('unit/lists', this.searchProps);
        this.$store.dispatch('barcode/lists', this.searchProps);
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
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
        numberOnly: function (e) {
            return appService.floatNumber(e);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.name = "";
            this.props.search.sku = "";
            this.props.search.buying_price = "";
            this.props.search.selling_price = "";
            this.props.search.product_category_id = null;
            this.props.search.product_brand_id = null;
            this.props.search.barcode_id = null;
            this.props.search.tax_id = null;
            this.props.search.unit_id = null;
            this.props.search.show_stock_out = null;
            this.props.search.status = null;
            this.props.search.can_purchasable = null;
            this.props.search.refundable = null;
            this.list();
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('product/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (product) {
            this.loading.isActive = true;
            appService.sideDrawerShow();
            this.$store.dispatch('product/edit', product.id);
            this.loading.isActive = false;

            this.props.form.name = product.name;
            this.props.form.sku = product.sku;
            this.props.form.product_category_id = product.product_category_id;
            this.props.form.barcode_id = product.barcode_id;
            this.props.form.buying_price = product.flat_buying_price;
            this.props.form.selling_price = product.flat_selling_price;
            this.props.form.tax_id = this.taxUpdate(product.tax_id);
            this.props.form.product_brand_id = product.product_brand_id;
            this.props.form.status = product.status;
            this.props.form.can_purchasable = product.can_purchasable;
            this.props.form.show_stock_out = product.show_stock_out;
            this.props.form.refundable = product.refundable;
            this.props.form.maximum_purchase_quantity = product.maximum_purchase_quantity;
            this.props.form.low_stock_quantity_warning = product.low_stock_quantity_warning;
            this.props.form.unit_id = product.unit_id;
            this.props.form.weight = product.weight;
            this.props.form.warranty = product.warranty;
            this.props.form.convertTags = this.tagUpdate(product.product_tags);
            this.props.form.tags = ""
            this.props.form.description = product.description;
        },
        tagUpdate: function (objects) {
            let tags = [];
            _.forEach(objects, (object) => {
                tags.push({ "text": object.name, "tiClasses": ["ti-valid"] });
            });
            return tags;
        },
        taxUpdate: function (objects) {
            let taxes = [];
            _.forEach(objects, (object, key) => {
                taxes.push(object.tax_id);
            });
            return taxes;
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('product/destroy', { id: id, search: this.props.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.products'));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    })
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch("product/export", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.products");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        download: function () {
            this.loading.isActive = true;
            this.$store.dispatch("product/download").then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.products");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
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