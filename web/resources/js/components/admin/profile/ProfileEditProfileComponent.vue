<template>
    <div class="col-12">
        <BreadcrumbComponent />
    </div>

    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t("button.edit_profile") }}</h3>
            </div>

            <div class="db-card-body">
                <form @submit.prevent="save" class="w-full d-block">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{ $t('label.name') }}</label>
                            <input type="text" id="name" class="db-field-control" v-model="form.name"
                                :class="errors.name ? 'invalid' : ''">
                            <small class="db-field-alert" v-if="errors.name">
                                {{ errors.name[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title required">{{ $t('label.email') }}</label>
                            <input type="text" id="email" class="db-field-control" v-model="form.email"
                                :class="errors.email ? 'invalid' : ''">
                            <small class="db-field-alert" v-if="errors.email">
                                {{ errors.email[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title required">{{ $t("label.phone") }}</label>
                            <div :class="errors.phone ? 'invalid' : ''" class="db-field-control flex items-center">
                                <div class="w-fit flex-shrink-0 dropdown-group">
                                    <button type="button" class="flex items-center gap-1 dropdown-btn">
                                        {{ flag }}
                                        <span class="whitespace-nowrap flex-shrink-0 text-xs">{{ form.country_code
                                        }}</span>
                                        <i class="fa-solid fa-caret-down text-xs"></i>
                                    </button>
                                    <ul
                                        class="p-1.5 w-24 rounded-lg shadow-xl absolute top-8 -left-4 z-10 border border-gray-200 bg-white scale-y-0 origin-top dropdown-list !h-52 !overflow-x-hidden !overflow-y-auto thin-scrolling">
                                        <li v-for="countryCode in countryCodes" @click="change(countryCode)"
                                            class="flex items-center gap-2 p-1.5 rounded-md cursor-pointer hover:bg-gray-100">
                                            {{ countryCode.flag_emoji }}
                                            <span class="whitespace-nowrap text-xs">{{ countryCode.calling_code }}</span>
                                        </li>
                                    </ul>

                                </div>
                                <input v-model="form.phone" v-on:keypress="phoneNumber($event)" v-bind:class="errors.phone
                                    ? 'invalid' : ''" type="text" id="phone" class="pl-2 text-sm w-full h-full" />
                            </div>

                            <small class="db-field-alert" v-if="errors.phone">
                                {{ errors.phone[0] }}
                            </small>
                        </div>

                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3">
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
import BreadcrumbComponent from "../components/BreadcrumbComponent";
import LoadingComponent from "../components/LoadingComponent";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";

export default {
    name: "ProfileEditProfileComponent",
    components: {
        BreadcrumbComponent,
        LoadingComponent
    },
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
            calling_code: "",
            errors: {},
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            const profile = this.$store.getters.authInfo;
            this.$store.dispatch('countryCode/lists');
            this.form = {
                name: profile.name,
                email: profile.email,
                phone: profile.phone,
                country_code: profile.country_code,
            };

            if (profile.country_code !== null) {
                this.$store.dispatch('countryCode/callingCode', profile.country_code).then(res => {
                    this.flag = res.data.data.flag_emoji;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }

            this.$store.dispatch('company/lists').then(companyRes => {
                this.$store.dispatch('countryCode/show', companyRes.data.data.company_country_code).then(res => {

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
            return this.$store.getters['countryCode/lists'];
        },
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        change: function (e) {
            this.flag = e.flag_emoji;
            this.form.country_code = e.calling_code;

        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("frontendEditProfile/updateProfile", this.form).then((res) => {
                    this.$store.dispatch('updateAuthInfo', res.data.data).then(res => {
                        this.loading.isActive = false;
                        alertService.success(this.$t("message.profile_update"));
                        this.errors = {};
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
    }
}
</script>
