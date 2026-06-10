<template>
    <div class="db-card-header border-none">
        <h3 class="db-card-title">{{ $t('label.video') }}</h3>
        <div class="db-card-filter">
            <ProductVideoCreateComponent :props="props" v-if="permissionChecker('products_create')" />
            <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
        </div>
    </div>

    <div class="db-table-responsive">
        <table class="db-table stripe">
            <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.video_provider") }}</th>
                    <th class="db-table-head-th">{{ $t("label.link") }}</th>
                    <th class="db-table-head-th">{{ $t("label.action") }}</th>
                </tr>
            </thead>
            <tbody class="db-table-body" v-if="productVideos.length > 0">
                <tr class="db-table-body-tr" v-for="productVideo in productVideos" :key="productVideo">
                    <td class="db-table-body-td">
                        {{ productVideo.provider_name }}
                    </td>
                    <td class="db-table-body-td">
                        {{ productVideo.link }}
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                            <SmIconModalEditComponent @click="edit(productVideo)" v-if="permissionChecker('products_edit')" />
                            <SmIconDeleteComponent @click="destroy(productVideo.id)"
                                v-if="permissionChecker('products_delete')" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
        <PaginationSMBox :pagination="pagination" :method="list" />
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <PaginationTextComponent :props="{ page: paginationPage }" />
            <PaginationBox :pagination="pagination" :method="list" />
        </div>
    </div>
</template>

<script>
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import ProductVideoCreateComponent from "./ProductVideoCreateComponent";
import TableLimitComponent from "../../components/TableLimitComponent";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent";
import ExcelComponent from "../../components/buttons/export/ExcelComponent.vue";
import ProductCreateComponent from "../ProductCreateComponent.vue";
import ExportComponent from "../../components/buttons/export/ExportComponent.vue";
import FilterComponent from "../../components/buttons/collapse/FilterComponent.vue";
import PrintComponent from "../../components/buttons/export/PrintComponent.vue";

export default {
    name: "ProductVideoListComponent",
    components: {
        PrintComponent, FilterComponent, ExportComponent, ProductCreateComponent, ExcelComponent,
        ProductVideoCreateComponent,
        TableLimitComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        SmIconDeleteComponent,
        SmIconModalEditComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            props: {
                form: {
                    video_provider: null,
                    link: '',
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc'
                },
                productId: 0
            }
        }
    },
    mounted() {
        this.props.productId = this.$route.params.id;
        this.list();
    },
    computed: {
        productVideos: function () {
            return this.$store.getters['productVideo/lists'];
        },
        pagination: function () {
            return this.$store.getters['productVideo/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['productVideo/page'];
        }
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('productVideo/lists', this.props).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        edit: function (productVideo) {
            appService.modalShow('#videoModal');
            this.loading.isActive = true;
            this.$store.dispatch('productVideo/edit', productVideo.id);
            this.props.form = {
                video_provider: productVideo.video_provider,
                link: productVideo.link,
            };
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('productVideo/destroy', {
                        id: id,
                        productId: this.props.productId,
                        search: this.props.search
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.product_video'));
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
