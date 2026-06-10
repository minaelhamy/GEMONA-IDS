<template>
    <LoadingComponent :props="loading" />
    <div v-if="show" class="mb-6 rounded-2xl shadow-card">
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 border-b border-gray-100">
            <h4 class="font-bold capitalize">{{ title }}</h4>
            <div class="flex flex-wrap items-center gap-4">
                <button v-if="Object.keys(selectedAddress).length > 0" type="button"
                    @click.prevent="edit(selectedAddress)"
                    class="px-3 h-8 leading-8 rounded-full flex items-center gap-2 bg-[#E6FFF0] text-success">
                    <i class="lab-fill-edit"></i>
                    <span class="text-sm font-medium capitalize whitespace-nowrap">{{ $t('button.edit') }}</span>
                </button>
                <button type="button" @click.prevent="showTarget(slug + '-address-modal', 'modal-active')"
                    class="px-3 h-8 leading-8 rounded-full flex items-center gap-2 bg-[#FFF4F1] text-primary">
                    <i class="lab-fill-circle-plus"></i>
                    <span class="text-sm font-medium capitalize whitespace-nowrap">{{ $t('button.add_new') }}</span>
                </button>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4">
            <div :class="Object.keys(selectedAddress).length > 0 && address.id === selectedAddress.id ? 'border-primary/50 bg-[#FFF4F1]' : 'border-[#F7F7F7] bg-[#F7F7F7]'"
                @click.prevent="activeAddress(address)" v-for="address in addresses" :key="address"
                class="py-3 px-4 rounded-lg cursor-pointer border transition-all duration-300">
                <span class="text-base font-medium capitalize mb-1">{{ address.full_name }}</span>
                <span v-if="address.phone" class="block text-sm leading-6">{{
                    address.country_code ?? ''
                    }} {{ address.phone }},</span>
                <span v-if="address.email" class="block text-sm leading-6">{{ address.email }},</span>
                <span v-if="address.state" class="block text-sm leading-6">{{ address.state }},</span>
                <span v-if="address.city" class="block text-sm leading-6">{{ address.city }},</span>
                <span v-if="address.country" class="block text-sm leading-6">{{ address.country }},</span>
                <span v-if="address.address" class="block text-sm leading-6">{{ address.address }}<span
                        v-if="address.zip_code">,</span></span>
                <span v-if="address.zip_code" class="block text-sm leading-6">{{ address.zip_code }}</span>
            </div>
        </div>
    </div>

    <div :id="slug + '-address-modal'"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-3xl">
            <div class="flex items-center justify-between gap-2 py-4 px-4 border-b border-slate-100">
                <h3 class="text-lg font-bold capitalize">{{ $t('label.address') }}</h3>
                <button @click.prevent="reset" type="button"
                    class="lab-line-circle-cross text-lg text-[#E93C3C]"></button>
            </div>
            <form class="w-full p-5" @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="full_name" class="text-sm font-medium capitalize mb-1 field-title required">
                            {{ $t('label.full_name') }}
                        </label>
                        <input type="text" v-model="address.form.full_name" :class="errors.full_name ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.full_name">
                            {{ errors.full_name[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="email" class="text-sm font-medium capitalize mb-1 field-title">
                            {{ $t("label.email") }}
                        </label>
                        <input type="email" v-model="address.form.email" :class="errors.email ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.email">
                            {{ errors.email[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="phone" class="text-sm font-medium capitalize mb-1 field-title required">
                            {{ $t("label.phone") }}
                        </label>
                        <div :class="errors.phone ? 'invalid' : ''" class="field-control flex items-center">
                            <div class="w-fit flex-shrink-0 dropdown-group">
                                <button type="button" class="flex items-center gap-1 dropdown-btn">
                                    {{ address.flag }}
                                    <span class="whitespace-nowrap flex-shrink-0 text-xs">
                                        {{ address.form.country_code }}
                                    </span>
                                    <i class="fa-solid fa-caret-down text-xs"></i>
                                </button>
                                <ul
                                    class="p-1.5 w-24 rounded-lg shadow-xl absolute top-8 -left-4 z-10 border border-gray-200 bg-white scale-y-0 origin-top dropdown-list !h-52 !overflow-x-hidden !overflow-y-auto thin-scrolling">
                                    <li v-for="countryCode in countryCodes" @click.prevent="changeCountry(countryCode)"
                                        class="flex items-center gap-2 p-1.5 rounded-md cursor-pointer hover:bg-gray-100">
                                        {{ countryCode.flag_emoji }}
                                        <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code }}</span>
                                    </li>
                                </ul>
                            </div>
                            <input v-model="address.form.phone" v-on:keypress="phoneNumber($event)"
                                :class="errors.phone ? 'invalid' : ''" type="text" id="phone"
                                class="pl-2 text-sm w-full h-full" />
                        </div>

                        <small class="db-field-alert" v-if="errors.phone">
                            {{ errors.phone[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="text-sm font-medium capitalize mb-1 field-title required" for="country">
                            {{ $t('label.country') }}
                        </label>
                        <vue-select
                            class="w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="country" :class="errors.country ? 'invalid' : ''" v-model="address.form.country"
                            @update:modelValue="callStates($event)" :options="countries" label-by="name" value-by="name"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.country">
                            {{ errors.country[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="text-sm font-medium capitalize mb-1 field-title required" for="state">
                            {{ $t('label.state') }}
                        </label>
                        <vue-select
                            class="w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="state" v-bind:class="errors.state ? 'invalid' : ''" v-model="address.form.state"
                            @update:modelValue="callCities($event)" :options="address.states" label-by="name"
                            value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                            placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.state">
                            {{ errors.state[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="text-sm font-medium capitalize mb-1 field-title required">
                            {{ $t('label.city') }}
                        </label>
                        <vue-select
                            class="w-full h-12 px-4 rounded-lg text-base capitalize border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500 appearance-none"
                            id="city" v-bind:class="errors.city ? 'invalid' : ''" v-model="address.form.city"
                            :options="address.cities" label-by="name" value-by="name" :closeOnSelect="true"
                            :searchable="true" :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.city">
                            {{ errors.city[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="text-sm font-medium capitalize mb-1" for="zip_code">
                            {{ $t('label.zip_code') }}
                        </label>
                        <input type="text" v-model="address.form.zip_code" :class="errors.zip_code ? 'invalid' : ''"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.zip_code">
                            {{ errors.zip_code[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="text-sm font-medium capitalize mb-1 field-title required" for="street_address">
                            {{ $t('label.street_address') }}
                        </label>
                        <input type="text" :class="errors.address ? 'invalid' : ''" v-model="address.form.address"
                            class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <small class="db-field-alert" v-if="errors.address">
                            {{ errors.address[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <div class="flex flex-wrap gap-6 mt-2">
                            <button type="submit"
                                class="font-bold text-center h-12 leading-12 px-8 rounded-full whitespace-nowrap bg-primary text-white capitalize">
                                {{ $t('button.save_address') }}
                            </button>

                            <button @click.prevent="reset" type="button"
                                class="font-bold text-center h-12 leading-12 px-8 rounded-full whitespace-nowrap bg-[#F7F7FC] capitalize">
                                {{ $t('button.cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>


<script>
import orderTypeEnum from "../../../../enums/modules/orderTypeEnum";
import appService from "../../../../services/appService";
import targetService from "../../../../services/targetService";
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../components/LoadingComponent.vue";


export default {
    name: "AddressComponent",
    props: {
        "show": { type: Boolean, Default: false },
        "slug": { type: String, Default: "shipping" },
        "title": { type: String },
        "selectedAddress": { type: Object },
        "method": { type: Function }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            orderTypeEnum: orderTypeEnum,
            address: {
                form: {
                    full_name: "",
                    email: "",
                    country_code: null,
                    phone: "",
                    country: null,
                    state: null,
                    city: null,
                    zip_code: "",
                    address: "",
                },
                search: {
                    paginate: 0,
                    order_column: "id",
                    order_type: "asc",

                },
                flag: "",
                calling_code: "",
                states: [],
                cities: []
            },
            worldMapData: [],
            activeAddressId: null,
            errors: {},
        }
    },
    components: {
        LoadingComponent
    },
    computed: {
        addresses: function () {
            return this.$store.getters["frontendAddress/lists"];
        },
        countryCodes: function () {
            return this.$store.getters['frontendCountryCode/lists'];
        },
        countries: function () {
            return this.$store.getters['frontendCountryStateCity/countries'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        setTimeout(() => {
            this.callCountry();
        }, 300);
        this.$store.dispatch("frontendAddress/lists", {
            search: {
                paginate: 0,
                order_column: "id",
                order_type: "asc",
            }
        }).then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch('frontendCountryCode/lists');
        this.$store.dispatch('company/lists').then(companyRes => {
            this.$store.dispatch('frontendCountryCode/show', companyRes.data.data.company_country_code).then(res => {
                this.address.form.country_code = res.data.data.calling_code;
                this.address.calling_code = res.data.data.calling_code;
                this.address.flag = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        activeAddress: function (address) {
            this.activeAddressId = address.id;
            this.method(address);
        },
        showTarget: function (targetID, addClass) {
            targetService.showTarget(targetID, addClass);
        },
        callCountry: function () {
            this.$store.dispatch('frontendCountryStateCity/countries');
        },
        changeCountry: function (e) {
            this.address.flag = e.flag_emoji;
            this.address.form.country_code = e.calling_code;
        },
        callStates: function (countryName) {
            this.address.form.state = null;
            this.address.cities = [];
            this.address.states = [];
            this.address.form.city = null;

            this.$store.dispatch('frontendCountryStateCity/statesByCountry', countryName)
                .then((res) => {
                    this.address.states = res.data.data;
                })
        },
        callCities: function (stateName) {
            this.address.form.city = null;
            this.address.cities = []

            this.$store.dispatch('frontendCountryStateCity/citiesByState', stateName)
                .then((res) => {
                    this.address.cities = res.data.data;
                })
        },
        reset: function () {
            targetService.hideTarget(this.slug + '-address-modal', 'modal-active');
            this.$store.dispatch("frontendAddress/reset").then().catch();
            this.errors = {};
            this.address.form = {
                full_name: "",
                email: "",
                country_code: this.address.calling_code,
                phone: "",
                country: null,
                state: null,
                city: null,
                zip_code: "",
                address: "",
            };
            this.address.states = [];
            this.address.cities = [];
        },
        save: function () {
            try {
                const tempId = this.$store.getters["frontendAddress/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("frontendAddress/save", this.address).then((res) => {
                    targetService.hideTarget(this.slug + '-address-modal', 'modal-active');
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.address"));
                    this.address.form = {
                        full_name: "",
                        email: "",
                        country_code: this.address.calling_code,
                        phone: "",
                        country: null,
                        state: null,
                        city: null,
                        zip_code: "",
                        address: "",
                    };
                    this.address.states = [];
                    this.address.cities = [];
                    this.errors = {};
                    this.activeAddress(res.data.data);
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        edit: function (address) {
            if (Object.keys(this.selectedAddress).length > 0) {
                targetService.showTarget(this.slug + '-address-modal', 'modal-active');
                this.loading.isActive = true;
                this.$store.dispatch("frontendAddress/edit", address.id).then(async (res) => {
                    this.loading.isActive = false;

                    if (address.state !== "") {
                        await this.$store.dispatch('frontendCountryStateCity/statesByCountry', address.country)
                            .then((res) => {
                                this.address.states = res.data.data;
                            })
                        await this.$store.dispatch('frontendCountryStateCity/citiesByState', address.state)
                            .then((res) => {
                                this.address.cities = res.data.data;
                            })

                        if (address.city === "") {
                            this.address.form.city = null;
                        }
                    } else {
                        await this.$store.dispatch('frontendCountryStateCity/statesByCountry', address.country)
                            .then((res) => {
                                this.address.states = res.data.data;
                            })
                        this.address.form.state = null;
                        this.address.form.city = null;
                    }

                    this.address.form = {
                        full_name: address.full_name,
                        email: address.email,
                        country_code: address.country_code,
                        phone: address.phone,
                        country: address.country,
                        state: address.state,
                        city: address.city,
                        zip_code: address.zip_code,
                        address: address.address,
                    };

                    if (address.state === "") {
                        this.address.form.state = null;
                    }

                    if (address.city === "") {
                        this.address.form.city = null;
                    }

                    this.$store.dispatch('frontendCountryCode/callingCode', address.country_code).then(res => {
                        this.address.flag = res.data.data.flag_emoji;
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });
                }).catch((err) => {
                    alertService.error(err.response.data.message);
                });
            }
        }
    }
}
</script>