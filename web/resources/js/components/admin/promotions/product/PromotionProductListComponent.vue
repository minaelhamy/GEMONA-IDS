<template>
    <div class="db-card-header border-none">
        <h3 class="db-card-title">{{ $t('menu.products') }}</h3>
        <div class="db-card-filter">
            <PromotionProductCreateComponent :props="promotionProps" />
            <TableLimitComponent :method="list" :search="promotionProps.search" :page="paginationPage" />
        </div>
    </div>
    <div class="db-table-responsive">
        <table class="db-table stripe">
            <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.name") }}</th>
                    <th class="db-table-head-th">{{ $t("label.price") }}</th>
                    <th class="db-table-head-th">{{ $t("label.status") }}</th>
                    <th class="db-table-head-th">{{ $t("label.action") }}</th>
                </tr>
            </thead>
            <tbody class="db-table-body" v-if="promotionProducts.length > 0">
                <tr class="db-table-body-tr" v-for="promotionProduct in promotionProducts" :key="promotionProduct">
                    <td class="db-table-body-td">
                        {{ promotionProduct.promotion_product_name }}
                    </td>
                    <td class="db-table-body-td">
                        {{ promotionProduct.is_offer ? promotionProduct.discounted_price :
                            promotionProduct.currency_price }}
                    </td>
                    <td class="db-table-body-td">
                        <span :class="statusClass(promotionProduct.promotion_product_status)">
                            {{ enums.statusEnumArray[promotionProduct.promotion_product_status] }}
                        </span>
                    </td>
                    <td class="db-table-body-td">
                        <SmIconDeleteComponent @click="destroy(promotionProduct.id)" />
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
import SmSidebarModalCreateComponent from "../../components/buttons/SmSidebarModalCreateComponent";
import alertService from "../../../../services/alertService";
import statusEnum from "../../../../enums/modules/statusEnum";
import appService from "../../../../services/appService";
import SmIconDeleteComponent from "../../components/buttons/SmIconDeleteComponent";
import SmIconModalEditComponent from "../../components/buttons/SmIconModalEditComponent";
import PromotionProductCreateComponent from "./PromotionProductCreateComponent";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import TableLimitComponent from "../../components/TableLimitComponent";

export default {
    name: "PromotionProductListComponent",
    components: {
        PromotionProductCreateComponent, SmSidebarModalCreateComponent, SmIconModalEditComponent, SmIconDeleteComponent, TableLimitComponent, PaginationTextComponent, PaginationBox, PaginationSMBox
    },
    props: {
        promotion: { type: Number },
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            promotionProps: {
                id: this.promotion,
                form: {
                    product_id: null,
                },
                search: {
                    id: this.promotion,
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                }
            },
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        promotionProducts: function () {
            return this.$store.getters['promotionProduct/lists'];
        },
        pagination: function () {
            return this.$store.getters["promotionProduct/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["promotionProduct/page"];
        },
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.promotionProps.search.page = page;
            this.$store.dispatch("promotionProduct/lists", this.promotionProps.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch('promotionProduct/destroy', { promotion: this.promotion, id: id, search: this.promotionProps.search }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('label.product'));
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
            });
        }
    }
}
</script>
