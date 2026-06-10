<template>
    <LoadingComponent :props="loading" />
    <h2 class="capitalize text-2xl font-bold mb-7 text-primary">{{ $t('label.account_information') }}</h2>
    <form @submit.prevent="save" id="formElem">
        <div class="p-6 mb-6 rounded-2xl shadow-card bg-white">
            <h3 class="text-xl font-bold capitalize mb-5">{{ $t('label.personal_info') }}</h3>
            <div class="row">
                <div class="col-12 md:col-6">
                    <label for="name" class="field-title required">{{ $t('label.full_name') }}</label>
                    <input type="text" id="name" v-model="form.name" v-bind:class="errors.name ? 'invalid' : ''"
                        class="field-control">
                    <small class="db-field-alert" v-if="errors.name">
                        {{ errors.name[0] }}
                    </small>
                </div>
                <div class="col-12 md:col-6">
                    <label for="email" class="field-title required">{{ $t('label.email') }}</label>
                    <input type="email" id="email" v-model="form.email" v-bind:class="errors.email ? 'invalid' : ''"
                        class="field-control">
                    <small class="db-field-alert" v-if="errors.email">
                        {{ errors.email[0] }}
                    </small>
                </div>

                <div class="col-12 md:col-6">
                    <label for="phone" class="field-title required">
                        {{ $t("label.phone") }}
                    </label>
                    <div :class="errors.phone ? 'invalid' : ''" class="field-control flex items-center">
                        <div class="w-fit flex-shrink-0 dropdown-group">
                            <button type="button" class="flex items-center gap-1 dropdown-btn">
                                {{ flag }}
                                <span class="whitespace-nowrap flex-shrink-0 text-xs">{{ form.country_code
                                }}</span>
                                <i class="fa-solid fa-caret-down text-xs"></i>
                            </button>
                            <ul
                                class="p-1.5 w-24 rounded-lg shadow-xl absolute top-8 -left-4 z-10 border border-gray-200 bg-white scale-y-0 origin-top dropdown-list !h-52 !overflow-x-hidden !overflow-y-auto thin-scrolling">
                                <li v-for="countryCode in countryCodes" @click="changeCountryCode(countryCode)"
                                    class="flex items-center gap-2 p-1.5 rounded-md cursor-pointer hover:bg-gray-100">
                                    {{ countryCode.flag_emoji }}
                                    <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code }}</span>
                                </li>
                            </ul>

                        </div>
                        <input v-model="form.phone" v-on:keypress="phoneNumber($event)" v-bind:class="errors.phone
                            ? 'invalid' : ''" type="text" id="phone" class="pl-2 text-sm w-full h-full" />
                    </div>

                    <small class="db-field-alert" v-if="errors.phone">{{ errors.phone[0] }}</small>
                </div>

                <div class="col-12 md:col-6">
                    <label for="image" class="field-title">
                        {{ $t("label.upload_image") }}
                    </label>
                    <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                        class="field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" />
                    <small class="db-field-alert" v-if="errors.image">{{
                        errors.image[0]
                    }}</small>
                </div>
            </div>
        </div>
        <button type="submit"
            class="px-6 py-3 capitalize rounded-full whitespace-nowrap text-center font-semibold text-white bg-primary">{{
                $t('button.save_changes') }}</button>
    </form>
</template>

<script>
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import LoadingComponent from "../../components/LoadingComponent";
export default {
    name: "AccountInfoComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                name: "",
                email: "",
                phone: "",
                country_code: ""
            },
            flag: "",
            image: "",
            errors: {},
        };
    },
    mounted() {
        try {
            this.loading.isActive = true;
            const profile = this.$store.getters.authInfo;
            this.$store.dispatch('frontendCountryCode/lists');
            this.form = {
                name: profile.name,
                email: profile.email,
                phone: profile.phone,
                country_code: profile.country_code,
            };

            if (profile.country_code !== null) {
                this.$store.dispatch('frontendCountryCode/callingCode', profile.country_code).then(res => {
                    this.flag = res.data.data.flag_emoji;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }

            this.$store.dispatch('frontendSetting/lists').then(companyRes => {
                this.$store.dispatch('frontendCountryCode/show', companyRes.data.data.company_country_code).then(res => {

                    if (profile.country_code === null) {
                        this.flag = res.data.data.flag_emoji;
                        this.form.country_code = res.data.data.calling_code;
                    }

                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }).catch((err) => {
                this.loading.isActive = false;
            });
            this.loading.isActive = false;

        } catch (err) {
            this.loading.isActive = false;
            alertService.error(err);
        }
    },
    computed: {
        countryCodes: function () {
            return this.$store.getters['frontendCountryCode/lists'];
        },
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        changeCountryCode: function (e) {
            this.flag = e.flag_emoji;
            this.form.country_code = e.calling_code;

        },
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        save: function () {
            try {
                this.loading.isActive = true;
                const formData = new FormData();
                formData.append("name", this.form.name);
                formData.append("email", this.form.email);
                formData.append("phone", this.form.phone);
                formData.append("country_code", this.form.country_code);
                if (this.image) {
                    formData.append("image", this.image);
                }
                this.$store.dispatch("frontendEditProfile/updateProfile", formData).then((res) => {
                    this.$store.dispatch('updateAuthInfo', res.data.data).then(res => {
                        this.loading.isActive = false;
                        alertService.successFlip(1, this.$t("menu.profile"));
                        this.image = "";
                        this.errors = {};
                        this.$refs.imageProperty.value = null;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err);
                    });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
}
</script>
