<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t('menu.product_categories') }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
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
                        <UploadFileComponent props="categoryImportModal" />
                    </div>
                </div>
                <ProductCategoryCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe" id="print">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t('label.name') }}</th>
                        <th class="db-table-head-th">{{ $t('label.parent_category') }}</th>
                        <th class="db-table-head-th">{{ $t('label.status') }}</th>
                        <th class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="productCategories.length > 0">
                    <tr class="db-table-body-tr" v-for="productCategory in productCategories" :key="productCategory">
                        <td class="db-table-body-td">{{ productCategory.name }}</td>
                        <td class="db-table-body-td">{{ productCategory.parent_category }}</td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(productCategory.status)">
                                {{ enums.statusEnumArray[productCategory.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td hidden-print">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.settings.productCategory.show'"
                                    :id="productCategory.id" />
                                <SmModalEditComponent @click="edit(productCategory)" />
                                <SmDeleteComponent @click="destroy(productCategory.id)" />
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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="productCategories.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
    <ProductCategoryFileUploadComponent />
</template>
<script>
import LoadingComponent from "../../components/LoadingComponent";
import ProductCategoryCreateComponent from "./ProductCategoryCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import ExportComponent from "../../components/buttons/export/ExportComponent";
import PrintComponent from "../../components/buttons/export/PrintComponent";
import ExcelComponent from "../../components/buttons/export/ExcelComponent";
import ImportComponent from "../../components/buttons/import/ImportComponent";
import ProductCategoryFileUploadComponent from "./ProductCategoryFileUploadComponent";
import SampleFileComponent from "../../components/buttons/import/SampleFileComponent";
import UploadFileComponent from "../../components/buttons/import/UploadFileComponent";
import ENV from "../../../../config/env";

export default {
    name: "ProductCategoryListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ProductCategoryCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        ImportComponent,
        ProductCategoryFileUploadComponent,
        SampleFileComponent,
        UploadFileComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.product_categories"),
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            props: {
                form: {
                    name: "",
                    description: "",
                    parent_id: null,
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                }
            },
            ENV:ENV
        }
    },
    computed: {
        productCategories: function () {
            return this.$store.getters['productCategory/lists'];
        },
        pagination: function () {
            return this.$store.getters['productCategory/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['productCategory/page'];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('productCategory/lists', this.props.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (productCategory) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch('productCategory/edit', productCategory.id);
            this.props.form = {
                name: productCategory.name,
                parent_id: productCategory.parent_id === 0 ? null : productCategory.parent_id,
                status: productCategory.status,
                description: productCategory.description
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('productCategory/destroy', { id: id, search: this.props.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('menu.product_categories'));
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
            this.$store.dispatch("productCategory/export", this.props.search).then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.product_categories");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        download: function () {
            this.loading.isActive = true;
            this.$store.dispatch("productCategory/download").then((res) => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], {
                    type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.product_categories");
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