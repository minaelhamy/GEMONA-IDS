<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.product_brands") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <ProductBrandCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.description") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="productBrands.length > 0">
                    <tr class="db-table-body-tr" v-for="productBrand in productBrands" :key="productBrand">
                        <td class="db-table-body-td">
                            {{ productBrand.name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ textShortener(productBrand.description) }}
                        </td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(productBrand.status)">
                                {{ enums.statusEnumArray[productBrand.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.settings.productBrand.show'" :id="productBrand.id" />
                                <SmModalEditComponent @click="edit(productBrand)" />
                                <SmDeleteComponent @click="destroy(productBrand.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="4">
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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"  v-if="productBrands.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../../components/LoadingComponent";
import ProductBrandCreateComponent from "./ProductBrandCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import ENV from "../../../../config/env";

export default {
    name: "ProductBrandListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ProductBrandCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
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
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                },
            },
            ENV:ENV
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        productBrands: function () {
            return this.$store.getters["productBrand/lists"];
        },
        pagination: function () {
            return this.$store.getters["productBrand/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["productBrand/page"];
        },
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
            this.$store
                .dispatch("productBrand/lists", this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        edit: function (productBrand) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch("productBrand/edit", productBrand.id);
            this.props.form = {
                name: productBrand.name,
                description: productBrand.description,
                status: productBrand.status,
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService
                .destroyConfirmation()
                .then((res) => {
                    try {
                        this.loading.isActive = true;
                        this.$store
                            .dispatch("productBrand/destroy", {
                                id: id,
                                search: this.props.search,
                            })
                            .then((res) => {
                                this.loading.isActive = false;
                                alertService.successFlip(
                                    null,
                                    this.$t("menu.product_brands")
                                );
                            })
                            .catch((err) => {
                                this.loading.isActive = false;
                                alertService.error(err.response.data.message);
                            });
                    } catch (err) {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    }
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
    },
};
</script>
