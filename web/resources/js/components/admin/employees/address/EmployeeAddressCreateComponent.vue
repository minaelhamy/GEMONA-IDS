<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent v-on:click="this.props.isMap = true" :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.address") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="full_name" class="db-field-title required">{{ $t("label.full_name") }}</label>
                            <input v-model="props.form.full_name" v-bind:class="errors.full_name ? 'invalid' : ''"
                                type="text" id="full_name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.full_name">{{ errors.full_name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title">{{ $t("label.email") }}</label>
                            <input v-model="props.form.email" v-bind:class="errors.email ? 'invalid' : ''" type="text"
                                id="email" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title required">{{
                                $t("label.phone")
                                }}</label>
                            <div :class="errors.phone ? 'invalid' : ''" class="db-field-control flex items-center">
                                <div class="w-fit flex-shrink-0 dropdown-group">
                                    <button type="button" class="flex items-center gap-1 dropdown-btn">
                                        {{ props.flag }}
                                        <span class="whitespace-nowrap flex-shrink-0 text-xs">{{ props.form.country_code
                                            }}</span>
                                        <i class="fa-solid fa-caret-down text-xs"></i>
                                    </button>
                                    <ul
                                        class="p-1.5 w-24 rounded-lg shadow-xl absolute top-8 -left-4 z-10 border border-gray-200 bg-white scale-y-0 origin-top dropdown-list !h-52 !overflow-x-hidden !overflow-y-auto thin-scrolling">
                                        <li v-for="countryCode in countryCodes" @click="changeCountry(countryCode)"
                                            class="flex items-center gap-2 p-1.5 rounded-md cursor-pointer hover:bg-gray-100">
                                            {{ countryCode.flag_emoji }}
                                            <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code
                                                }}</span>
                                        </li>
                                    </ul>

                                </div>
                                <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" v-bind:class="errors.phone
                                    ? 'invalid' : ''" type="text" id="phone" class=" pl-2 text-sm w-full h-full" />
                            </div>

                            <small class="db-field-alert" v-if="errors.phone">
                                {{ errors.phone[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6"><label class="db-field-title required" for="country">{{
                            $t('label.country') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="country"
                                v-bind:class="errors.country ? 'invalid' : ''" v-model="props.form.country"
                                @update:modelValue="callStates($event)" :options="countries" label-by="name"
                                value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.country">
                                {{ errors.country[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6"><label class="db-field-title required" for="state">{{
                            $t('label.state') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="state"
                                v-bind:class="errors.state ? 'invalid' : ''" v-model="props.form.state"
                                @update:modelValue="callCities($event)" :options="props.states" label-by="name"
                                value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.state">
                                {{ errors.state[0] }}
                            </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6"><label class="db-field-title required">{{
                            $t('label.city') }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="city"
                                v-bind:class="errors.city ? 'invalid' : ''" v-model="props.form.city"
                                :options="props.cities" label-by="name" value-by="name" :closeOnSelect="true"
                                :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.city">
                                {{ errors.city[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="zip_code" class="db-field-title">{{ $t("label.zip_code") }}</label>
                            <input v-model="props.form.zip_code" v-bind:class="errors.zip_code ? 'invalid' : ''"
                                type="text" id="zip_code" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.zip_code">{{ errors.zip_code[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="street_address">{{
                                $t('label.street_address') }}</label><input type="text"
                                :class="errors.address ? 'invalid' : ''" v-model="props.form.address"
                                class="db-field-control">
                            <small class="db-field-alert" v-if="errors.address">
                                {{ errors.address[0] }}
                            </small>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import statusEnum from "../../../../enums/modules/statusEnum";

export default {
    name: "EmployeeAddressCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: {
        props: Object,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            errors: {},
            flag: "",
            calling_code: "",
        };
    },
    mounted() {
        this.loading.isActive = true;
        setTimeout(() => {
            this.callCountry();
        }, 300);
        this.$store.dispatch('countryCode/lists');
        this.$store.dispatch('company/lists').then(companyRes => {
            this.$store.dispatch('countryCode/show', companyRes.data.data.company_country_code).then(res => {

                this.props.form.country_code = res.data.data.calling_code;
                this.calling_code = res.data.data.calling_code;
                this.props.flag = res.data.data.flag_emoji;
                this.flag = res.data.data.flag_emoji;

                this.loading.isActive = false;

            }).catch((err) => {
                this.loading.isActive = false;

            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        countryCodes: function () {
            return this.$store.getters['countryCode/lists'];
        },
        addButton: function () {
            return { title: this.$t("button.add_address") }
        },
        countries: function () {
            return this.$store.getters['country/lists'];
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        changeCountry: function (e) {
            this.props.flag = e.flag_emoji;
            this.$props.props.form.country_code = e.calling_code;
        },
        callCountry: function () {
            this.$store.dispatch('country/lists', { status: statusEnum.ACTIVE });
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
            this.$store.dispatch("employeeAddress/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                full_name: "",
                email: "",
                country_code: this.calling_code,
                phone: "",
                country: null,
                state: null,
                city: null,
                zip_code: "",
                address: "",
            };
            this.$props.props.flag = this.flag;
            this.$props.props.states = [];
            this.$props.props.cities = [];
        },
        save: function () {
            try {
                const tempId = this.$store.getters["employeeAddress/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("employeeAddress/save", {
                    search: this.props.search,
                    form: this.props.form,
                    id: this.$route.params.id
                }).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.address"));
                    this.props.form = {
                        full_name: "",
                        email: "",
                        country_code: this.calling_code,
                        phone: "",
                        country: null,
                        state: null,
                        city: null,
                        zip_code: "",
                        address: "",
                    };
                    this.$props.props.flag = this.flag;
                    this.$props.props.states = [];
                    this.$props.props.cities = [];;
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
};
</script>