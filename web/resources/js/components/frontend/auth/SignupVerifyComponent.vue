<template>
    <LoadingComponent :props="loading" />
    <div class="w-full max-w-3xl mx-auto rounded-2xl flex overflow-hidden gap-y-6 bg-white shadow-card mb-24 sm:mb-0">
        <img :src="APP_URL + '/images/required/auth.jpg'" alt="banners"
            class="w-full hidden sm:block sm:max-w-xs md:max-w-sm flex-shrink-0" />
        <form class="w-full p-6" @submit.prevent="save">
            <div class="text-center mb-8 relative">
                <h3 class="capitalize text-2xl mb-2 font-bold text-primary"
                    v-if="props.form.country_code && props.form.phone">{{ $t('label.verify_number') }}</h3>
                <h3 class="capitalize text-2xl mb-2 font-bold text-primary" v-else>{{ $t('label.verify_email') }}</h3>
            </div>
            <div class="mb-6">
                <label class="text-sm first-letter:uppercase mb-1 text-heading">{{ $t('label.enter_the_code_sent_to') }}
                    <span class="font-medium" v-if="props.form.country_code && props.form.phone">
                        {{ props.form.country_code + '' + props.form.phone }}
                    </span>
                    <span class="font-medium" v-else>
                        {{ props.form.email }}
                    </span>
                </label>
                <input type="text" v-model="props.form.token"
                    class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                <small class="db-field-alert" v-if="errors">{{ errors }}</small>
                <small class="block mt-3 text-center text-sm font-medium">{{ $t('label.not_receive_code') }}
                    <button v-if="props.form.phone" @click.prevent="resendCodeToPhone" type="button"
                        class="font-bold text-primary">
                        {{ $t('button.resend_code') }}
                    </button>
                    <button v-else @click.prevent="resendCodeToEmail" type="button" class="font-bold text-primary">
                        {{ $t('button.resend_code') }}
                    </button>
                </small>
            </div>
            <button type="submit"
                class="font-bold text-center w-full h-12 leading-12 rounded-full bg-primary text-white mb-6">
                {{ $t('button.verify') }}</button>
            <router-link class="block text-center font-bold text-primary" :to="{ name: 'auth.login' }">
                {{ $t('label.back_to_sign_in') }}
            </router-link>
        </form>
    </div>
</template>

<script>

import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import ENV from "../../../config/env";
import _ from "lodash";

export default {
    name: "SignupVerifyComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                form: {
                    email: "",
                    phone: "",
                    token: "",
                    country_code: "",
                },
            },
            APP_URL: ENV.API_URL,
            errors: "",
            message: null,
        };
    },
    mounted() {
        this.phoneOrEmailChecking();
    },
    methods: {
        phoneOrEmailChecking: function () {
            this.loading.isActive = true;
            const otpPhone = this.$store.getters['frontendSignup/phone'];
            const otpEmail = this.$store.getters['frontendSignup/email'];
            if (Object.keys(otpPhone).length > 0 && otpPhone.otp.phone !== "") {
                this.props.form.phone = otpPhone.otp.phone;
                this.props.form.country_code = otpPhone.otp.country_code;
                this.props.form.email = "";
                this.loading.isActive = false;
            } else if (Object.keys(otpEmail).length > 0 && otpPhone.otp.email !== "") {
                this.props.form.email = otpPhone.otp.email;
                this.props.form.phone = "";
                this.props.form.country_code = "";
                this.loading.isActive = false;

            }
            else {
                this.$router.push({ name: 'auth.signup' });
            }
            this.loading.isActive = false;
        },
        resendCodeToPhone: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendSignup/otpPhone", this.props.form).then((res) => {
                    this.loading.isActive = false;
                    this.errors = "";
                    alertService.success(res.data.message, 'bottom-center');
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        resendCodeToEmail: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendSignup/otpEmail", this.props.form).then((res) => {
                    this.loading.isActive = false;
                    this.errors = "";
                    alertService.success(res.data.message, 'bottom-center');
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.message;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                if (this.props.form.country_code !== "" && this.props.form.phone !== "") {
                    this.$store.dispatch("verifyPhone", this.props.form).then((res) => {
                        this.signup();
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.message;
                    });
                } else {
                    this.$store.dispatch("verifyEmail", this.props.form).then((res) => {
                        this.signup();
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.message;
                    });
                }

            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        signup: function () {
            try {
                const form = this.$store.getters['frontendSignup/formData'];
                this.$store.dispatch("frontendSignup/signup", form).then(async (res) => {
                    await this.$store.dispatch("signupLoginVerify", form).then((res) => {
                        this.loading.isActive = false;
                        alertService.success(res.data.message);
                    }).catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.message;
                    });
                    this.$store.dispatch("frontendSignup/reset");
                    this.props.form = {
                        email: "",
                        phone: "",
                        token: "",
                        country_code: "",
                    };
                    this.errors = '';
                    this.$router.push({
                        name: "frontend.home",
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                
                    if (typeof err.response.data.errors === 'object') {
                        _.forEach(err.response.data.errors, (error) => {
                            alertService.error(error[0]);
                        });
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    },
}
</script>
