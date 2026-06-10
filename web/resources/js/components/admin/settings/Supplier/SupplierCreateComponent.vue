<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.suppliers") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="company" class="db-field-title required">
                                {{ $t("label.company") }}
                            </label>
                            <input v-model="props.form.company" type="text" id="company" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.company">{{
                                errors.company[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">
                                {{ $t("label.name") }}
                            </label>
                            <input v-model="props.form.name" type="text" id="name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.name">{{
                                errors.name[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title">
                                {{ $t("label.email") }}
                            </label>
                            <input v-model="props.form.email" type="text" id="email" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.email">{{
                                errors.email[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title">{{ $t("label.phone") }}</label>
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
                                        <li v-for="countryCode in countryCodes" @click="change(countryCode)"
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

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" for="country">{{
                                $t('label.country') }}</label>
                            <vue-select class="db-field-control" id="country"
                                v-bind:class="errors.country ? 'is-invalid' : ''" v-model="props.form.country"
                                @update:modelValue="callStates($event)" :options="countries" label-by="name" value-by="name"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.country">
                                {{ errors.country[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" for="state">{{
                                $t('label.state') }}</label>
                            <vue-select class="db-field-control" id="state" v-bind:class="errors.state ? 'is-invalid' : ''"
                                v-model="props.form.state" @update:modelValue="callCities($event)" :options="props.states"
                                label-by="name" value-by="name" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.state">
                                {{ errors.state[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title">{{
                                $t('label.city') }}</label>
                            <vue-select class="db-field-control" id="city" v-bind:class="errors.city ? 'is-invalid' : ''"
                                v-model="props.form.city" :options="props.cities" label-by="name" value-by="name"
                                :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                                search-placeholder="--" />
                            <small class="db-field-alert" v-if="errors.city">
                                {{ errors.city[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="zip_code" class="db-field-title">
                                {{ $t("label.zip_code") }}
                            </label>
                            <input v-model="props.form.zip_code" type="text" id="zip_code" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.zip_code">{{
                                errors.zip_code[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-12">
                            <label for="image" class="db-field-title">{{ $t('label.image') }}</label>
                            <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                                class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg">
                            <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <label for="address" class="db-field-title">{{ $t("label.address") }}</label>
                            <textarea v-model="props.form.address" v-bind:class="errors.address ? 'invalid' : ''"
                                id="address" class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors.address">{{
                                errors.address[0]
                            }}</small>
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
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";

export default {
    name: "SupplierCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            image: "",
            errors: {},
            flag: "",
            calling_code: "",
        };
    },
    mounted() {
        try {
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
        addButton: function () {
            return { title: this.$t("button.add_supplier") }
        },
        countries: function () {
            return this.$store.getters['country/lists'];
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        callCountry: function () {
            this.$store.dispatch('country/lists',{status:statusEnum.ACTIVE});
            this.loading.isActive = false;
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
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            appService.modalHide();
            this.$store.dispatch("supplier/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                company: "",
                name: "",
                email: "",
                phone: "",
                country_code: this.calling_code,
                country: null,
                state: null,
                city: null,
                zip_code: "",
                address: "",
            };
            this.$props.props.states = [];
            this.$props.props.cities = [];

            this.$props.props.form.country_code = this.calling_code;
            this.$props.props.flag = this.flag;
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },

        change: function (e) {
            this.props.flag = e.flag_emoji;
            this.$props.props.form.country_code = e.calling_code;

        },

        save: function () {
            try {
                const fd = new FormData();
                fd.append('company', this.props.form.company);
                fd.append('name', this.props.form.name);
                fd.append('email', this.props.form.email);
                fd.append('country_code', this.props.form.country_code);
                fd.append('phone', this.props.form.phone);
                fd.append('country', this.props.form.country === null ? '' : this.props.form.country);
                fd.append('state', this.props.form.state === null ? '' : this.props.form.state);
                fd.append('city', this.props.form.city === null ? '' : this.props.form.city);
                fd.append('zip_code', this.props.form.zip_code);
                fd.append('address', this.props.form.address);
                if (this.image) {
                    fd.append('image', this.image);
                }

                const tempId = this.$store.getters["supplier/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch('supplier/save', {
                    form: fd,
                    search: this.props.search
                }).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(
                        tempId === null ? 0 : 1,
                        this.$t("menu.suppliers")
                    );
                    this.props.form = {
                        company: "",
                        name: "",
                        email: "",
                        phone: "",
                        country_code: this.calling_code,
                        country: null,
                        state: null,
                        city: null,
                        zip_code: "",
                        address: "",
                    };
                    this.$props.props.flag = this.flag;
                    this.image = "";
                    this.errors = {};
                    this.$refs.imageProperty.value = null;
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
};
</script>
