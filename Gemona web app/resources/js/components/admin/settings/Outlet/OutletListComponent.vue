<template>
    <LoadingComponent :props="loading"/>

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.outlets") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage"/>
                <OutletCreateComponent :props="props"/>
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                <tr class="db-table-head-tr">
                    <th class="db-table-head-th">{{ $t("label.name") }}</th>
                    <th class="db-table-head-th">
                        {{ $t("label.status") }}
                    </th>
                    <th class="db-table-head-th">
                        {{ $t("label.action") }}
                    </th>
                </tr>
                </thead>
                <tbody class="db-table-body" v-if="outlets.length > 0">
                <tr class="db-table-body-tr" v-for="outlet in outlets" :key="outlet">
                    <td class="db-table-body-td" v-if="site_default_outlet === outlet.id">
                        {{ outlet.name }}({{ $t('label.default') }})
                    </td>
                    <td class="db-table-body-td" v-else>
                        {{ outlet.name }}
                    </td>
                    <td class="db-table-body-td">
                            <span :class="statusClass(outlet.status)">
                                {{ enums.statusEnumArray[outlet.status] }}
                            </span>
                    </td>
                    <td class="db-table-body-td">
                        <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                            <SmViewComponent :link="'admin.settings.outlet.show'" :id="outlet.id"/>
                            <SmModalEditComponent @click="edit(outlet)"/>
                            <SmDeleteComponent @click="destroy(outlet.id)" v-if="site_default_outlet !== outlet.id"/>
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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="outlets.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list"/>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }"/>
                <PaginationBox :pagination="pagination" :method="list"/>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingComponent from "../../components/LoadingComponent";
import OutletCreateComponent from "./OutletCreateComponent";
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
import ENV from "../../../../config/env";

export default {
    name: "OutletListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        OutletCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
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
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            props: {
                form: {
                    name: "",
                    email: "",
                    country_code: "",
                    phone: "",
                    latitude: "",
                    longitude: "",
                    city: "",
                    state: "",
                    zip_code: "",
                    address: "",
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
            site_default_outlet: null,
            ENV:ENV
        };
    },
    mounted() {
        this.list();
        this.siteList();
    },
    computed: {
        outlets: function () {
            return this.$store.getters["outlet/lists"];
        },
        pagination: function () {
            return this.$store.getters["outlet/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["outlet/page"];
        },
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        siteList: function () {
            this.loading.isActive = true;
            this.$store.dispatch('site/lists').then(res => {
                this.site_default_outlet = res.data.data.site_default_outlet;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("outlet/lists", this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (outlet) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch("outlet/edit", outlet.id);
            this.props.form = {
                name: outlet.name,
                email: outlet.email,
                country_code: outlet.country_code,
                phone: outlet.phone,
                latitude: outlet.latitude,
                longitude: outlet.longitude,
                city: outlet.city,
                state: outlet.state,
                zip_code: outlet.zip_code,
                address: outlet.address,
                status: outlet.status,
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
                            .dispatch("outlet/destroy", {
                                id: id,
                                search: this.props.search,
                            })
                            .then((res) => {
                                this.loading.isActive = false;
                                alertService.successFlip(null, this.$t("menu.outlets"));
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
