<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.reviews') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('review-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-filter-div" id="review-filter">
                <form class="p-4 sm:p-5 mb-5 w-full d-block" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title">{{
                                $t("label.product")
                                }}</label>

                            <vue-select class="db-field-control f-b-custom-select" id="product_id"
                                v-model="props.search.product_id" :options="products" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="user_id" class="db-field-title">
                                {{ $t("label.customer") }}
                            </label>
                            <vue-select class="db-field-control f-b-custom-select" id="user_id"
                                v-model="props.search.user_id" :options="customers" label-by="name" value-by="id"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
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
                            <th class="db-table-head-th">{{ $t('label.rating') }}</th>
                            <th class="db-table-head-th">{{ $t('label.review') }}</th>
                            <th class="db-table-head-th">{{ $t('label.product') }}</th>
                            <th class="db-table-head-th">{{ $t('label.customer') }}</th>
                            <th v-if="permissionChecker('reviews')" class="db-table-head-th hidden-print">{{
                                $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body border-b border-gray-200" v-if="reviews.length > 0">
                        <tr class="db-table-body-tr" v-for="(review, index) of reviews" :key="index">
                            <td class="db-table-body-td">
                                <starRating border-color="#FFBC1F" inactive-color="#FFFFFF" active-color="#FFBC1F"
                                    :rounded-corners="true" :padding="2.5" :border-width="2.5" :star-size="11"
                                    class="-mt-0.5" :round-start-rating="false" :show-rating="false" :read-only="true"
                                    :max-rating="5" :rating="review.star" />
                            </td>
                            <td class="db-table-body-td"> <span v-html="textShortener(review.review)"></span></td>
                            <td class="db-table-body-td">{{ review.product_name }}</td>
                            <td class="db-table-body-td">{{ review.user_name }}</td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('reviews')">
                                <SmIconViewComponent :link="'admin.review.show'" :id="review.id"
                                    v-if="permissionChecker('reviews')" />
                            </td>
                        </tr>

                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="6">
                                <div class="p-4">
                                    <div class="max-w-[300px] mx-auto mt-2">
                                        <img class="w-full h-full"
                                            :src="ENV.API_URL + '/images/default/not-found/not_found.png'"
                                            alt="Not Found">
                                    </div>
                                    <span class="d-block mt-3 text-lg">{{ $t('message.no_data_found') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"
                v-if="reviews.length > 0">
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
import starRating from "vue-star-rating";
import ENV from "../../../config/env";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import LoadingComponent from "../components/LoadingComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";

export default {
    name: 'ReviewListComponent',
    components: {
        PaginationBox,
        PaginationSMBox,
        PaginationTextComponent,
        TableLimitComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        ExportComponent,
        SmIconViewComponent,
        LoadingComponent,
        SmIconSidebarModalEditComponent,
        starRating
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t('menu.reviews')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    user_id: null,
                    product_id: null,
                }
            },
            productSearch: {
                paginate: 0,
                page: 1,
                order_column: 'id',
            },
            ENV: ENV
        }
    },
    mounted() {
        this.list();
        this.loading.isActive = true;
        this.$store.dispatch('product/getSimpleProduct', this.productSearch).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
        this.$store.dispatch('user/lists', {
            order_column: 'id',
            order_type: 'asc',
            status: statusEnum.ACTIVE
        });
    },
    computed: {
        reviews: function () {
            return this.$store.getters['review/lists'];
        },
        pagination: function () {
            return this.$store.getters['review/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['review/page'];
        },
        products: function () {
            return this.$store.getters['product/simpleList'];
        },
        customers: function () {
            return this.$store.getters['user/lists'];
        },
    },
    methods: {
        textShortener: function (text, number = 55) {
            text = appService.htmlTagRemover(text);
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('review/lists', this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                })
        },
        clear: function () {
            this.props.search = {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: 'id',
                order_type: 'desc',
                user_id: null,
                product_id: null,
            },
                this.list();
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('review/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.reviews");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });

        },
        reset: function () {
            this.$store.dispatch('review/reset').then().catch();
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
