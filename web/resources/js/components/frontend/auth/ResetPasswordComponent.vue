<template>
    <LoadingComponent :props="loading" />
    <div class="w-full max-w-3xl mx-auto rounded-2xl flex overflow-hidden gap-y-6 bg-white shadow-card mb-24 !sm:mb-0">
        <img :src="APP_URL + '/images/required/auth.jpg'" alt="banners"
            class="w-full hidden sm:block sm:max-w-xs md:max-w-sm flex-shrink-0">
        <form class="w-full p-6" @submit.prevent="save">
            <div class="text-center mb-8">
                <h3 class="capitalize text-2xl font-bold text-primary">{{ $t('label.reset_password') }}</h3>
            </div>

            <div class="mb-6">
                <label for="formPassword" class="field-title required">{{
                    $t('label.new_password') }}</label>
                <input v-model="form.password" :class="errors.password ? 'invalid' : ''" id="formPassword" type="password"
                    class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                <small class="db-field-alert" v-if="errors.password">{{ errors.password[0] }}</small>
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="field-title required">{{
                    $t('label.confirm_password')
                }}</label>
                <input v-model="form.password_confirmation" :class="errors.password_confirmation ? 'invalid' : ''"
                    id="password_confirmation" type="password"
                    class="w-full h-12 px-4 rounded-lg text-base border border-[#D9DBE9] hover:border-primary/30 focus-within:border-primary/30 transition-all duration-500" />
                <small class="db-field-alert" v-if="errors.password_confirmation">{{ errors.password_confirmation[0]
                }}</small>
            </div>
            <button type="submit"
                class="font-bold text-center w-full h-12 leading-12 rounded-full bg-primary text-white capitalize mb-6">
                {{ $t('button.submit') }}
            </button>
        </form>
    </div>
</template>

<script>
import router from "../../../router";
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import ENV from "../../../config/env";

export default {
    name: "ResetPasswordComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                password: "",
                password_confirmation: "",
                email: "",
                phone: "",
                country_code: "",
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
    },
    mounted() {
        this.phoneOrEmailChecking();
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        phoneOrEmailChecking: function () {
            this.loading.isActive = true;
            const otpPhone = this.$store.getters['phone'];
            const otpEmail = this.$store.getters['email'];
            if (Object.keys(otpPhone).length > 0 && otpPhone.otp.phone !== "") {
                this.form.phone = otpPhone.otp.phone;
                this.form.country_code = otpPhone.otp.country_code;
                this.form.email = "";
                this.loading.isActive = false;
            } else if (Object.keys(otpEmail).length > 0 && otpPhone.otp.email !== "") {
                this.form.email = otpPhone.otp.email;
                this.form.phone = "";
                this.form.country_code = "";
                this.loading.isActive = false;
            }
            else {
                this.$router.push({ name: 'auth.login' });
            }
            this.loading.isActive = false;
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch('resetPassword', this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.success(res.data.message);
                    this.$store.dispatch("reset");
                    if (this.carts.length > 0) {
                        router.push({ name: "frontend.checkout" });
                    } else {
                        router.push({ name: "frontend.home" });
                    }
                    router.push({ name: "frontend.home" });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        close: function () {
            this.errors = {}
        },
    }
}
</script>
