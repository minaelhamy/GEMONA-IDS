<template>
    <LoadingComponent :props="loading" />
    <div class="w-full max-w-5xl mx-auto rounded-2xl flex overflow-hidden gap-y-6 bg-white shadow-card mb-24 !sm:mb-0">
        <div class="w-full hidden sm:flex sm:max-w-xs md:max-w-sm flex-shrink-0 bg-[#f7f3fb] items-center justify-center p-8">
            <div class="text-center">
                <img :src="APP_URL + '/images/required/gemona-signup.png'" alt="GEMONA"
                    class="w-56 max-w-full mx-auto" loading="lazy">
            </div>
        </div>
        <form class="w-full p-6" @submit.prevent="signup">
            <div class="text-center mb-8">
                <h3 class="capitalize text-2xl mb-2 font-bold text-primary">{{ $t('label.sign_up') }}</h3>
                <p class="text-sm text-[#6E7191]">{{ $t('message.signup_approval_intro') }}</p>
            </div>

            <div v-if="pendingMessage" class="mb-6 rounded-xl border border-[#D9DBE9] bg-[#F7F3FB] p-4">
                <h4 class="font-bold text-primary mb-1">{{ $t('message.account_request_received') }}</h4>
                <p class="text-sm text-[#6E7191]">{{ pendingMessage }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="formName" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.name') }}
                    </label>
                    <input v-model="form.name" :class="errors.name ? 'invalid' : ''" id="formName" type="text"
                        class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                    <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                </div>

                <div>
                    <label for="formCountry" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.country') }}
                    </label>
                    <input v-model="form.country" :class="errors.country ? 'invalid' : ''" id="formCountry" type="text"
                        list="signupCountries"
                        @input="countryChange($event.target.value)"
                        @change="countryChange($event.target.value)"
                        class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                    <datalist id="signupCountries">
                        <option v-for="country in organizationCountries" :key="country" :value="country" />
                    </datalist>
                    <small class="db-field-alert" v-if="errors.country">{{ errors.country[0] }}</small>
                </div>

                <div>
                    <label for="formEmail" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.email') }}
                    </label>
                    <input v-model="form.email" :class="errors.email ? 'invalid' : ''" id="formEmail" type="email"
                        class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                    <small class="db-field-alert" v-if="errors.email">{{ errors.email[0] }}</small>
                </div>

                <div>
                    <label for="phone" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.phone') }}
                    </label>
                    <div :class="errors.phone ? 'invalid' : ''"
                        class="flex items-center gap-1.5 px-4 h-12 rounded-lg border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                        <div class="w-fit flex-shrink-0 dropdown-group">
                            <button type="button" class="flex items-center gap-1 dropdown-btn">
                                {{ flag }}
                                <span class="whitespace-nowrap flex-shrink-0 text-xs">{{ form.country_code }}</span>
                                <i class="fa-solid fa-caret-down text-xs"></i>
                            </button>
                            <ul
                                class="p-1.5 w-24 rounded-lg shadow-xl absolute top-8 -left-4 z-10 border border-gray-200 bg-white scale-y-0 origin-top dropdown-list !h-52 !overflow-x-hidden !overflow-y-auto thin-scrolling">
                                <li v-for="countryCode in countryCodes" @click="countryCodeChange(countryCode)"
                                    class="flex items-center gap-2 p-1.5 rounded-md cursor-pointer hover:bg-gray-100">
                                    {{ countryCode.flag_emoji }}
                                    <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code }}</span>
                                </li>
                            </ul>
                        </div>
                        <input v-model="form.phone" v-on:keypress="phoneNumber($event)" type="text" id="phone"
                            class="pl-2 text-sm w-full h-full" />
                    </div>
                    <small class="db-field-alert" v-if="errors.phone">{{ errors.phone[0] }}</small>
                    <small class="db-field-alert" v-if="errors.country_code">{{ errors.country_code[0] }}</small>
                </div>

                <div class="md:col-span-2">
                    <label for="organizationMode" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.organization') }}
                    </label>
                    <div class="flex flex-wrap gap-4 mb-3">
                        <label class="flex items-center gap-2 text-sm text-heading cursor-pointer">
                            <input type="radio" value="existing" v-model="form.organization_mode" @change="resetOrganizationName">
                            {{ $t('label.select_existing_organization') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm text-heading cursor-pointer">
                            <input type="radio" value="new" v-model="form.organization_mode" @change="form.organization_id = null">
                            {{ $t('label.add_new_organization') }}
                        </label>
                    </div>
                    <vue-select v-if="form.organization_mode === 'existing'"
                        class="w-full h-12 rounded-lg border border-[#D9DBE9]"
                        v-model="form.organization_id"
                        :options="filteredOrganizations"
                        label-by="display_name"
                        value-by="id"
                        :closeOnSelect="true"
                        :searchable="true"
                        :clearOnClose="true"
                        :placeholder="$t('label.select_your_organization')"
                        :search-placeholder="$t('label.search_organization')"
                        @search:change="organizationSearchChange"
                        @update:modelValue="organizationChange" />
                    <input v-else v-model="form.organization_name" :class="errors.organization_name ? 'invalid' : ''"
                        type="text" class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500"
                        :placeholder="$t('label.organization_name')" />
                    <small class="db-field-alert" v-if="errors.organization_id">{{ errors.organization_id[0] }}</small>
                    <small class="db-field-alert" v-if="errors.organization_name">{{ errors.organization_name[0] }}</small>
                    <small class="db-field-alert" v-if="errors.organization_mode">{{ errors.organization_mode[0] }}</small>
                </div>

                <div class="md:col-span-2">
                    <label for="formAddress" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.address') }}
                    </label>
                    <textarea v-model="form.address" :class="errors.address ? 'invalid' : ''" id="formAddress"
                        rows="3"
                        class="w-full px-4 py-3 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500"></textarea>
                    <small class="db-field-alert" v-if="errors.address">{{ errors.address[0] }}</small>
                </div>

                <div class="md:col-span-2">
                    <label for="formPassword" class="text-sm font-medium capitalize mb-1 field-title required">
                        {{ $t('label.password') }}
                    </label>
                    <input v-model="form.password" :class="errors.password ? 'invalid' : ''" id="formPassword" type="password"
                        class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                    <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
                </div>
            </div>

            <button type="submit"
                class="font-bold text-center w-full h-12 leading-12 rounded-full bg-primary text-white capitalize mt-6 mb-6">
                {{ $t('label.request_account_approval') }}
            </button>
            <div class="flex items-center justify-center gap-1.5">
                <span class="font-medium text-text">{{ $t('message.already_have_account') }}</span>
                <router-link class="capitalize font-bold text-primary" :to="{ name: 'auth.login' }">
                    {{ $t('label.sign_in') }}
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import appService from "../../../services/appService";
import ENV from "../../../config/env";
import alertService from "../../../services/alertService";

export default {
    name: "SignupComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                name: "",
                country: "",
                email: "",
                phone: "",
                country_code: "",
                organization_mode: "existing",
                organization_id: null,
                organization_name: "",
                address: "",
                password: ""
            },
            flag: "",
            errors: {},
            pendingMessage: "",
            organizationSearch: "",
            APP_URL: ENV.API_URL,
        }
    },
    computed: {
        countryCodes: function () {
            return this.$store.getters['frontendCountryCode/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        organizations: function () {
            return this.$store.getters['frontendSignup/organizations'];
        },
        organizationCountries: function () {
            return [...new Set(this.organizations.map((organization) => organization.country).filter(Boolean))].sort();
        },
        filteredOrganizations: function () {
            const search = this.organizationSearch.trim().toLowerCase();

            return this.organizations
                .filter((organization) => {
                    if (!search) {
                        return true;
                    }

                    return [
                        organization.name,
                        organization.country,
                        organization.address,
                        organization.type,
                    ].filter(Boolean).some((value) => String(value).toLowerCase().includes(search));
                })
                .map((organization) => ({
                    ...organization,
                    display_name: `${organization.name}${organization.country ? ' - ' + organization.country : ''}`
                }));
        },
    },
    mounted() {
        this.loading.isActive = true;
        Promise.all([
            this.$store.dispatch('frontendCountryCode/lists'),
            this.$store.dispatch('frontendSetting/lists'),
            this.$store.dispatch('frontendSignup/organizations')
        ]).then(() => {
            this.$store.dispatch('frontendCountryCode/show', this.setting.company_country_code).then(res => {
                this.form.country_code = res.data.data.calling_code;
                this.flag = res.data.data.flag_emoji;
                this.loading.isActive = false;
            }).catch(() => {
                this.loading.isActive = false;
            });
        }).catch(() => {
            this.loading.isActive = false;
        });
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        countryCodeChange: function (e) {
            this.flag = e.flag_emoji;
            this.form.country_code = e.calling_code;
        },
        normalizeCountryName: function (country) {
            return String(country || "")
                .toLowerCase()
                .replace(/\([^)]*\)/g, "")
                .replace(/[^a-z0-9]+/g, " ")
                .trim();
        },
        countryChange: function (country) {
            const selectedCountry = this.normalizeCountryName(country);

            if (!selectedCountry) {
                return;
            }

            const countryCode = this.countryCodes.find((item) => {
                const countryName = this.normalizeCountryName(item.country_name);
                return countryName === selectedCountry;
            });

            if (countryCode) {
                this.countryCodeChange(countryCode);
            }
        },
        selectedOrganization: function () {
            return this.organizations.find((organization) => Number(organization.id) === Number(this.form.organization_id));
        },
        organizationChange: function () {
            const organization = this.selectedOrganization();
            if (organization) {
                this.form.country = organization.country || this.form.country;
                this.countryChange(this.form.country);
                this.form.address = organization.address || this.form.address;
            }
        },
        organizationSearchChange: function (search) {
            this.organizationSearch = search || "";
        },
        resetOrganizationName: function () {
            this.form.organization_name = "";
        },
        resetForm: function () {
            this.form = {
                name: "",
                country: "",
                email: "",
                phone: "",
                country_code: this.form.country_code,
                organization_mode: "existing",
                organization_id: null,
                organization_name: "",
                address: "",
                password: ""
            };
            this.errors = {};
        },
        signup: function () {
            try {
                this.loading.isActive = true;
                this.pendingMessage = "";
                this.$store.dispatch("frontendSignup/signupValidation", this.form).then(() => {
                    this.$store.dispatch("frontendSignup/signup", this.form).then((res) => {
                        this.loading.isActive = false;
                        this.pendingMessage = res.data.message;
                        alertService.success(res.data.message, 'bottom-center');
                        this.$store.dispatch("frontendSignup/reset");
                        this.resetForm();
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    })
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        },
    }
}
</script>
