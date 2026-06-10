<template>
    <LoadingComponent :props="loading" />
    <h2 class="capitalize text-2xl font-bold mb-7 text-primary">{{ $t('label.change_password') }}</h2>
    <form @submit.prevent="changePassword">
        <div class="p-6 mb-6 rounded-2xl shadow-card bg-white">
            <h3 class="text-xl font-bold capitalize mb-5">{{ $t('label.change_password') }}</h3>
            <div class="row">
                <div class="col-12">
                    <label for="old_password" class="field-title required">{{ $t('label.old_password') }}</label>
                    <input type="password" id="old_password" v-model="form.old_password"
                        v-bind:class="errors.old_password ? 'invalid' : ''" class="field-control">
                    <small class="db-field-alert" v-if="errors.old_password">
                        {{ errors.old_password[0] }}
                    </small>
                </div>
                <div class="col-12 md:col-6">
                    <label for="new_password" class="field-title required">{{ $t('label.new_password') }}</label>
                    <input type="password" id="new_password" v-model="form.new_password"
                        v-bind:class="errors.new_password ? 'invalid' : ''" class="field-control">
                    <small class="db-field-alert" v-if="errors.new_password">
                        {{ errors.new_password[0] }}
                    </small>
                </div>
                <div class="col-12 md:col-6">
                    <label for="password_confirmation" class="field-title required">{{ $t('label.password_confirmation')
                    }}</label>
                    <input type="password" id="password_confirmation" v-model="form.confirm_password"
                        v-bind:class="errors.confirm_password ? 'invalid' : ''" class="field-control">
                    <small class="db-field-alert" v-if="errors.confirm_password">
                        {{ errors.confirm_password[0] }}
                    </small>
                </div>
            </div>
        </div>
        <button type="submit"
            class="px-6 py-3 capitalize rounded-full whitespace-nowrap text-center font-semibold text-white bg-primary">save
            changes</button>
    </form>
</template>

<script>
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import LoadingComponent from "../../components/LoadingComponent";
export default {
    name: "ChangePasswordComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                old_password: "",
                new_password: "",
                confirm_password: "",
            },
            errors: {},
        };
    },
    methods: {
        changePassword: function () {
            try {
                this.loading.isActive = true;
                this.$store
                    .dispatch("frontendEditProfile/changePassword", this.form).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(
                            res.config.method === "put" ?? 0,
                            this.$t("menu.password")
                        );
                        this.form = {
                            old_password: "",
                            password: "",
                            confirm_password: "",
                        };
                        this.errors = {};
                    })
                    .catch((err) => {
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
