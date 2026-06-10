<template>
    <LoadingComponent :props="loading" />
    <button type="button" @click="add" data-modal="#variationModal" class="db-btn h-[37px] text-white bg-primary">
        <i class="lab lab-line-add-circle"></i>
        <span>{{ addButton.title }}</span>
    </button>

    <div id="variationModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.variations") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500" @click="reset">
                </button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div v-if="errors.global"
                            class="form-col-12 db-field-alert bg-red-100 border text-red-700 px-4 py-3 mb-5 rounded"
                            role="alert">
                            <span class="block sm:inline">{{ errors.global[0] }}</span>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <div class="form-col-12" v-if="attributes.length > 0" v-for="attribute in attributes">
                                <label :for="'label_variation' + attribute.id" class="db-field-title">
                                    {{ attribute.name }}
                                </label>
                                <select class="db-field-control" v-model="attributeProps.elements[attribute.id]"
                                    :id="'label_variation' + attribute.id">
                                    <option value="">{{ $t('label.please_select') }}</option>
                                    <option v-for="option in attribute.options" :value="option.id">
                                        {{ option.name }}
                                    </option>
                                </select>
                            </div>
                            <div v-else>
                                <div 
                                    class="mt-2 form-col-12 db-field-alert bg-red-100 border text-red-700 px-4 py-3 mb-5 rounded"
                                    role="alert">
                                    <span class="block sm:inline">{{ $t('message.empty_variation_message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <div class="form-col-12">
                                <label for="price" class="db-field-title required">{{ $t("label.price") }}</label>
                                <input v-on:keypress="numberOnly($event)" v-model="attributeProps.price"
                                    v-bind:class="errors.price ? 'invalid' : ''" type="text" id="price"
                                    class="db-field-control" />
                            </div>

                            <div class="form-col-12">
                                <label for="sku" class="db-field-title required">{{ $t("label.sku") }}</label>
                                <div class="db-group-field">
                                    <input v-model="attributeProps.sku" v-bind:class="errors.sku ? 'invalid' : ''"
                                        type="text" id="sku">
                                    <button type="button" @click="getSku" class="lab lab-fill-shuffle"></button>
                                </div>
                            </div>
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
import _ from "lodash";

export default {
    name: "ProductVariationCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: ["attributeProps"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            productId: null,
            variationProps: {
                form: {
                    attribute: null
                },
                productId: null,
            },
            errors: {},
        };
    },
    computed: {
        attributes: function () {
            return this.$store.getters['productAttribute/lists'];
        },
        addButton: function () {
              return {title: this.$t("button.add_variation")}
        }
    },
    mounted() {
        this.productId = this.$route.params.id;
        this.getSku();
        this.attributeList();
    },
    methods: {
        numberOnly: function (e) {
            return appService.floatNumber(e);
        },
        add: function () {
            appService.modalShow('#variationModal');
        },
        attributeList: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch('productAttribute/lists', { order_type: "asc" }).then(res => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        },
        reset: function () {
            appService.modalHide();
            this.$store.dispatch('productVariation/reset').then().catch();
            this.errors = {};
            this.variationProps.form.attribute = null;
            this.attributeProps.price = null;
            this.attributeProps.sku = null;
            this.attributeProps.elements = {};
            this.attributeProps.attribute = [];
            this.getSku();
        },
        getSku: function () {
            this.$store.dispatch("product/getSku").then((res) => {
                this.attributeProps.sku = res.data.data.product_sku;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        save: function () {
            try {
                const tempId = this.$store.getters["productVariation/temp"].temp_id;
                this.attributeProps.attribute = [];
                _.forEach(this.attributeProps.elements, (element, index) => {
                    if (element > 0) {
                        this.attributeProps.attribute.push({
                            "product_attribute_id": index,
                            "product_attribute_option_id": element,
                            "price": this.attributeProps.price,
                            "sku": this.attributeProps.sku
                        });
                    }
                });

                this.variationProps.productId = this.productId;
                this.variationProps.form.attribute = JSON.stringify(this.attributeProps.attribute);
                this.$store.dispatch('productVariation/save', this.variationProps).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(tempId === null ? 0 : 1, this.$t("label.variation"));
                    this.variationProps.form.attribute = null;
                    this.errors = {};

                    this.attributeProps.price = null;
                    this.attributeProps.elements = {};
                    this.attributeProps.attribute = [];
                    this.getSku();
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
};
</script>
