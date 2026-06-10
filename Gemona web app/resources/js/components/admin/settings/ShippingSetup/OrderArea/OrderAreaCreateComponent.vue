<template>
    <LoadingComponent :props="loading" />

    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.order_area") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="country">{{
                                $t('label.country') }}</label>
                            <vue-select class="db-field-control" id="country"
                                v-bind:class="errors.country ? 'invalid' : ''" v-model="props.form.country"
                                @update:modelValue="callStates($event)" :options="countries" label-by="name"
                                value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.country">
                                {{ errors.country[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="state">{{
                                $t('label.state') }}</label>
                            <vue-select class="db-field-control" id="state"
                                v-bind:class="errors.state ? 'invalid' : ''" v-model="props.form.state"
                                @update:modelValue="callCities($event)" :options="props.states" label-by="name"
                                value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.state">
                                {{ errors.state[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">{{
                                $t('label.city') }}</label>
                            <vue-select class="db-field-control" id="city"
                                v-bind:class="errors.city ? 'invalid' : ''" v-model="props.form.city"
                                :options="props.cities" label-by="name" value-by="name" :closeOnSelect="true"
                                :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.city">
                                {{ errors.city[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="shipping_cost">{{
                                $t('label.shipping_cost') }}
                            </label>
                            <input v-on:keypress="floatNumber($event)" type="text" v-model="props.form.shipping_cost"
                                :class="errors.shipping_cost ? 'invalid' : ''" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.shipping_cost">
                                {{ errors.shipping_cost[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t('label.status') }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t('label.active') }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status"
                                            type="radio" id="inactive" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save"></i>
                                    <span>{{ $t("button.save") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import appService from "../../../../../services/appService";
import alertService from "../../../../../services/alertService";
import LoadingComponent from "../../../components/LoadingComponent";
import statusEnum from "../../../../../enums/modules/statusEnum";
import SmModalCreateComponent from "../../../components/buttons/SmModalCreateComponent";

export default {
    name: "OrderAreaCreateComponent",
    components: { LoadingComponent, SmModalCreateComponent },
    props: ["props"],
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
            errors: {},

        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t("button.add_order_area") }
        },
        countries: function () {
            return this.$store.getters['country/lists'];
        }
    },
    mounted() {
        setTimeout(() => {
            this.callCountry();
        }, 300);
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },

        phoneNumber(e) {
            return appService.phoneNumber(e);
        },

        callCountry: function () {
            this.loading.isActive = true;
            this.$store.dispatch('country/lists', { order_column: "name", order_type: "asc" });
            this.loading.isActive = false;
        },

        callStates: function (countryName) {
            this.props.states = [];
            this.props.form.state = null;
            this.props.cities = [];
            this.props.form.city = null;

            this.$store.dispatch('state/statesByCountry', countryName)
                .then((res) => {
                    this.props.states = res.data.data;
                })
        },
        callCities: function (stateName) {
            this.props.form.city = null;
            this.props.cities = [];

            this.$store.dispatch('city/citiesByState', stateName)
                .then((res) => {
                    this.props.cities = res.data.data;
                })
        },
        reset: function () {
            appService.modalHide();
            this.$store.dispatch("orderArea/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                country: null,
                state: null,
                city: null,
                shipping_cost: "",
                status: statusEnum.ACTIVE,
            };
            this.$props.props.flag = this.flag;
            this.$props.props.states = [];
            this.$props.props.cities = [];
        },
        save: function () {
            try {
                const tempId = this.$store.getters["orderArea/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("orderArea/save", this.props).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("menu.order_area"));
                    this.props.form = {
                        country: null,
                        state: null,
                        city: null,
                        shipping_cost: "",
                        status: statusEnum.ACTIVE,
                    };
                    this.$props.props.states = [];
                    this.$props.props.cities = [];
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    }
}
</script>