<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.damages') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('damage-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div class="dropdown-list db-card-filter-dropdown-list">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <router-link @click="reset" v-if="permissionChecker('damage_create')" to="damages/create"
                        class="db-btn h-[37px] text-white bg-primary">
                        <i class="lab lab-line-add-circle"></i>
                        <span>{{ $t('button.add_damage') }}</span>
                    </router-link>
                </div>
            </div>
            <div class="table-filter-div" id="damage-filter">
                <form class="p-4 sm:p-5 mb-5 w-full d-block" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="date" class="db-field-title after:hidden">{{ $t('label.date') }}</label>

                                <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" 
                                @update:modelValue="handleEndDate" v-model="props.search.date" :range="false" >
                            </Datepicker>
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="searchCode" class="db-field-title after:hidden">{{ $t('label.reference_no')
                            }}</label>
                            <input id="searchCode" v-model="props.search.reference_no" type="text" class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="total" class="db-field-title after:hidden">{{ $t('label.total') }}</label>
                            <input id="total" v-model="props.search.total" v-on:keypress="floatNumber($event)" type="text"
                                class="db-field-control">
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="note" class="db-field-title after:hidden">{{ $t('label.note') }} </label>
                            <input id="note" v-model="props.search.note" type="text" class="db-field-control">
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
                            <th class="db-table-head-th">{{ $t('label.date') }}</th>
                            <th class="db-table-head-th">{{ $t('label.reference_no') }}</th>
                            <th class="db-table-head-th">{{ $t('label.total') }}</th>
                            <th class="db-table-head-th">{{ $t('label.note') }}</th>
                            <th v-if="permissionChecker('damage_show') || permissionChecker('damage_edit') || permissionChecker('damage_delete')"
                                class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body border-b border-gray-200" v-if="damages.length > 0">
                        <tr class="db-table-body-tr" v-for="(damage, index) of damages" :key="index">
                            <td class="db-table-body-td">{{ damage.converted_date }}</td>
                            <td class="db-table-body-td">{{ damage.reference_no }}</td>
                            <td class="db-table-body-td">{{ damage.total_flat_price }}</td>
                            <td class="db-table-body-td"> <span v-html="textShortener(damage.note)"></span></td>
                            <td class="db-table-body-td hidden-print"
                                v-if="permissionChecker('damage_show') || permissionChecker('damage_edit') || permissionChecker('damage_delete')">
                                <SmIconViewComponent :link="'admin.damage.show'" :id="damage.id"
                                    v-if="permissionChecker('damage_show')" />
                                <SmIconEditComponent @click="edit(damage)" :link="'admin.damage.edit'" :id="damage.id"
                                    v-if="permissionChecker('damage_edit')" />
                                <SmIconDeleteComponent @click="destroy(damage.id)"
                                    v-if="permissionChecker('damage_delete')" />
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
            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6" v-if="damages.length > 0">
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
import LoadingComponent from "../components/LoadingComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconEditComponent from '../components/buttons/SmIconEditComponent';
import PaginationBox from "../components/pagination/PaginationBox";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import appService from "../../../services/appService";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import alertService from "../../../services/alertService";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ENV from "../../../config/env";
export default {
    name: 'DamageListComponent',
    components: {
        PaginationBox,
        PaginationSMBox,
        PaginationTextComponent,
        TableLimitComponent,
        FilterComponent,
        PrintComponent,
        ExcelComponent,
        ExportComponent,
        Datepicker,
        SmIconViewComponent,
        SmIconDeleteComponent,
        LoadingComponent,
        SmIconSidebarModalEditComponent,
        SmIconEditComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            printObj: {
                id: "print",
                popTitle: this.$t('menu.damages')
            },
            props: {
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    date: "",
                    reference_no: "",
                    total: null,
                    note: ""
                }
            },
            items: [],
            ENV: ENV
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        damages: function () {
            return this.$store.getters['damage/lists'];
        },
        pagination: function () {
            return this.$store.getters['damage/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['damage/page'];
        },
    },
    methods: {
        textShortener: function (text, number = 30) {
            text = appService.htmlTagRemover(text);
            return appService.textShortener(text, number);
        },
        search: function () {
            this.list();
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        handleEndDate: function (e) {
            if (e) {
                this.props.search.date = e;
            } else {
                this.props.search.date = null;

            }
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        edit: function (damage) {
            this.$store.dispatch('damage/edit', damage.id);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            this.props.search.page = page;
            this.$store.dispatch('damage/lists', this.props.search)
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                })
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("damage/destroy", {
                        id: id,
                        search: this.props.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            null,
                            this.$t("menu.damages")
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
        clear: function () {
            this.props.search = {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: 'id',
                order_type: 'desc',
                user_id: null,
                date: "",
                reference_no: "",
                total: null,
                note: ""
            },
                this.list();
        },
        xls: function () {
            this.loading.isActive = true;
            this.$store.dispatch('damage/export', this.props.search).then(res => {
                this.loading.isActive = false;
                const blob = new Blob([res.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = this.$t("menu.damages");
                link.click();
                URL.revokeObjectURL(link.href);
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });

        },
        reset: function () {
            this.$store.dispatch('damage/reset').then().catch();
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
