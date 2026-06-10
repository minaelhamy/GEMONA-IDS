<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card db-tab-div active">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t("menu.subscribers") }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('subscriber-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <SubscriberMailComponent :props="props" v-if="permissionChecker('subscribers')" />
                </div>
            </div>
            <div class="table-filter-div" id="subscriber-filter">
                <form class="p-4 sm:p-5 mb-5 w-full d-block" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchEmail" class="db-field-title after:hidden">{{ $t('label.email') }}</label>
                            <input id="searchEmail" v-model="props.search.email" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchStartDate" class="db-field-title after:hidden">{{
                                $t('label.date')
                            }}</label>
                            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" 
                                @update:modelValue="handleDate" v-model="modelValue" :range="true" >
                            </Datepicker>
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
                            <th class="db-table-head-th">{{ $t("label.email") }}</th>
                            <th class="db-table-head-th">{{ $t("label.date") }}</th>
                            <th class="db-table-head-th hidden-print" v-if="permissionChecker('subscribers')">
                                {{ $t("label.action") }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="subscribers.length > 0">
                        <tr class="db-table-body-tr" v-for="subscriber in subscribers" :key="subscriber">
                            <td class="db-table-body-td">{{ subscriber.email }}</td>
                            <td class="db-table-body-td">{{ subscriber.date_time }}</td>
                            <td class="db-table-body-td hidden-print" v-if="permissionChecker('subscribers')">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconDeleteComponent @click="destroy(subscriber.id)"
                                        v-if="permissionChecker('subscribers')" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="db-table-body" v-else>
                        <tr class="db-table-body-tr">
                            <td class="db-table-body-td text-center" colspan="3">
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

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="subscribers.length > 0">
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
import SubscriberMailComponent from "./SubscriberMailComponent";
import alertService from "../../../services/alertService";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import TableLimitComponent from "../components/TableLimitComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import print from 'vue3-print-nb';
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ENV from "../../../config/env";


export default {
    name: "SubscriberListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        SubscriberMailComponent,
        LoadingComponent,
        SmIconDeleteComponent,
        ExportComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        Datepicker,
        SmIconViewComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printLoading: true,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.subscribers')
            },
            props: {
                form: {
                    subject: "",
                    message: "",
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: "id",
                    order_type: "desc",
                    email: "",
                    from_date: "",
                    to_date: "",
                },
            },
            modelValue: null,
            ENV:ENV
        };
    },
    mounted() {
        this.list();
    },
    computed: {
        subscribers: function () {
            return this.$store.getters["subscriber/lists"];
        },
        pagination: function () {
            return this.$store.getters["subscriber/pagination"];
        },
        paginationPage: function () {
            return this.$store.getters["subscriber/page"];
        },
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        search: function () {
            this.list();
        },
        clear: function () {
            this.props.search.paginate = 1;
            this.props.search.page = 1;
            this.props.search.email = "";
            this.props.search.from_date = "";
            this.props.search.to_date = "";
            this.modelValue = null;
            this.list();
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        handleDate: function (e) {
            if (e) {
                this.props.search.from_date = e[0];
                this.props.search.to_date = e[1];
            } else {
                this.props.search.from_date = null;
                this.props.search.to_date = null;

            }
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch("subscriber/lists", this.props.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("subscriber/destroy", {
                        id: id,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            null,
                            this.$t("menu.subscribers")
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

        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('subscriber/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.subscribers");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        }
    },
};

</script>
<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
