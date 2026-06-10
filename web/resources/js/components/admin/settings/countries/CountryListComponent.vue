<template>
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.countries") }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('country-filter')" />
                    <CountryCreateComponent :props="props" />
                </div>
            </div>
            <div class="table-filter-div" id="country-filter">
                <form class="form-row p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="form-col-12 sm:form-col-6 lg:form-col-4">
                        <label for="name" class="db-field-title ">{{ $t("label.name") }}</label>
                        <input v-model="props.search.name" v-bind:class="errors.name ? 'invalid' : ''" type="text" id="name"
                            class="db-field-control">
                        <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6 lg:form-col-4">
                        <label for="code" class="db-field-title ">{{ $t("label.code") }}</label>
                        <input v-model="props.search.code" v-bind:class="errors.code ? 'invalid' : ''" type="text" id="code"
                            class="db-field-control">
                        <small class="db-field-alert" v-if="errors.code">{{ errors.code[0] }}</small>
                    </div>
                    <div class="form-col-12 sm:form-col-6 lg:form-col-4">
                        <label class="db-field-title " for="active">{{ $t('label.status') }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="searchStatus"
                            v-model="props.search.status" :options="[
                                { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') },
                            ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                            placeholder="--" search-placeholder="--" />
                    </div>
                    <div class="col-12 sm:form-col-6 lg:form-col-4">
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
                </form>
            </div>
            <div class="db-table-responsive">
                <LoadingContentComponent :props="loading" />
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t("label.name") }}</th>
                            <th class="db-table-head-th">{{ $t("label.code") }}</th>
                            <th class="db-table-head-th">{{ $t("label.status") }}</th>
                            <th class="db-table-head-th">{{ $t("label.action") }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="countries.length > 0">
                        <tr class="db-table-body-tr" v-for="country in countries" :key="country">
                            <td class="db-table-body-td">
                                <div v-if="country.name.length < 40"> {{ country.name }}</div>
                                <div v-else>{{ country.name.substring(0, 40) + ".." }}</div>
                            </td>
                            <td class="db-table-body-td">{{ country.code }}</td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(country.status)">
                                    {{ enums.statusEnumArray[country.status] }}
                                </span>
                            </td>
                            <td class="db-table-body-td hidden-print">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmModalEditComponent @click="edit(country)" />
                                    <SmDeleteComponent @click="destroy(country.id)" />
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

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="countries.length > 0">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
</template>
<script>
import CountryCreateComponent from "./CountryCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import FilterComponent from "../../components/buttons/collapse/FilterComponent";
import SmIconViewComponent from "../../components/buttons/SmIconViewComponent";
import LoadingContentComponent from "../../../frontend/components/LoadingContentComponent.vue";
import ENV from "../../../../config/env";


export default {
    name: "CouponListComponent",
    components: {
        SmModalEditComponent,
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        CountryCreateComponent,
        SmDeleteComponent,
        SmViewComponent,
        FilterComponent,
        SmIconViewComponent,
        LoadingContentComponent
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
                    code: "",
                    status: statusEnum.ACTIVE,
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "name",
                    order_type: "asc",
                    name: "",
                    code: "",
                    status: null,
                },
            },
            errors: {},
            ENV: ENV,
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        countries: function () {
            return this.$store.getters["country/lists"];
        },
        pagination: function () {
            return this.$store.getters["country/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["country/page"];
        },
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.name = "";
            this.props.search.code = "";
            this.props.search.status = null;
            this.list();
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("country/lists", this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (country) {
            appService.modalShow();
            this.loading.isActive = true;
            this.$store.dispatch("country/edit", country.id).then((res) => {
                this.loading.isActive = false;
                this.props.errors = {};
                this.props.form = {
                    name: country.name,
                    code: country.code,
                    status: country.status,
                };
            }).catch((err) => {
                alertService.error(err.response.data.message);
            });
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("country/destroy", {
                        id: id,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            null,
                            this.$t("menu.countries")
                        );
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
    },
};

</script>