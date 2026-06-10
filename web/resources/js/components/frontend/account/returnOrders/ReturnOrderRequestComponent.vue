<template>
    <LoadingComponent :props="loading" />
    <div class="flex items-center gap-4 mb-7">
        <button @click.prevent="$router.back()" class="lab-line-undo text-xl font-bold text-primary"></button>
        <h2 class="capitalize text-xl font-bold text-primary">{{ $t('label.return_request') }}</h2>
    </div>
    <form class="w-full d-block min-h-dvh" @submit.prevent="save">
        <div class="rounded-2xl shadow-card mb-6 bg-white">
            <h3 class="font-semibold p-4">{{ $t('label.order_id') }}: #{{ order.order_serial_no }}</h3>
            <div v-if="errors.products"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-5 rounded relative" role="alert">
                <span class="block sm:inline">{{ errors.products[0] }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="close">
                    <i class="lab lab-fill-close-circle margin-top-5-px"></i>
                </span>
            </div>
            <div class="mobile:overflow-x-auto">
                <table class="w-full text-left text-sm capitalize">
                    <thead class="font-semibold border-b border-t border-gray-200">
                        <tr>
                            <th class="p-4">
                                <input type="checkbox" v-model="isAllReturn" @change="selectAll($event, orderProducts)"
                                    class="cs-custom-checkbox" />
                            </th>
                            <th class="p-4">{{ $t('label.products') }}</th>
                            <th class="p-4">{{ $t('label.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium" v-if="orderProducts.length > 0">
                        <tr v-for="(product, index) in orderProducts" :key="index" class="group">
                            <td class="p-4 border-b group-last:border-0 border-gray-100">
                                <input v-if="product.is_refundable" type="checkbox" v-model="product.isReturn"
                                    @change="selectProduct($event, index, product)" class="cs-custom-checkbox" />
                                <span v-else class="text-danger font-semibold">{{ $t('label.not_refundable')
                                    }}</span>
                            </td>
                            <td class="p-4 border-b group-last:border-0 border-gray-100">
                                <div class="inline-flex items-center gap-3">
                                    <img :src="product.product_image" alt="product"
                                        class="w-12 h-12 object-cover rounded-md flex-shrink-0" />
                                    <div class="overflow-hidden">
                                        <h4
                                            class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-0.5">
                                            {{ product.product_name }}</h4>
                                        <p class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis"
                                            v-if="product.has_variation">
                                            {{ product.variation_names }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 border-b group-last:border-0 border-gray-100">

                                <div class="flex items-center gap-1 w-20 p-1 rounded-full bg-[#F7F7FC]">
                                    <button @click.prevent="quantityDecrement(index, product)" type="button"
                                        :class="product.quantity === 1 ? 'cursor-not-allowed text-[#A0A3BD]' : 'text-primary'"
                                        class="lab-fill-circle-minus text-lg leading-none">
                                    </button>
                                    <input type="number" :id="'quantityInput' + index"
                                        v-on:keyup="quantityUp($event, index, product)"
                                        v-on:keypress="onlyNumber($event)" v-model="product.quantity"
                                        class="text-center w-full h-5 text-sm font-medium">
                                    <button @click.prevent="quantityIncrement(index, product)" type="button"
                                        :class="product.quantity === product.order_quantity ? 'cursor-not-allowed text-[#A0A3BD]' : 'text-primary'"
                                        class="lab-fill-circle-plus text-lg leading-none text-[#A0A3BD]"></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl shadow-card mb-6 p-4 bg-white">
            <div class="mb-4">
                <label for="return_reason_id" class="capitalize font-medium mb-2">{{ $t('label.return_reason')
                    }} <span class="text-danger">*</span></label>
                <vue-select class="field-control appearance-none cursor-pointer" id="return_reason_id"
                    v-bind:class="errors.return_reason_id ? 'invalid' : ''" v-model="form.return_reason_id"
                    :options="returnReasons" label-by="title" value-by="id" :closeOnSelect="true" :searchable="true"
                    :clearOnClose="true" placeholder="--" search-placeholder="--" />
                <small class="db-field-alert" v-if="errors.return_reason_id">
                    {{ errors.return_reason_id[0] }}
                </small>
            </div>
            <div class="mb-4">
                <label for="note" class="capitalize font-medium mb-2">{{ $t('label.return_note') }}</label>
                <textarea id="note" class="field-control" v-model="form.note"
                    v-bind:class="errors.note ? 'invalid' : ''"></textarea>
                <small class="db-field-alert" v-if="errors.note">{{
                    errors.note[0]
                    }}</small>
            </div>
            <div>
                <label class="capitalize font-medium mb-2">{{ $t('label.attachment') }}</label>
                <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                    class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" multiple>
            </div>
        </div>
        <button type="submit" class="field-button w-fit font-semibold tracking-wide">{{ $t('button.submit_return')
            }}</button>
    </form>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";
export default {
    name: "ReturnOrderRequestComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            isAllReturn: false,
            form: {
                return_reason_id: null,
                note: "",
                order_id: null,
                order_serial_no: null,
                products: []
            },
            search: {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: "id",
                order_type: "desc",
            },
            products: [],
            qty: [],
            image: "",
            errors: {},
        }
    },
    computed: {
        order: function () {
            return this.$store.getters['frontendOrder/show'];
        },
        orderProducts: function () {
            return this.$store.getters['frontendOrder/orderProducts'];
        },
        returnReasons: function () {
            return this.$store.getters["frontendReturnReason/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.$store.dispatch("frontendOrder/show", this.$route.params.id).then(res => {
                this.form.order_id = res.data.data.id;
                this.form.order_serial_no = res.data.data.order_serial_no;
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            });

            this.$store.dispatch('frontendReturnReason/lists', {
                order_column: 'id',
                order_type: 'asc',
                status: statusEnum.ACTIVE
            });
        }
    },
    methods: {
        changeImage: function (e) {
            this.image = e.target.files;
        },
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        close: function () {
            this.errors.products = ""
        },
        selectAll(e, products) {
            products.forEach((product, i) => {
                let quantity = 1;
                let quantityExist = this.qty.find(p =>
                    p.product_id === product.product_id
                );

                let quantityIndex = this.qty.findIndex(p =>
                    p.product_id === product.product_id
                );

                let productData = {
                    product_id: product.product_id,
                    quantity: quantity,
                    price: product.price,
                    total: product.total,
                    tax: product.tax,
                    order_quantity: product.order_quantity,
                    return_price: parseFloat(product.quantity * product.price),
                    has_variation: product.has_variation,
                    variation_id: product.variation_id,
                    variation_names: product.variation_names,
                };

                if (quantityExist) {
                    productData.quantity = this.qty[quantityIndex].quantity;
                    quantity = this.qty[quantityIndex].quantity;
                } else {
                    productData.quantity = product.quantity;
                    quantity = product.quantity;
                }

                if (this.isAllReturn) {
                    if (product.is_refundable) {
                        product.isReturn = true;
                        let productExist = this.products.find(p =>
                            p.product_id === product.product_id
                        );

                        if (productExist) {
                            let productIndex = this.products.findIndex(p =>
                                p.product_id === product.product_id
                            );
                            this.products.splice(productIndex, 1);
                            this.products.push(productData);
                        } else {
                            this.products.push(productData);
                        }
                    }
                }
                else {
                    product.isReturn = false;
                    this.products = [];
                    this.qty = [];
                }
            })
        },

        selectProduct(e, i, product) {
            let quantity = 1;
            let quantityExist = this.qty.find(p =>
                p.product_id === product.product_id
            );

            let quantityIndex = this.qty.findIndex(p =>
                p.product_id === product.product_id
            );

            let productData = {
                product_id: product.product_id,
                quantity: quantity,
                price: product.price,
                total: product.total,
                tax: product.tax,
                order_quantity: product.order_quantity,
                return_price: parseFloat(product.quantity * product.price),
                has_variation: product.has_variation,
                variation_id: product.variation_id,
                variation_names: product.variation_names,
            };

            if (quantityExist) {
                productData.quantity = this.qty[quantityIndex].quantity;
                quantity = this.qty[quantityIndex].quantity;
            } else {
                productData.quantity = product.quantity;
                quantity = product.quantity;
            }

            if (e.target.checked) {
                this.products.push(productData);
            } else {
                let productIndex = this.products.findIndex(p =>
                    p.product_id === product.product_id
                );
                this.products.splice(productIndex, 1);
                this.isAllReturn = false;
            }
        },

        quantityIncrement: function (i, product) {
            let quantityExist = this.qty.find(p =>
                p.product_id === product.product_id
            );

            let quantity = 1;
            let quantityIndex = null;

            if (quantityExist) {
                quantityIndex = this.qty.findIndex(p =>
                    p.product_id === product.product_id
                );

                if (product.quantity < this.qty[quantityIndex].max_quantity) {
                    product.quantity += 1;
                } else {
                    product.quantity = this.qty[quantityIndex].max_quantity;
                }
                quantity = product.quantity;
                this.qty[quantityIndex].quantity = quantity;
            }

            let productExist = this.products.find(p =>
                p.product_id === product.product_id
            );

            if (productExist) {
                let productIndex = this.products.findIndex(p =>
                    p.product_id === product.product_id
                );
                this.products[productIndex].quantity = quantity;
                this.products[productIndex].return_price = parseFloat(quantity * product.price);
            }
        },

        quantityDecrement: function (i, product) {
            let quantityData = {
                product_id: product.product_id,
                quantity: product.quantity,
                max_quantity: product.order_quantity
            }
            if (product.quantity > 1) {
                product.quantity -= 1;
            }
            let quantityExist = this.qty.find(p =>
                p.product_id === product.product_id
            );

            let quantity = product.quantity;
            let quantityIndex = null;

            if (!quantityExist) {
                quantity = product.quantity;
                if (quantity === 0) {
                    quantity = 1;
                }
                quantityData.quantity = quantity;
                this.qty.push(quantityData);
            } else {
                quantityIndex = this.qty.findIndex(p =>
                    p.product_id === product.product_id
                );

                if (quantity === 0) {
                    quantity = 1;
                }
                this.qty[quantityIndex].quantity = quantity;
            }

            let productExist = this.products.find(p =>
                p.product_id === product.product_id
            );

            if (productExist) {
                let productIndex = this.products.findIndex(p =>
                    p.product_id === product.product_id
                );
                this.products[productIndex].quantity = quantity;
                this.products[productIndex].return_price = parseFloat(quantity * product.price);
            }

        },

        quantityUp: function (e, index, product) {
            let quantityData = {
                product_id: product.product_id,
                quantity: product.quantity,
                max_quantity: product.order_quantity
            }

            let quantity = parseInt(e.target.value);
            let quantityIndex = null;

            if (quantity === 0 || quantity < 0) {
                quantity = 1;
                product.quantity = quantity;
            }
            if (quantity > product.order_quantity) {
                quantity = product.order_quantity
                product.quantity = product.order_quantity
            }

            let quantityExist = this.qty.find(p =>
                p.product_id === product.product_id
            );

            if (!quantityExist) {
                quantityData.quantity = quantity;
                this.qty.push(quantityData);
            } else {
                quantityIndex = this.qty.findIndex(p =>
                    p.product_id === product.product_id
                );
                this.qty[quantityIndex].quantity = quantity;
            }

            let productExist = this.products.find(p =>
                p.product_id === product.product_id
            );

            if (productExist) {
                let productIndex = this.products.findIndex(p =>
                    p.product_id === product.product_id
                );
                this.products[productIndex].quantity = quantity;
                this.products[productIndex].return_price = parseFloat(quantity * product.price);
            }
        },

        save: function () {
            try {

                const fd = new FormData();
                fd.append('return_reason_id', this.form.return_reason_id !== null ? this.form.return_reason_id : '');
                fd.append('note', this.form.note);
                fd.append('order_id', this.form.order_id);
                fd.append('order_serial_no', this.form.order_serial_no);
                fd.append('products', this.products.length > 0 ? JSON.stringify(this.products) : '');
                if (this.image) {
                    for (let i = 0; i < this.image.length; i++) {
                        fd.append('image[]', this.image[i]);
                    }
                }
                this.loading.isActive = true;
                this.$store.dispatch('frontendReturnAndRefund/save', {
                    id: this.$route.params.id,
                    form: fd,
                    search: this.search
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(0, this.$t("menu.return_orders"));
                    this.form = {
                        return_reason_id: null,
                        note: "",
                        order_id: null,
                        order_serial_no: null,
                        products: []
                    };
                    this.products = [];
                    this.qty = [];
                    this.image = "";
                    this.errors = {};
                    this.$refs.imageProperty.value = null;
                    this.$router.push({ name: "frontend.account.returnOrders" });
                }).catch((err) => {
                    this.loading.isActive = false;
                    if (err.response.data.errors === undefined) {
                        if (err.response.data.message) {
                            this.errors = {};
                            alertService.error(err.response.data.message);
                        }
                    } else {
                        this.errors = err.response.data.errors;
                    }
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },


    }
}
</script>