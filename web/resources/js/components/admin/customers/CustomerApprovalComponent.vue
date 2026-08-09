<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <div>
                    <h3 class="db-card-title">{{ $t("label.user_approvals") }}</h3>
                    <p class="text-sm text-[#6E7191] mt-1">{{ $t("message.approve_new_signup_requests") }}</p>
                </div>
                <div class="db-card-filter">
                    <router-link :to="{ name: 'admin.customers.list' }" class="db-btn py-2 text-white bg-gray-600">
                        <i class="lab lab-line-users lab-font-size-16"></i>
                        <span>{{ $t("label.all_customers") }}</span>
                    </router-link>
                </div>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t("label.name") }}</th>
                            <th class="db-table-head-th">{{ $t("label.email") }}</th>
                            <th class="db-table-head-th">{{ $t("label.phone") }}</th>
                            <th class="db-table-head-th">{{ $t("label.organization") }}</th>
                            <th class="db-table-head-th">{{ $t("label.country") }}</th>
                            <th class="db-table-head-th">{{ $t("label.address") }}</th>
                            <th class="db-table-head-th">{{ $t("label.action") }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="customers.length > 0">
                        <tr class="db-table-body-tr" v-for="customer in customers" :key="customer.id">
                            <td class="db-table-body-td">{{ textShortener(customer.name, 24) }}</td>
                            <td class="db-table-body-td">{{ customer.email }}</td>
                            <td class="db-table-body-td">
                                <span dir="ltr">{{ customer.phone ? customer.country_code + '' + customer.phone : '' }}</span>
                            </td>
                            <td class="db-table-body-td">
                                {{ textShortener(customer.organization?.name || '', 32) }}
                            </td>
                            <td class="db-table-body-td">{{ customer.country }}</td>
                            <td class="db-table-body-td">{{ textShortener(customer.address, 40) }}</td>
                            <td class="db-table-body-td">
                                <div class="flex justify-start items-center gap-1.5">
                                    <button type="button" class="db-btn py-1.5 text-white bg-[#1AB759]"
                                        @click.prevent="approve(customer.id)">
                                        <i class="lab lab-line-circle-check lab-font-size-16"></i>
                                        <span>{{ $t("label.approve") }}</span>
                                    </button>
                                    <SmIconViewComponent :link="'admin.customers.show'" :id="customer.id"
                                        v-if="permissionChecker('customers_show')" />
                                </div>
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
                                    <span class="d-block mt-3 text-lg">{{ $t("message.no_pending_user_approvals") }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="customers.length > 0">
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
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import ENV from "../../../config/env";

export default {
    name: "CustomerApprovalComponent",
    components: {
        LoadingComponent,
        PaginationTextComponent,
        PaginationBox,
        PaginationSMBox,
        SmIconViewComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    status: statusEnum.INACTIVE,
                },
            },
            ENV: ENV,
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        customers: function () {
            return this.$store.getters["customer/lists"];
        },
        pagination: function () {
            return this.$store.getters["customer/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["customer/page"];
        },
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("customer/lists", this.props.search)
                .then(() => {
                    this.loading.isActive = false;
                })
                .catch(() => {
                    this.loading.isActive = false;
                });
        },
        approve: function (id) {
            this.loading.isActive = true;
            this.$store.dispatch("customer/approve", {
                id: id,
                search: this.props.search,
            }).then(() => {
                this.loading.isActive = false;
                alertService.success(this.$t("message.customer_approved_successfully"));
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
    },
};
</script>
