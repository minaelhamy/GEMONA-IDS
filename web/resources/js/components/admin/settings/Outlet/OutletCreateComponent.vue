<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.outlets") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="name" class="db-field-title required">{{
                                $t("label.name")
                            }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.name">{{
                                errors.name[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title" for="latitude">{{ $t("label.latitude") }}/{{
                                $t("label.longitude")
                            }}</label>
                            <div class="db-multiple-field">
                                <input v-model="props.form.latitude" v-bind:class="errors.latitude ? 'invalid' : ''
                                    " type="text" id="latitude" />
                                <input v-model="props.form.longitude" v-bind:class="errors.longitude ? 'invalid' : ''
                                    " type="text" id="longitude" />
                            </div>

                            <small class="db-field-alert" v-if="errors.latitude">{{ errors.latitude[0] }}</small>
                            <small class="db-field-alert" v-if="errors.longitude">{{ errors.longitude[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="email" class="db-field-title">{{
                                $t("label.email")
                            }}</label>
                            <input v-model="props.form.email" v-bind:class="errors.email ? 'invalid' : ''" type="email"
                                id="email" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.email">{{
                                errors.email[0]
                            }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label for="phone" class="db-field-title">{{
                                $t("label.phone")
                            }}</label>


                            <div :class="errors.phone ? 'invalid' : ''" class="db-field-control flex items-center">
                                <div class="w-fit flex-shrink-0 dropdown-group">
                                    <button type="button" class="flex items-center gap-1 dropdown-btn">
                                        {{ flag }}
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
                            <small class="db-field-alert" v-if="errors.phone">{{
                                errors.phone[0]
                            }}</small>

                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="city" class="db-field-title required">{{
                                $t("label.city")
                            }}</label>
                            <input v-model="props.form.city" v-bind:class="errors.city ? 'invalid' : ''" type="text"
                                id="city" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.city">{{
                                errors.city[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="state" class="db-field-title required">{{ $t("label.state") }}</label>
                            <input v-model="props.form.state" v-bind:class="errors.state ? 'invalid' : ''" type="text"
                                id="state" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.state">{{
                                errors.state[0]
                            }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="zip_code" class="db-field-title required">{{ $t("label.zip_code") }}</label>
                            <input v-model="props.form.zip_code" v-bind:class="errors.zip_code ? 'invalid' : ''" type="text"
                                id="zip_code" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.zip_code">{{ errors.zip_code[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status" type="radio"
                                            id="inactive" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <label for="address" class="db-field-title required">{{ $t("label.address") }}</label>
                            <textarea v-model="props.form.address" v-bind:class="errors.address ? 'invalid' : ''"
                                id="address" class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors.address">{{ errors.address[0] }}</small>
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
import statusEnum from "../../../../enums/modules/statusEnum";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";

export default {
    name: "OutletCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            flag: "",
            address: "",
            errors: {},
        };
    },
    mounted() {
        this.countryCodeList();
    },
    computed: {
        countryCodes: function () {
            return this.$store.getters['countryCode/lists'];
        },
        addButton: function () {
              return {title: this.$t("button.add_outlet")}
        }
    },
    methods: {
        phoneNumber(e) {
            return appService.phoneNumber(e);
        },
        change: function (e) {
            this.flag = e.flag_emoji;
            this.props.form.country_code = e.calling_code;
        },
        countryCodeList: function () {
            this.$store.dispatch('countryCode/lists').then(res => {
                if (this.props.form.country_code === "") {
                    this.flag = res.data.data[0].flag_emoji;
                    this.props.form.country_code = res.data.data[0].calling_code;
                }

            });
        },
        location: function (e) {
            this.address = e.address;
            this.props.form.latitude = e.location.lat;
            this.props.form.longitude = e.location.lng;
            this.props.form.city = e.other.city;
            this.props.form.state = e.other.state;
            this.props.form.zip_code = e.other.zipCode;
            this.props.form.address = e.address;
        },
        reset: function () {
            appService.modalHide();
            this.$store.dispatch("outlet/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                email: "",
                country_code: "",
                phone: "",
                latitude: "",
                longitude: "",
                city: "",
                state: "",
                zip_code: "",
                address: "",
                status: statusEnum.ACTIVE,
            };
        },
        save: function () {
            try {
                const tempId = this.$store.getters["outlet/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("outlet/save", this.props).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(
                        tempId === null ? 0 : 1,
                        this.$t("menu.outlets")
                    );
                    this.props.form = {
                        name: "",
                        email: "",
                        country_code: "",
                        phone: "",
                        latitude: "",
                        longitude: "",
                        city: "",
                        state: "",
                        zip_code: "",
                        address: "",
                        status: statusEnum.ACTIVE,
                    };
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
    },
};
</script>
