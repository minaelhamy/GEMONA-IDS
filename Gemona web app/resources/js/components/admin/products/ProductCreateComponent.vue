<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" @click.self="reset"
        class="fixed inset-0 z-50 invisible transition-all duration-500 opacity-0 bg-black/50">
        <div class="w-full h-screen max-w-xl overflow-x-hidden bg-white thin-scrolling ms-auto ltr:translate-x-full rtl:-translate-x-full">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t("menu.products") }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <div class="flex items-center justify-between">
                            <label for="name" class="db-field-title">{{ $t("label.name") }}</label>
                            <button v-if="aiStatus" type="button" @click="generateAiName" class="flex items-center text-xs cursor-pointer text-primary">
                                <i class="lab-fill-ai text-[#8B5CF6]"></i>
                                <span :class="aiNameLoading ? '' : 'hidden'" class="ai-text-animation" role="status">
                                    {{ $t("label.just_a_second") }}
                                </span>
                                <span :class="!aiNameLoading && !props.form.name ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.generate") }}
                                </span>
                                <span :class="!aiNameLoading && props.form.name ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.regenerate") }}
                                </span>
                            </button>
                        </div>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="sku" class="db-field-title required">{{ $t("label.sku") }}</label>
                        <div class="db-group-field">
                            <input v-on:keypress="onlyNumber($event)" v-model="props.form.sku"
                                v-bind:class="errors.sku ? 'invalid' : ''" type="text" id="sku">
                            <button type="button" @click="getSku" class="lab lab-fill-shuffle"></button>
                        </div>
                        <small class="db-field-alert" v-if="errors.sku">{{ errors.sku[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="product_category_id" class="db-field-title required">
                            {{ $t("label.category") }}
                        </label>
                        <vue-select ref="product_category_id" class="db-field-control f-b-custom-select"
                            id="product_category_id" v-bind:class="errors.product_category_id ? 'invalid' : ''"
                            v-model="props.form.product_category_id" :options="productCategories" label-by="option"
                            value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.product_category_id">
                            {{ errors.product_category_id[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="barcode_id" class="db-field-title required">{{ $t("label.barcode") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="barcode_id"
                            v-bind:class="errors.barcode_id ? 'invalid' : ''" v-model="props.form.barcode_id"
                            :options="barcodes" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                            :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.barcode_id">
                            {{ errors.barcode_id[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="buying_price" class="db-field-title required">{{ $t("label.buying_price") }}</label>
                        <input v-on:keypress="floatNumber($event)" v-model="props.form.buying_price"
                            v-bind:class="errors.buying_price ? 'invalid' : ''" type="text" id="buying_price"
                            class="db-field-control">
                        <small class="db-field-alert" v-if="errors.buying_price">{{ errors.buying_price[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="selling_price" class="db-field-title required">
                            {{ $t("label.selling_price") }}
                        </label>
                        <input v-on:keypress="floatNumber($event)" v-model="props.form.selling_price"
                            v-bind:class="errors.selling_price ? 'invalid' : ''" type="text" id="selling_price"
                            class="db-field-control">
                        <small class="db-field-alert" v-if="errors.selling_price">{{ errors.selling_price[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="tax_id" class="db-field-title">{{ $t("label.tax") }}</label>
                        <vue-select ref="tax_id" class="db-field-control f-b-custom-select" id="tax_id"
                            v-bind:class="errors.tax_id ? 'invalid' : ''" v-model="props.form.tax_id" :options="taxes"
                            label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                            placeholder="--" search-placeholder="--" :multiple="true" />
                        <small class="db-field-alert" v-if="errors.tax_id">{{ errors.tax_id[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="product_brand_id" class="db-field-title">{{ $t("label.brand") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="product_brand_id"
                            v-bind:class="errors.product_brand_id ? 'invalid' : ''"
                            v-model="props.form.product_brand_id" :options="productBrands" label-by="name" value-by="id"
                            :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.product_brand_id">
                            {{ errors.product_brand_id[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.status" id="active"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active" class="db-field-label">{{ $t('label.active') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                        id="inactive" :value="enums.statusEnum.INACTIVE">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="yes">{{ $t("label.can_purchasable") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.can_purchasable" id="yes"
                                        :value="enums.askEnum.YES" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="yes" class="db-field-label">{{ $t('label.yes') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.can_purchasable"
                                        id="no" :value="enums.askEnum.NO">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="no" class="db-field-label">{{ $t('label.no') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="enable">{{ $t("label.show_stock_out") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.show_stock_out" id="enable"
                                        :value="enums.activityEnum.ENABLE" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="enable" class="db-field-label">{{ $t('label.enable') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.show_stock_out"
                                        id="disable" :value="enums.activityEnum.DISABLE">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="disable" class="db-field-label">{{ $t('label.disable') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required" for="refundableYes">{{ $t("label.refundable") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.refundable" id="refundableYes"
                                        :value="enums.askEnum.YES" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="refundableYes" class="db-field-label">{{ $t('label.yes') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.refundable"
                                        id="refundableNo" :value="enums.askEnum.NO">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="refundableNo" class="db-field-label">{{ $t('label.no') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="maximum_purchase_quantity" class="db-field-title required">
                            {{ $t("label.maximum_purchase_quantity") }}
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="props.form.maximum_purchase_quantity"
                            v-bind:class="errors.maximum_purchase_quantity ? 'invalid' : ''" type="text"
                            id="maximum_purchase_quantity" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.maximum_purchase_quantity">
                            {{ errors.maximum_purchase_quantity[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="low_stock_quantity_warning" class="db-field-title required">
                            {{ $t("label.low_stock_quantity_warning") }}
                        </label>
                        <input v-on:keypress="onlyNumber($event)" v-model="props.form.low_stock_quantity_warning"
                            v-bind:class="errors.low_stock_quantity_warning ? 'invalid' : ''" type="text"
                            id="low_stock_quantity_warning" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.low_stock_quantity_warning">
                            {{ errors.low_stock_quantity_warning[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label for="unit" class="db-field-title required">{{ $t("label.unit") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="unit_id"
                            v-bind:class="errors.unit_id ? 'invalid' : ''" v-model="props.form.unit_id" :options="units"
                            label-by="name_code" value-by="id" :closeOnSelect="true" :searchable="true"
                            :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.unit_id">
                            {{ errors.unit_id[0] }}
                        </small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="weight" class="db-field-title">
                            {{ $t("label.weight") }}
                        </label>
                        <input v-model="props.form.weight" v-bind:class="errors.weight ? 'invalid' : ''" type="text"
                            id="weight" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.weight">
                            {{ errors.weight[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label for="warranty" class="db-field-title">
                            {{ $t("label.warranty") }}
                        </label>
                        <input v-model="props.form.warranty" v-bind:class="errors.warranty ? 'invalid' : ''" type="text"
                            id="warranty" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.warranty">
                            {{ errors.warranty[0] }}
                        </small>
                    </div>
                    <div class="form-col-12 sm:form-col-12">
                        <div class="relative flex items-center justify-between inline-block w-full group">
                            <label for="tags" class="db-field-title">{{ $t("label.tags") }} <i class="lab lab-fill-info lab-font-size-14"></i></label>

                            <span class="min-w-[200px] absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-sm rounded px-2 py-1">
                                {{ $t('message.tags_hint') }}
                            </span>
                            <button v-if="aiStatus" type="button" @click="generateAiTags" class="flex items-center text-xs cursor-pointer text-primary">
                                <i class="lab-fill-ai text-[#8B5CF6]"></i>
                                <span :class="aiTagsLoading ? '' : 'hidden'" class="ai-text-animation" role="status">
                                    {{ $t("label.just_a_second") }}
                                </span>
                                <span :class="!aiTagsLoading && props.form.convertTags.length === 0 ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.generate") }}
                                </span>
                                <span :class="!aiTagsLoading && props.form.convertTags.length > 0 ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.regenerate") }}
                                </span>
                            </button>
                        </div>

                        <div>
                            <vue-tags-input id="tags" v-bind:class="errors.tags ? 'invalid-tag' : ''" placeholder=""
                                v-model="tag" :tags="props.form.convertTags"
                                @tags-changed="newTags => props.form.convertTags = newTags" />
                        </div>
                        <small class="db-field-alert" v-if="errors.tags">{{ errors.tags[0] }}</small>
                    </div>

                    <div class="form-col-12">
                         <div class="flex items-center justify-between">
                            <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                            <button v-if="aiStatus" type="button" @click="generateAiDescription" class="flex items-center text-xs cursor-pointer text-primary">
                                <i class="lab-fill-ai text-[#8B5CF6]"></i>
                                <span :class="aiDescriptionLoading ? '' : 'hidden'" class="ai-text-animation" role="status">
                                    {{ $t("label.just_a_second") }}
                                </span>
                                <span :class="!aiDescriptionLoading && !props.form.description ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.generate") }}
                                </span>
                                <span :class="!aiDescriptionLoading && props.form.description ? '' : 'hidden'" class="btn-text">
                                    {{ $t("label.regenerate") }}
                                </span>
                            </button>
                        </div>
                        <div :class="errors.description ? 'invalid textarea-error-box-style' : ''">
                            <quill-editor id="description" v-model:value="props.form.description"
                                class="!h-40 textarea-border-radius" />
                        </div>
                        <small class="db-field-alert" v-if="errors.description">
                            {{ errors.description[0] }}
                        </small>
                    </div>

                    <div class="col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="submit" class="py-2 text-white db-btn bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>{{ $t("label.save") }}</span>
                            </button>
                            <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                <i class="lab lab-fill-close-circle"></i>
                                <span>{{ $t("button.close") }}</span>
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
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import LoadingComponent from "../components/LoadingComponent";
import askEnum from "../../../enums/modules/askEnum";
import statusEnum from "../../../enums/modules/statusEnum";
import activityEnum from "../../../enums/modules/activityEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import VueTagsInput from "@sipec/vue3-tags-input";
import { quillEditor } from 'vue3-quill';
import { useCanvas } from "../../../composables/canvas";


export default {
    name: "ProductCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, quillEditor, VueTagsInput },
    props: ['props'],
    data() {
        return {
            tag: "",
            loading: {
                isActive: false
            },
            aiNameLoading: false,
            aiDescriptionLoading: false,
            aiTagsLoading: false,
            enums: {
                statusEnum: statusEnum,
                askEnum: askEnum,
                activityEnum: activityEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                },
                askEnumArray: {
                    [askEnum.YES]: this.$t("label.yes"),
                    [askEnum.NO]: this.$t("label.no")
                },
                activityEnumArray: {
                    [activityEnum.ENABLE]: this.$t("label.enable"),
                    [activityEnum.DISABLE]: this.$t("label.disable")
                }
            },
            errors: {},
            productCategories: [],
            units: [],
            productBrands: [],
            taxes: [],
            barcodes: [],
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.getSku();

        this.$store.dispatch('productCategory/depthTrees', {
        }).then((res) => {
            this.productCategories = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch('productBrand/lists', {
            order_column: 'id',
            order_type: 'asc'
        }).then((res) => {
            this.productBrands = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch('unit/lists', {
            order_column: 'id',
            order_type: 'asc'
        }).then((res) => {
            this.units = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch('tax/lists', {
            order_column: 'id',
            order_type: 'asc'
        }).then((res) => {
            this.taxes = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });

        this.loading.isActive = true;
        this.$store.dispatch('barcode/lists', {
            order_column: 'id',
            order_type: 'asc'
        }).then((res) => {
            this.barcodes = res.data.data;
            this.loading.isActive = false;
        }).catch((error) => {
            this.loading.isActive = false;
        });
        this.$store.dispatch('ai/fetchStatus').then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    computed: {
        addButton: function () {
            return { title: this.$t("button.add_product") }
        },
        aiStatus: function () {
            return this.$store.getters['ai/status'];
        },
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.$store.dispatch('product/reset').then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                sku: "",
                product_category_id: null,
                barcode_id: null,
                buying_price: "",
                selling_price: "",
                tax_id: [],
                product_brand_id: null,
                status: statusEnum.ACTIVE,
                can_purchasable: askEnum.NO,
                show_stock_out: activityEnum.DISABLE,
                refundable: askEnum.NO,
                maximum_purchase_quantity: "",
                low_stock_quantity_warning: "",
                unit_id: null,
                weight: "",
                warranty: "",
                tags: "",
                convertTags: [],
                description: "",
            };
            this.getSku();
        },
        getSku: function () {
            this.$store.dispatch("product/getSku").then((res) => {
                this.$props.props.form.sku = res.data.data.product_sku;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        save: function () {
            try {
                this.props.form.tags = JSON.stringify(this.props.form.convertTags);
                const tempId = this.$store.getters['product/temp'].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch('product/save', this.props).then((res) => {
                    useCanvas().closeCanvas('sidebar');
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('menu.products'));
                    this.props.form = {
                        name: "",
                        sku: "",
                        product_category_id: null,
                        barcode_id: null,
                        buying_price: "",
                        selling_price: "",
                        tax_id: [],
                        product_brand_id: null,
                        status: statusEnum.ACTIVE,
                        can_purchasable: askEnum.NO,
                        show_stock_out: activityEnum.DISABLE,
                        refundable: askEnum.NO,
                        maximum_purchase_quantity: "",
                        low_stock_quantity_warning: "",
                        unit_id: null,
                        weight: "",
                        warranty: "",
                        tags: "",
                        convertTags: [],
                        description: "",
                    }
                    this.getSku();
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = {};
                    if (err.response && err.response.data && err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    } else {
                        alertService.error(err.response.data.message);
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        },
        generateAiName: function () {
            if (!this.props.form.name) {
                alertService.warning(this.$t('message.product_name_is_required_to_generate_name'));
                return;
            }
            this.aiNameLoading = true;
            this.$store.dispatch("ai/fetchName", { name: this.props.form.name }).then((res) => {
                this.aiNameLoading = false;
                if (res.data.data) {
                    this.props.form.name = res.data.data;
                }
            }).catch((err) => {
                this.aiNameLoading = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        generateAiDescription: function () {
            if (!this.props.form.name) {
                alertService.warning(this.$t('message.product_name_is_required_to_generate_description'));
                return;
            }
            this.aiDescriptionLoading = true;
            this.$store.dispatch("ai/fetchDescription", { name: this.props.form.name }).then((res) => {
                this.aiDescriptionLoading = false;
                if (res.data.data) {
                    this.props.form.description = res.data.data;
                }
            }).catch((err) => {
                this.aiDescriptionLoading = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        },
        generateAiTags: function () {
            if (!this.props.form.name) {
                alertService.warning(this.$t('message.product_name_is_required_to_generate_tags'));
                return;
            }
            this.aiTagsLoading = true;
            this.$store.dispatch("ai/fetchTags", { name: this.props.form.name }).then((res) => {
                this.aiTagsLoading = false;
                if (res.data.data && Array.isArray(res.data.data)) {
                    this.props.form.convertTags = res.data.data.map(tag => ({ text: tag, tiClasses: ['ti-valid'] }));
                }
            }).catch((err) => {
                this.aiTagsLoading = false;
                alertService.error(err.response?.data?.message || err.message);
            });
        }
    }
}
</script>
