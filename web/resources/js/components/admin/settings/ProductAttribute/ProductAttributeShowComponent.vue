

<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.product_attributes") }}- <span class="text-primary">({{
                productAttribute.name }})</span></h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <ProductAttributeOptionCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="productAttributeOptions.length > 0">
                    <tr class="db-table-body-tr" v-for="productAttributeOption in productAttributeOptions"
                        :key="productAttributeOption">
                        <td class="db-table-body-td">
                            {{ productAttributeOption.name }}
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmModalEditComponent @click="edit(productAttributeOption)" />
                                <SmDeleteComponent @click="destroy(productAttributeOption.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="2">
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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="productAttributeOptions.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>

<script>

import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import TableLimitComponent from "../../components/TableLimitComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import ProductAttributeOptionCreateComponent from "./ProductAttributeOptionCreateComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import ENV from "../../../../config/env";

export default {
    name: "ProductAttributeShowComponent",
    components: {
        LoadingComponent, SmModalEditComponent, TableLimitComponent, PaginationTextComponent,PaginationBox, PaginationSMBox, SmDeleteComponent, ProductAttributeOptionCreateComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            props: {
                form: {
                    name: "",
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                },
                attributeId: 0,
            },
            ENV:ENV
        }
    },
    computed: {
        productAttribute: function () {
            return this.$store.getters['productAttribute/show'];
        },
        productAttributeOptions: function () {
            return this.$store.getters['productAttributeOption/lists'];
        },
        pagination: function () {
            return this.$store.getters['productAttributeOption/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['productAttributeOption/page'];
        }
    },
    mounted() {
        this.props.attributeId = this.$route.params.id;
        this.list();
        this.show();
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        show: function () {
            this.loading.isActive = true;
            this.$store.dispatch('productAttribute/show', this.$route.params.id).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('productAttributeOption/lists', this.props).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        edit: function (productAttributeOption) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch('productAttributeOption/edit', productAttributeOption.id);
            this.props.form = {
                name: productAttributeOption.name,
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('productAttributeOption/destroy', {
                        id: id,
                        attributeId: this.props.attributeId,
                        search: this.props.search
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.product_attribute_option'));
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
        }
    }
}
</script>

<style scoped></style>
