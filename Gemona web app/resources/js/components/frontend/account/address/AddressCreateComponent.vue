<template>
    <LoadingComponent :props="loading" />

    <button data-modal="#address" @click="showTarget()" type="button"
        class="w-full rounded-2xl py-10 flex items-center justify-center gap-2.5 text-primary bg-[#FFF4F1]">
        <i class="lab-fill-circle-plus text-lg"></i>
        <span class="text-lg font-semibold capitalize">{{ addButton.title }}</span>
    </button>
    <div id="address"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-3xl">
            <div class="flex items-center justify-between gap-2 py-4 px-4 border-b border-slate-100">
                <h3 class="text-lg font-bold capitalize">{{ $t('label.address') }}</h3><button @click="reset()"
                    type="button" class="lab-line-circle-cross text-lg text-[#E93C3C]"></button>
            </div>
            <form class="w-full p-5" @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6"><label for="full_name"
                            class="text-sm font-medium capitalize mb-1 field-title required">{{ $t('label.full_name')
                            }}</label><input type="text" v-model="props.form.full_name"
                            :class="errors.full_name ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.full_name">
                            {{ errors.full_name[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="email" class="text-sm font-medium capitalize mb-1 field-title">{{
                            $t("label.email")
                        }}</label>
                        <input type="email" v-model="props.form.email" :class="errors.email ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="phone" class="text-sm font-medium capitalize mb-1 field-title required">{{
                            $t("label.phone")
                        }}</label>
                        <div :class="errors.phone ? 'invalid' : ''" class="field-control flex items-center">
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
                                        <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code }}</span>
                                    </li>
                                </ul>

                            </div>
                            <input v-model="props.form.phone" v-on:keypress="phoneNumber($event)" v-bind:class="errors.phone
                                ? 'invalid' : ''" type="text" id="phone" class="pl-2 text-sm w-full h-full" />
                        </div>

                        <small class="db-field-alert" v-if="errors.phone">
                            {{ errors.phone[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6"><label
                            class="text-sm font-medium capitalize mb-1 field-title required" for="country">{{
                                $t('label.country') }}</label>
                        <vue-select
                            class="frontend-select w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="country" v-bind:class="errors.country ? 'invalid' : ''" v-model="props.form.country"
                            @update:modelValue="callStates($event)" :options="countries" label-by="name" value-by="name"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.country">
                            {{ errors.country[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6"><label
                            class="text-sm font-medium capitalize mb-1 field-title required" for="state">{{
                                $t('label.state') }}</label>
                        <vue-select
                            class="frontend-select w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="state" v-bind:class="errors.state ? 'invalid' : ''" v-model="props.form.state"
                            @update:modelValue="callCities($event)" :options="props.states" label-by="name"
                            value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                            placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.state">
                            {{ errors.state[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6"><label
                            class="text-sm font-medium capitalize mb-1 field-title required">{{
                                $t('label.city') }}</label>
                        <vue-select
                            class="frontend-select w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="city" v-bind:class="errors.city ? 'invalid' : ''" v-model="props.form.city"
                            :options="props.cities" label-by="name" value-by="name" :closeOnSelect="true"
                            :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.city">
                            {{ errors.city[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6"><label class="text-sm font-medium capitalize mb-1"
                            for="zip_code">{{
                                $t('label.zip_code') }}
                        </label><input type="text" v-model="props.form.zip_code"
                            :class="errors.zip_code ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.zip_code">
                            {{ errors.zip_code[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6"><label
                            class="text-sm font-medium capitalize mb-1 field-title required" for="street_address">{{
                                $t('label.street_address') }}</label><input type="text"
                            :class="errors.address ? 'invalid' : ''" v-model="props.form.address"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.address">
                            {{ errors.address[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <div class="flex flex-wrap gap-6 mt-2"><button type="submit"
                                class="font-bold text-center h-12 leading-12 px-8 rounded-full whitespace-nowrap bg-primary text-white capitalize">{{
                                    $t('button.add_address')
                                }}</button><button @click="reset()" type="button"
                                class="font-bold text-center h-12 leading-12 px-8 rounded-full whitespace-nowrap bg-[#F7F7FC] capitalize">{{
                                    $t('button.cancel')
                                }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import appService from "../../../../services/appService";
import targetService from "../../../../services/targetService";
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../components/LoadingComponent";

export default {
    name: "AddressComponent",
    components: { LoadingComponent },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            errors: {},
            targetID: "address",
            addClass: "modal-active",
            flag: "",
            calling_code: "",
            worldMapData: [],


        }
    },
    mounted() {
        this.loading.isActive = true;
        setTimeout(() => {
            this.callCountry();
        }, 300);
        this.$store.dispatch('frontendCountryCode/lists');
        this.$store.dispatch('frontendSetting/lists').then(companyRes => {
            this.$store.dispatch('frontendCountryCode/show', companyRes.data.data.company_country_code).then(res => {

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
        addButton: function () {
            return { title: this.$t("button.add_new_address") }
        },
        countryCodes: function () {
            return this.$store.getters['frontendCountryCode/lists'];
        },
        countries: function () {
            return this.$store.getters['frontendCountryStateCity/countries'];
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        showTarget: function () {
            targetService.showTarget(this.targetID, this.addClass);
        },
        changeCountry: function (e) {
            this.props.flag = e.flag_emoji;
            this.$props.props.form.country_code = e.calling_code;
        },

        callCountry: function () {
            this.$store.dispatch('frontendCountryStateCity/countries');
        },

        callStates: function (countryName) {
            this.props.form.state = null;
            this.props.cities = [];
            this.props.form.city = null;

            this.$store.dispatch('frontendCountryStateCity/statesByCountry', countryName)
                .then((res) => {
                    this.props.states = res.data.data;
                })
        },
        callCities: function (stateName) {
            this.props.form.city = null;

            this.$store.dispatch('frontendCountryStateCity/citiesByState', stateName)
                .then((res) => {
                    this.props.cities = res.data.data;
                })
        },
        reset: function () {
            targetService.hideTarget(this.targetID, this.addClass);
            this.$store.dispatch("frontendAddress/reset").then().catch();
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
                const tempId = this.$store.getters["frontendAddress/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("frontendAddress/save", this.props).then((res) => {
                    targetService.hideTarget(this.targetID, this.addClass);
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