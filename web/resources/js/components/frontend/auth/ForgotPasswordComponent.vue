<template>
    <LoadingComponent :props="loading" />
    <div class="w-full max-w-3xl mx-auto rounded-2xl flex overflow-hidden gap-y-6 bg-white shadow-card mb-24 !sm:mb-0">
        <img :src="APP_URL + '/images/required/auth.jpg'" alt="banners"
            class="w-full hidden sm:block sm:max-w-xs md:max-w-sm flex-shrink-0">
        <form class="w-full p-6" @submit.prevent="save">
            <div class="text-center relative mb-8">
                <router-link :to="{ name: 'auth.login' }" class="absolute top-1/2 ltr:left-0 rtl:right-0 -translate-y-1/2">
                    <i class="lab-line-long-arrow-left text-2xl !font-semibold text-primary"></i>
                </router-link>
                <h3 class="capitalize text-2xl mb-2 font-bold text-primary">{{ $t('label.forgot_password') }}</h3>
                <div v-if="errors.validation"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
                    <span class="block sm:inline">{{ errors.validation }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="close">
                        <i class="lab lab-close-circle-line margin-top-5-px"></i>
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <label :for="!toggleValue ? 'formEmail' : 'phone'"
                        class="text-sm font-medium capitalize mb-1 field-title required">{{ inputLabel
                        }}</label>
                    <button type="button" class="text-sm font-medium capitalize mb-1 underline text-primary"
                        @click="handleField()">{{ inputButton }}</button>
                </div>
                <div v-if="toggleValue" :class="errors.phone ? 'invalid' : ''"
                    class="flex items-center gap-1.5 px-4 h-12 rounded-lg border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500">
                    <div class="w-fit flex-shrink-0 dropdown-group">
                        <button type="button" class="flex items-center gap-1 dropdown-btn">
                            {{ flag }}
                            <span class="whitespace-nowrap flex-shrink-0 text-xs">{{ form.country_code
                            }}</span>
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
                    <input v-model="form.phone" v-on:keypress="phoneNumber($event)" v-bind:class="errors.phone
                        ? 'invalid' : ''" type="text" id="phone" class="pl-2 text-sm w-full h-full" />
                </div>
                <input v-if="!toggleValue" v-model="form.email" :class="errors.email ? 'invalid' : ''" id="formEmail"
                    type="email"
                    class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                <small class="db-field-alert" v-if="errors.email_or_phone">{{ errors.email_or_phone }}</small>
                <span v-else>
                    <small class="db-field-alert" v-if="errors.phone && toggleValue">{{ errors.phone[0] }}</small>
                    <small class="db-field-alert" v-if="errors.email && !toggleValue">{{ errors.email[0] }}</small>
                </span>
            </div>
            <button type="submit"
                class="font-bold text-center w-full h-12 leading-12 rounded-full bg-primary text-white capitalize mb-6">
                {{ $t('label.get_code') }}
            </button>
            <div class="text-center">
                <router-link :to="{ name: 'auth.login' }" class="font-bold text-primary">{{ $t('label.back_to_signin')
                }}</router-link>
            </div>
        </form>
    </div>
</template>

<script>
import router from "../../../router";
import askEnum from "../../../enums/modules/askEnum"
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import ENV from "../../../config/env";

export default {
    name: "ForgotPasswordComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                email: "",
                phone: "",
                country_code: "",
                password: ""
            },
            flag: "",
            errors: {},
            permissions: {},
            firstMenu: null,
            demo: ENV.DEMO,
            APP_URL: ENV.API_URL,
            toggleValue: false,
            inputLabel: this.$t('label.email'),
            inputButton: this.$t('label.use_phone_instead')
        }
    },
    computed: {
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        },
        countryCodes: function () {
            return this.$store.getters['frontendCountryCode/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch('frontendCountryCode/lists');
        this.$store.dispatch('frontendSetting/lists').then(res => {
            this.$store.dispatch('frontendCountryCode/show', res.data.data.company_country_code).then(res => {
                this.form.country_code = res.data.data.calling_code;
                this.flag = res.data.data.flag_emoji;

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
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch('forgotPassword', this.form).then((res) => {
                    this.loading.isActive = false;
                    if (this.setting.site_phone_verification === askEnum.YES && this.form.phone !== "" && (this.demo !== 'true' || this.demo !== 'TRUE' || this.demo !== 'True' || this.demo !== '1' || this.demo !== 1)) {
                        alertService.success(res.data.message, 'bottom-center');
                        this.$router.push({ name: "auth.forgotPasswordVerify" });
                    } else if (this.setting.site_email_verification === askEnum.YES && this.form.email !== "" && (this.demo !== 'true' || this.demo !== 'TRUE' || this.demo !== 'True' || this.demo !== '1' || this.demo !== 1)) {
                        alertService.success(res.data.message, 'bottom-center');
                        this.$router.push({ name: "auth.forgotPasswordVerify" });
                    }
                    else {
                        this.$router.push({ name: "auth.resetPassword" });
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        handleField: function () {
            this.toggleValue = !this.toggleValue

            if (this.toggleValue) {
                this.form.email = "";
                this.inputLabel = this.$t('label.phone');
                this.inputButton = this.$t('label.use_email_instead');
            }
            else {
                this.form.phone = "";
                this.inputLabel = this.$t('label.email');
                this.inputButton = this.$t('label.use_phone_instead');
            }
        },
        countryCodeChange: function (e) {
            this.flag = e.flag_emoji;
            this.form.country_code = e.calling_code;
        },
        close: function () {
            this.errors = {}
        },
    }
}
</script>
