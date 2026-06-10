<template>
    <LoadingComponent :props="loading" />
    <div class="db-card-header border-none">
        <h3 class="db-card-title">{{ $t('menu.order_area') }}</h3>
        <div class="db-card-filter">
            <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
            <OrderAreaCreateComponent :props="props" />
        </div>
    </div>
    <div class="db-table-responsive">
        <table class="db-table stripe" id="print">
            <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">
                        {{ $t('label.country') }}
                    </th>
                    <th class="db-table-head-th">
                        {{ $t('label.state') }}
                    </th>
                    <th class="db-table-head-th">
                        {{ $t('label.city') }}
                    </th>
                    <th class="db-table-head-th">
                        {{ $t('label.shipping_cost') }}
                    </th>
                    <th class="db-table-head-th">
                        {{ $t('label.status') }}
                    </th>
                    <th class="db-table-head-th hidden-print">
                        {{ $t('label.action') }}
                    </th>
                </tr>
            </thead>
            <tbody class="db-table-body" v-if="orderAreas.length > 0">
                <tr class="db-table-body-tr" v-for="orderArea in orderAreas" :key="orderArea">
                    <td class="db-table-body-td">
                        {{ orderArea.country }}
                    </td>
                    <td class="db-table-body-td">{{ orderArea.state }}</td>
                    <td class="db-table-body-td">{{ orderArea.city }}</td>
                    <td class="db-table-body-td">{{ orderArea.shipping_cost }}</td>
                    <td class="db-table-body-td">
                        <span :class="statusClass(orderArea.status)">
                            {{ enums.statusEnumArray[orderArea.status] }}
                        </span>
                    </td>
                    <td class="db-table-body-td hidden-print">
                        <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                            <SmModalEditComponent @click="edit(orderArea)" />
                            <SmDeleteComponent @click="destroy(orderArea.id)" />
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
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="orderAreas.length > 0">
        <PaginationSMBox :pagination="pagination" :method="list" />
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <PaginationTextComponent :props="{ page: paginationPage }" />
            <PaginationBox :pagination="pagination" :method="list" />
        </div>
    </div>
</template>

<script>
import alertService from "../../../../../services/alertService";
import LoadingComponent from "../../../components/LoadingComponent";
import SmDeleteComponent from "../../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../../components/buttons/SmModalEditComponent";
import statusEnum from "../../../../../enums/modules/statusEnum";
import PaginationTextComponent from "../../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../../components/pagination/PaginationBox";
import PaginationSMBox from "../../../components/pagination/PaginationSMBox";
import appService from "../../../../../services/appService";
import TableLimitComponent from "../../../components/TableLimitComponent";
import OrderAreaCreateComponent from "./OrderAreaCreateComponent";
import ENV from "../../../../../config/env";

export default {
    name: "OrderAreaListComponent",
    components: {
        LoadingComponent,
        OrderAreaCreateComponent,
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        SmDeleteComponent,
        SmModalEditComponent,
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
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            props: {
                form: {
                    country: null,
                    state: null,
                    city: null,
                    shipping_cost: "",
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                },
                states: [],
                cities: []
            },
            errors: {},
            ENV:ENV
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.list();

    },
    computed: {
        orderAreas: function () {
            return this.$store.getters["orderArea/lists"];
        },
        pagination: function () {
            return this.$store.getters["orderArea/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["orderArea/page"];
        },
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("orderArea/lists", this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (orderArea) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch("orderArea/edit", orderArea.id).then(async (res) => {
                this.loading.isActive = false;

                if (orderArea.state !== "") {
                    await this.$store.dispatch('state/statesByCountry', orderArea.country)
                        .then((res) => {
                            this.props.states = res.data.data;
                        })
                    await this.$store.dispatch('city/citiesByState', orderArea.state)
                        .then((res) => {
                            this.props.cities = res.data.data;
                        })
                    if (orderArea.city === "") {
                        this.props.form.city = null;
                    }
                } else {
                    await this.$store.dispatch('state/statesByCountry', orderArea.country)
                        .then((res) => {
                            this.props.states = res.data.data;
                        })
                    this.props.form.state = null;
                    this.props.form.city = null;
                }

                setTimeout(() => {
                    this.props.form = {
                        country: orderArea.country,
                        state: orderArea.state,
                        city: orderArea.city,
                        shipping_cost: orderArea.shipping_cost,
                        status: orderArea.status,

                    };

                    if (orderArea.state === "") {
                        this.props.form.state = null;
                    }

                    if (orderArea.city === "") {
                        this.props.form.city = null;
                    }

                }, 200);
            }).catch((err) => {
                alertService.error(err.response.data.message);
            });
        },
        destroy: function (orderAreaId) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("orderArea/destroy", {
                        id: orderAreaId,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.order_area"));
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

    }
}
</script>
