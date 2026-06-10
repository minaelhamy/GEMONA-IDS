<template>
    <LoadingComponent :props="loading" />
    <form @submit.prevent="save" class="w-full d-block">
        <div id="order_setup" class="db-card db-tab-div active">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ $t('menu.shipping_setup') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="auto_update">{{ $t("label.select_shipping_method")
                        }}</label>
                        <div class="pt-2">
                            <div class="db-field-radio pb-2">
                                <div class="custom-radio">
                                    <input @click="multiTargets($event, 'tab-action', 'tab-content', 'productWise')"
                                        :value="enums.shippingMethodEnum.PRODUCT_WISE" v-model="form.shipping_setup_method"
                                        id="product_wise" type="radio" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="product_wise" class="db-field-label">{{ $t("label.product_wise")
                                }}</label>
                            </div>
                            <div class="db-field-radio pb-2">
                                <div class="custom-radio">
                                    <input @click="multiTargets($event, 'tab-action', 'tab-content', 'flatRate')"
                                        :value="enums.shippingMethodEnum.FLAT_RATE_WISE" v-model="form.shipping_setup_method"
                                        type="radio" id="flat_rate_wise" class="tab-action custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="flat_rate_wise" class="db-field-label">
                                    {{ $t("label.flat_rate_wise") }}
                                </label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input @click="multiTargets($event, 'tab-action', 'tab-content', 'areaWise')"
                                        :value="enums.shippingMethodEnum.AREA_WISE" v-model="form.shipping_setup_method"
                                        type="radio" id="area_wise" class="custom-radio-field" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="area_wise" class="db-field-label">
                                    {{ $t("label.area_wise") }}
                                </label>
                            </div>
                        </div>
                        <small class="db-field-alert" v-if="errors.shipping_setup_method">
                            {{ errors.shipping_setup_method[0] }}
                        </small>
                    </div>

                    <div class="form-col-12"
                        v-if="form.shipping_setup_method === enums.shippingMethodEnum.PRODUCT_WISE">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="productWise" :class="form.shipping_setup_method === enums.shippingMethodEnum.PRODUCT_WISE ? 'active' : ''">
        </div>

        <div class="form-col-12 db-card tab-content my-4" id="flatRate"
            :class="form.shipping_setup_method === enums.shippingMethodEnum.FLAT_RATE_WISE ? 'active' : ''">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('label.flat_rate_wise') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row py-2">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">
                            {{ $t("label.shipping_cost") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.shipping_setup_flat_rate_wise_cost"
                            v-bind:class="errors.shipping_setup_flat_rate_wise_cost ? 'invalid' : ''" type="text" id="name"
                            class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.shipping_setup_flat_rate_wise_cost">{{
                            errors.shipping_setup_flat_rate_wise_cost[0]
                        }}</small>
                    </div>
                    <div class="form-col-12">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-col-12 db-card tab-content my-4" id="areaWise"
            :class="form.shipping_setup_method === enums.shippingMethodEnum.AREA_WISE ? 'active' : ''">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('label.area_wise') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="row py-2">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">
                            {{ $t("label.default_shipping_cost") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="form.shipping_setup_area_wise_default_cost"
                            v-bind:class="errors.shipping_setup_area_wise_default_cost ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.shipping_setup_area_wise_default_cost">{{
                            errors.shipping_setup_area_wise_default_cost[0]
                        }}</small>
                    </div>
                    <div class="form-col-12">
                        <button type="submit" class="db-btn text-white bg-primary">
                            <i class="lab lab-fill-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="form-col-12 db-card tab-content my-4" id="areaWise"
        :class="form.shipping_setup_method === enums.shippingMethodEnum.AREA_WISE ? 'active' : ''">
        <OrderAreaListComponent />
    </div>
</template>

<script>

import shippingMethodEnum from "../../../../enums/modules/shippingMethodEnum";
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import targetService from "../../../../services/targetService";
import appService from "../../../../services/appService";
import OrderAreaListComponent from "./OrderArea/OrderAreaListComponent";

export default {
    name: "ShippingSetupComponent",
    components: { LoadingComponent, OrderAreaListComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            form: {
                shipping_setup_method: null,
                shipping_setup_flat_rate_wise_cost: "",
                shipping_setup_area_wise_default_cost: "",
            },
            enums: {
                shippingMethodEnum: shippingMethodEnum
            },
            errors: {}
        }
    },
    mounted() {
        try {
            this.loading.isActive = true;
            this.$store.dispatch('shippingSetup/lists').then(res => {
                this.form = {
                    shipping_setup_method: res.data.data.shipping_setup_method,
                    shipping_setup_flat_rate_wise_cost: res.data.data.shipping_setup_flat_rate_wise_cost,
                    shipping_setup_area_wise_default_cost: res.data.data.shipping_setup_area_wise_default_cost,
                }
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        } catch (err) {
            this.loading.isActive = false;
        }
    },
    methods: {
        multiTargets: function (event, commonBtnClass, commonDivClass, targetID) {
            targetService.multiTargets(event, commonBtnClass, commonDivClass, targetID);
        },
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch("shippingSetup/save", this.form).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.shipping_setup"));
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
    }
}
</script>
