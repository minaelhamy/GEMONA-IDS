<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <form @submit.prevent="save" class="block w-full">
            <div class="db-card mb-6">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ $t("menu.damages") }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="row">
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required">
                                {{ $t("label.date") }}
                            </label>
                            <Datepicker hideInputIcon autoApply v-model="props.form.date" :enableTimePicker="true" 
                                :is24="false" :monthChangeOnScroll="false" utc="false"
                                :input-class-name="errors.date ? 'invalid' : ''">
                                <template #am-pm-button="{ toggle, value }">
                                    <button @click="toggle">{{ value }}</button>
                                </template>
                            </Datepicker>
                            <small class="db-field-alert" v-if="errors.date">
                                {{ errors.date[0] }}
                            </small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title ">{{
                                $t("label.reference_no")
                            }}</label>
                            <input name="reference_no" v-model="props.form.reference_no" type="text"
                                class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.reference_no">{{
                                errors.reference_no[0]
                            }}</small>
                        </div>
                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title ">{{
                                $t("label.attachments")
                            }}</label>
                            <input @change="changeFile" v-bind:class="errors.file ? 'invalid' : ''" type="file"
                                ref="fileProperty" accept="image/png , image/jpeg, image/jpg , application/pdf "
                                class="db-field-control cursor-pointer" id="image" />
                            <small class="db-field-alert" v-if="errors.file">{{
                                errors.file[0]
                            }}</small>
                        </div>
                        <div class="form-col-12">
                            <div class="rounded-lg border border-amber-100">
                                <h4 class="w-full px-4 py-3 font-medium rounded-t-lg bg-amber-100 text-amber-600">
                                    {{ $t("message.selection_message") }}
                                </h4>
                                <div class="row p-5">
                                    <div class="form-col-12 ">
                                        <label class="db-field-title required">{{
                                            $t("label.add_products")
                                        }}</label>
                                        <div class="relative w-full h-12">
                                            <button type="button"
                                                class="lab-line-qrcode absolute top-1/2 -translate-y-1/2 left-4 z-10 cursor-pointer"></button>
                                            <vue-select class="h-full pr-4 pl-11" v-model="productId" :options="products"
                                                label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                                :clearOnClose="true" :placeholder="$t('label.select_one')
                                                    " search-placeholder="--" @update:modelValue="selectProduct($event)" />
                                        </div>
                                        <small class="db-field-alert" v-if="errors.products">{{
                                            errors.products[0]
                                        }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <label class="db-field-title">
                                {{ $t("label.products") }}
                            </label>
                            <div class="db-table-responsive border rounded-md">
                                <table class="db-table">
                                    <thead class="db-table-head border-t-0">
                                        <tr class="db-table-head-tr">
                                            <th class="db-table-head-th">
                                                {{ $t("label.product") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.unit_cost") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.quantity") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.discount") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.taxes") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.sub_total") }}
                                            </th>
                                            <th class="db-table-head-th">
                                                {{ $t("label.actions") }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="db-table-body">
                                        <tr v-for="(item, index) of datatable" :key="index" class="db-table-body-tr">
                                            <td class="db-table-body-td font-medium">
                                                {{ item.name }}
                                                <span v-if="item.variation_names">
                                                    ( {{ $t('label.variation') }} : {{ item.variation_names }})
                                                </span>
                                            </td>
                                            <td class="db-table-body-td">
                                                {{ floatFormat(item.price) }}
                                            </td>
                                            <td class="db-table-body-td">
                                                <input v-on:keypress="onlyNumber($event)" @keyup="updateQuantity(index)"
                                                    v-model="item.quantity" @click=" $event.target.select()" type="number"
                                                    min="1" class="db-field-control" />
                                            </td>
                                            <td class="db-table-body-td">
                                                {{ Number(item.total_discount) === 0 ? "" : "-" }}
                                                {{ floatFormat(item.total_discount) }}
                                            </td>
                                            <td class="db-table-body-td">
                                                {{ floatFormat(item.total_tax) }}
                                                ({{ floatFormat(item.total_tax_rate) }}%)
                                            </td>
                                            <td class="db-table-body-td">
                                                {{ floatFormat(item.total) }}
                                            </td>
                                            <td class="db-table-body-td">
                                                <SmIconSidebarModalEditComponent @click.prevent="editDatatable(index)" />
                                                <SmIconDeleteComponent @click.prevent="removeProduct(index)" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="db-table-body-td" colspan="2">
                                                {{ $t("label.total") }}
                                            </th>
                                            <th class="db-table-body-td">
                                                <span class="pl-3">
                                                    {{ Number.isInteger(totalQuantity) ? totalQuantity : 0 }}
                                                </span>
                                            </th>
                                            <th class="db-table-body-td">
                                                {{ floatFormat(totalDiscount) }}
                                            </th>
                                            <th class="db-table-body-td">
                                                {{ floatFormat(totalTax) }}
                                            </th>
                                            <th class="db-table-body-td">
                                                {{ floatFormat(totalPrice) }}
                                            </th>
                                            <th class="db-table-body-td"></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <ProductModalComponent :item="selectedProduct" :modal="modal" v-on:submitItem="modalSubmit"
                            ref="productModal" />
                    </div>
                    <div class="row pt-5">
                        <div class="form-col-12">
                            <div :class="errors.note ? 'invalid textarea-error-box-style' : ''">
                                <label class="db-field-title">
                                    {{ $t("label.note") }}
                                </label>
                                <quill-editor v-model:value="props.form.note" class="!h-40 textarea-border-radius" />
                                <small class="db-field-alert" v-if="errors.note">{{ errors.note[0] }}</small>
                            </div>
                        </div>
                        <div class="form-col-12">
                            <div class="flex flex-wrap gap-3">
                                <button v-if="permissionChecker('damage_create')" type="submit"
                                    class="db-btn text-white bg-primary">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span class="tracking-wide">
                                        {{ $t("button.save") }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script lang="js">
import LoadingComponent from "../components/LoadingComponent";
import Datepicker from "@vuepic/vue-datepicker";
import { quillEditor } from 'vue3-quill'
import alertService from "../../../services/alertService";
import ProductModalComponent from "../components/product/ProductModalComponent";
import appService from '../../../services/appService';
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";

export default {
    name: 'DamageCreateAndEditComponent',
    components: {
        quillEditor,
        LoadingComponent,
        Datepicker,
        ProductModalComponent,
        SmIconDeleteComponent,
        SmIconSidebarModalEditComponent
    },
    data() {
        return {
            file: "",
            productId: null,
            errors: {},
            datatable: [],
            loading: {
                isActive: false
            },
            modal: {
                isShowModal: false
            },
            props: {
                form: {
                    damage_id: 0,
                    date: "",
                    total: null,
                    reference_no: "",
                    note: "",
                    products: []
                }
            },
            selectedProduct: {
                name: "",
                quantity: 0,
                tax_id: [],
                price: 0,
                discount: 0,
                variation_id: 0,
                variation_names: "",
                product_id: 0,
                sku: "",
                is_variation: false,
                mode: 'add'
            },
            dataTableIndex: null
        }
    },
    mounted() {
        this.productList();
        this.damageInfo();
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists']
        },
        products: function () {
            return this.$store.getters['product/simpleList'];
        },
        subtotal: function () {
            return this.datatable.reduce((sum, item) => {
                return sum + +item.subtotal;
            }, 0);
        },
        totalPrice: function () {
            return this.datatable.reduce((sum, item) => {
                return sum + +item.total;
            }, 0);
        },
        totalQuantity: function () {
            return this.datatable.reduce((sum, item) => {
                return sum + +item.quantity;
            }, 0);
        },
        totalDiscount: function () {
            return this.datatable.reduce((sum, item) => {
                return sum + +item.total_discount;
            }, 0);
        },
        totalTax: function () {
            return this.datatable.reduce((sum, item) => {
                return sum + +item.total_tax;
            }, 0);
        },
    },
    methods: {
        permissionChecker: function (e) {
            return appService.permissionChecker(e);
        },
        changeFile: function (e) {
            this.file = e.target.files[0];
        },
        floatFormat: function (num) {
            return appService.floatFormat(num, this.setting.site_digit_after_decimal_point);
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        taxRateById: function (id) {
            return this.$refs.productModal.taxRateById(id);
        },
        selectProduct: function (id) {
            const product = this.products.find(product => product.id === id);
            if (product) {
                this.selectedProduct = {
                    name: product.name,
                    quantity: 1,
                    sku: product.sku,
                    tax_id: product.taxes,
                    price: product.buying_price,
                    discount: 0,
                    variation_id: 0,
                    variation_names: '',
                    product_id: product.id,
                    is_variation: product.is_variation,
                    mode: 'add'
                }

                if (product.is_variation) {
                    this.loadInitialVariations(product.id);
                    this.modal.isShowModal = true;
                } else {
                    this.productCheck();
                }

            }
        },
        modalSubmit: function () {
            this.productCheck();
            this.modal.isShowModal = false;
            this.productId = null;
        },
        productCheck: function () {
            let productExist = null;
            let oldQuantity = null;
            if (this.selectedProduct.mode === 'edit') {
                productExist = this.datatable[this.dataTableIndex];
            } else {
                if (this.datatable.length > 0) {
                    productExist = this.datatable.find(p =>
                        p.product_id === this.selectedProduct.product_id && p.variation_id === this.selectedProduct.variation_id
                    );
                }
            }

            if (productExist) {
                oldQuantity = this.selectedProduct.mode === 'edit' ? 0 : productExist.quantity;
            }

            let tax = 0;
            let total_tax = 0;
            let total_tax_rate = 0;
            let totalDiscount = this.selectedProduct.discount * this.selectedProduct.quantity;
            if (this.selectedProduct.tax_id.length > 0) {
                for (let i = 0; i < this.selectedProduct.tax_id.length; i++) {
                    const id = this.selectedProduct.tax_id[i];
                    const tax_rate = this.taxRateById(id);
                    tax += +((this.selectedProduct.price * tax_rate) / 100);
                    total_tax_rate += +tax_rate;
                }
                total_tax = tax * this.selectedProduct.quantity;
            }

            let finalItem = {
                mode: this.selectedProduct.mode,
                name: this.selectedProduct.name,
                quantity: this.selectedProduct.quantity + oldQuantity,
                tax_id: this.selectedProduct.tax_id,
                price: +this.selectedProduct.price,
                discount: this.selectedProduct.discount,
                variation_id: this.selectedProduct.variation_id,
                variation_names: this.selectedProduct.variation_names,
                product_id: this.selectedProduct.product_id,
                is_variation: this.selectedProduct.is_variation,
                tax: tax,
                total_tax: total_tax,
                total_tax_rate: total_tax_rate,
                total_discount: totalDiscount,
                subtotal: this.selectedProduct.quantity * this.selectedProduct.price,
                total: (+(this.selectedProduct.quantity * this.selectedProduct.price) + (+total_tax) - (+totalDiscount)).toFixed(2),
                sku: this.selectedProduct.sku,
                item_id: this.selectedProduct.is_variation ? this.selectedProduct.variation_id : this.selectedProduct.product_id
            }

            if (!productExist) {
                this.datatable.push(finalItem);
            } else {
                productExist.quantity = finalItem.quantity;
                productExist.tax_id = finalItem.tax_id;
                productExist.price = finalItem.price;
                productExist.discount = finalItem.discount;
                productExist.tax = finalItem.tax;
                productExist.total_tax = finalItem.total_tax;
                productExist.total_tax_rate = finalItem.total_tax_rate;
                productExist.total_discount = finalItem.total_discount;
                productExist.subtotal = finalItem.subtotal;
                productExist.total = finalItem.total;
                productExist.sku = finalItem.sku;
                productExist.item_id = finalItem.is_variation ? finalItem.variation_id : finalItem.product_id;
                productExist.variation_names = finalItem.variation_names;
                productExist.variation_id = finalItem.variation_id;
            }
        },
        editDatatable: function (index) {
            const product = this.datatable[index];
            this.selectedProduct = {
                name: product.name,
                quantity: product.quantity,
                tax_id: product.tax_id.length === 0 ? [] : product.tax_id,
                price: product.price,
                discount: product.discount,
                variation_id: product.variation_id,
                variation_names: product.variation_names,
                product_id: product.product_id,
                is_variation: product.is_variation,
                sku: product.sku,
                mode: 'edit'
            }
            this.dataTableIndex = index;
            this.loadInitialVariations(product.product_id);
            this.modal.isShowModal = true;
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append('date', this.props.form.date ? this.props.form.date : '');
                fd.append('reference_no', this.props.form.reference_no);
                fd.append('subtotal', this.subtotal);
                fd.append('tax', this.totalTax);
                fd.append('discount', this.totalDiscount);
                fd.append('total', this.totalPrice);
                fd.append('note', this.props.form.note);
                fd.append('products', JSON.stringify(this.datatable));
                if (this.file) {
                    fd.append('file', this.file);
                }
                this.loading.isActive = true;
                const tempId = this.$store.getters['damage/temp'].temp_id;
                this.$store.dispatch('damage/save', { form: fd }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip((tempId === null ? 0 : 1), this.$t('menu.damages'));
                    this.reset();
                    this.$router.push({ name: 'admin.damage.list' });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                    if (this.errors.global) {
                        alertService.error(this.errors.global[0]);
                    }
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        removeProduct: function (productIndex) {
            this.datatable.splice(productIndex, 1);
        },
        updateQuantity: function (i) {
            const tax = this.datatable[i].tax > 0 ? this.datatable[i].tax : 0;
            this.datatable[i].total_tax = tax * this.datatable[i].quantity;
            this.datatable[i].total_discount = this.datatable[i].discount * this.datatable[i].quantity;
            this.datatable[i].subtotal = (Number(this.datatable[i].quantity) * Number(this.datatable[i].price)).toFixed(2);
            this.datatable[i].total = (+(this.datatable[i].quantity * this.datatable[i].price) + (+this.datatable[i].total_tax) - (+this.datatable[i].total_discount)).toFixed(2);
        },
        productList: function () {
            this.loading.isActive = true;
            this.$store.dispatch('product/getSimpleProduct').then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        loadInitialVariations: function (product_id) {
            this.loading.isActive = true;
            this.$store.commit('productVariation/initialVariation', []);
            this.$store.dispatch("productVariation/initialVariation", product_id).then(() => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        damageInfo: function () {
            if (!isNaN(this.$route.params.id)) {
                this.$store.dispatch('damage/edit', this.$route.params.id).then((res) => {
                    this.getDamage(res.data.data);
                }).catch((err) => {
                })
            }
        },
        getDamage: function (order) {
            this.props.form.damage_id = order.id;
            this.props.form.date = order.date;
            this.props.form.user_id = order.user_id;
            this.props.form.reference_no = order.reference_no ? order.reference_no : '';
            this.props.form.total = order.total;
            this.props.form.note = order.note;
            const products = [];
            for (let i = 0; i < order.products.length; i++) {
                const product = order.products[i];
                const tax_id = [];
                let total_tax_rate = 0;
                if (product.taxes) {
                    for (let j = 0; j < product.taxes.length; j++) {
                        const tax = product.taxes[j];
                        tax_id.push(tax.tax_id);
                        total_tax_rate = total_tax_rate + +tax.tax_rate;
                    }
                }
                products.push({
                    product_id: product.product_id,
                    item_id: product.item_id,
                    variation_names: product.variation_names,
                    sku: product.sku,
                    name: product.product_name,
                    price: this.floatFormat(product.price),
                    quantity: product.quantity,
                    discount: product.discount > 0 ? product.discount / product.quantity : 0,
                    total_discount: +product.discount,
                    tax: product.tax > 0 ? product.tax / product.quantity : 0,
                    total_tax: product.tax,
                    total_tax_rate: total_tax_rate,
                    tax_id: tax_id,
                    subtotal: product.subtotal,
                    total: product.total,
                    is_variation: !!product.product_variation,
                    variation_id: product.product_variation ? product.item_id : 0
                });
            }
            this.datatable = products;
        },
        reset: function () {
            this.props.form.damage_id = 0;
            this.props.form.date = "";
            this.props.form.reference_no = "";
            this.props.form.total = null;
            this.props.form.status = null;
            this.props.form.note = "";
            this.props.form.products = [];
            this.datatable = [];
            this.file = "";
            this.$refs["fileProperty"].value = "";
            this.errors = {}
        }
    }
}
</script>
