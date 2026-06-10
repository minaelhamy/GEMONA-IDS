<template>
    <h2 class="capitalize text-2xl font-bold mb-7 text-primary">{{ $t('menu.addresses') }}</h2>
    <div class="p-6 rounded-2xl shadow-card bg-white">
        <div class="row">

            <div class="col-12 sm:col-6" v-if="addresses.length > 0" v-for="address in addresses" :key="address">
                <div class="py-3 px-4 rounded-2xl bg-[#F7F7F7]">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-medium capitalize whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ address.full_name }}</h3>
                        <div class="group relative"><button type="button"
                                class="lab-line-circle-more text-primary"></button>
                            <nav
                                class="w-20 rounded-lg shadow-paper absolute top-5 right-0 z-10 origin-top scale-y-0 transition-all duration-300 group-hover:scale-y-100 bg-white">
                                <button data-modal="#address" type="button" @click="edit(address)"
                                    class="text-sm font-semibold capitalize py-2 px-3 text-left w-full block border-b last:border-b-0 border-gray-200">
                                    {{ $t("button.edit") }}</button>
                                <button type="button" @click="destroy(address.id)"
                                    class="text-sm font-semibold capitalize py-2 px-3 text-left w-full block border-b last:border-b-0 border-gray-200">
                                    {{ $t("button.delete") }}</button>
                            </nav>
                        </div>
                    </div>
                    <p class="text-sm leading-6">
                        <span v-if="address.email">{{ address.email }},</span>
                        <span v-if="address.phone">{{ address.phone }},</span>
                        <span v-if="address.state">{{ address.state }},</span>
                        <span v-if="address.city">{{ address.city }},</span>
                        <span v-if="address.country">{{ address.country }},</span>
                        <span v-if="address.address">{{ address.address }}<span v-if="address.address">,</span></span>
                        <span v-if="address.zip_code">{{ address.zip_code }}</span>
                    </p>
                </div>
            </div>
            <div class="col-12 sm:col-6">
                <AddressCreateComponent :props="address" />
            </div>
        </div>
    </div>
</template>

<script>
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import LoadingComponent from "../../components/LoadingComponent";
import AddressCreateComponent from "./AddressCreateComponent";
import targetService from "../../../../services/targetService";
export default {
    name: "AddressComponent",
    components: {
        LoadingComponent,
        AddressCreateComponent,
    },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            localAddress: {},
            targetID: "address",
            addClass: "modal-active",
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
                states: [],
                cities: []
            },
        }
    },
    mounted() {
        this.list();
    },
    computed: {
        addresses: function () {

            return this.$store.getters["frontendAddress/lists"];
        },
    },
    methods: {
        list: function () {
            this.loading.isActive = true;
            this.$store.dispatch("frontendAddress/lists", {
                search: this.address.search
            }).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        edit: function (address) {
            targetService.showTarget(this.targetID, this.addClass);
            this.loading.isActive = true;
            this.$store.dispatch("frontendAddress/edit", address.id).then(async (res) => {

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

                setTimeout(() => {
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

                }, 200);

                this.loading.isActive = false;

                this.$store.dispatch('frontendCountryCode/callingCode', address.country_code).then(res => {
                    this.address.flag = res.data.data.flag_emoji;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }).catch((err) => {
                alertService.error(err.response.data.message);
            });
        },
        destroy: function (addressId) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("frontendAddress/destroy", {
                        id: addressId,
                        search: this.address.search,
                    }).then((res) => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("label.address"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    }
}
</script>
