<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.suppliers") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <SupplierCreateComponent :props="props" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.company") }}</th>
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.email") }}</th>
                        <th class="db-table-head-th">{{ $t("label.phone") }}</th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="suppliers.length > 0">
                    <tr class="db-table-body-tr" v-for="supplier in suppliers" :key="supplier">
                        <td class="db-table-body-td">
                            {{ supplier.company }}
                        </td>
                        <td class="db-table-body-td">
                            {{ supplier.name }}
                        </td>
                        <td class="db-table-body-td">
                            {{ supplier.email }}
                        </td>
                        <td class="db-table-body-td">
                            {{ supplier.phone }}
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.settings.supplier.show'" :id="supplier.id" />
                                <SmModalEditComponent @click="edit(supplier)" />
                                <SmDeleteComponent @click="destroy(supplier.id)" />
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="5">
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

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="suppliers.length > 0">
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
import SupplierCreateComponent from "./SupplierCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import ENV from "../../../../config/env";

export default {
    name: "SupplierListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        SupplierCreateComponent,
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
            props: {
                form: {
                    company: "",
                    name: "",
                    email: "",
                    phone: "",
                    country_code: "",
                    country: null,
                    state: null,
                    city: null,
                    zip_code: "",
                    address: "",
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                },
                flag: "",
                states: [],
                cities: [],
            },
            ENV:ENV
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        suppliers: function () {
            return this.$store.getters["supplier/lists"];
        },
        pagination: function () {
            return this.$store.getters["supplier/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["supplier/page"];
        },
    },
    methods: {
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store
                .dispatch("supplier/lists", this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        edit: async function (supplier) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch("supplier/edit", supplier.id);

            if (supplier.country !== "") {
                if (supplier.state !== "") {
                    await this.$store.dispatch('state/statesByCountry', supplier.country)
                        .then((res) => {
                            this.props.states = res.data.data;
                        })
                    await this.$store.dispatch('city/citiesByState', supplier.state)
                        .then((res) => {
                            this.props.cities = res.data.data;
                        })
                    if (supplier.city === "") {
                        this.props.form.city = null;
                    }
                } else {
                    await this.$store.dispatch('state/statesByCountry', supplier.country)
                        .then((res) => {
                            this.props.states = res.data.data;
                        })
                    this.props.form.state = null;
                    this.props.form.city = null;
                }
            } else {
                this.props.states = [];
                this.props.form.country = null;
                this.props.form.state = null;
                this.props.form.city = null
            }

            setTimeout(() => {
                this.props.form = {
                    company: supplier.company,
                    name: supplier.name,
                    email: supplier.email,
                    phone: supplier.phone,
                    country_code: supplier.country_code,
                    country: supplier.country,
                    state: supplier.state,
                    city: supplier.city,
                    zip_code: supplier.zip_code,
                    address: supplier.address,
                };

                if (supplier.country === "") {
                    this.props.form.country = null;
                }

                if (supplier.state === "") {
                    this.props.form.state = null;
                }

                if (supplier.city === "") {
                    this.props.form.city = null;
                }

            }, 200);

            this.$store.dispatch('countryCode/callingCode', supplier.country_code).then(res => {
                this.props.flag = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;
        },
        destroy: function (id) {
            appService
                .destroyConfirmation()
                .then((res) => {
                    try {
                        this.loading.isActive = true;
                        this.$store
                            .dispatch("supplier/destroy", {
                                id: id,
                                search: this.props.search,
                            })
                            .then((res) => {
                                this.loading.isActive = false;
                                alertService.successFlip(
                                    null,
                                    this.$t("menu.suppliers")
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
