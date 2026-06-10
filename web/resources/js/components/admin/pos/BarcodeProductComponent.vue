<template>
    <LoadingComponent :props="loading" />
    <div class="db-group-field">
        <input v-on:keypress="onlyNumber($event)" v-model="barcode" type="number" id="barcode" ref="posBarcodeScan"
            :placeholder="$t('label.barcode')" @keyup.enter="getProduct">
        <button type="button" @click="getProduct" class="lab lab-line-enter"></button>
    </div>

</template>


<script>
import LoadingComponent from "../components/LoadingComponent.vue";
import ProductListComponent from "../components/ProductListComponent.vue";
import VariationComponent from "../components/VariationComponent.vue";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";

export default {
    name: "BarcodeProductComponent",
    components: {
        VariationComponent,
        ProductListComponent,
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            props: {
                search: {
                    product_id: null,
                }
            },
            barcode: null,
            selectedVariation: null,
            productArray: {},
            initProduct: {
                isVariation: false,
                variationId: null,
                sku: null,
                stock: 0,
                quantity: 1,
                discount: 0,
                price: 0,
                oldPrice: 0,
                totalPrice: 0
            },
            temp: {
                name: "",
                image: "",
                isVariation: false,
                variationId: null,
                productId: 0,
                sku: null,
                stock: 0,
                taxes: {},
                quantity: 1,
                discount: 0,
                price: 0,
                oldPrice: 0,
                totalPrice: 0
            },
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        product: function () {
            return this.$store.getters["posProduct/show"];
        },
        images: function () {
            return this.$store.getters["posProduct/showImages"];
        },
    },
    mounted() {
        this.$refs.posBarcodeScan.focus();
    },
    methods: {
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        currencyFormat: function (amount, decimal, currency, position) {
            return appService.currencyFormat(amount, decimal, currency, position);
        },
        getProduct: function () {
            if (this.barcode) {
                if (this.barcode.toString().length > 8) {
                    this.barcode = this.barcode.toString().slice(0, -1);
                } else if (this.barcode.toString().length > 7) {
                    this.barcode = this.barcode.toString().slice(0, -1);
                }

                this.barcode = parseInt(this.barcode);
                this.$store.dispatch("product/barcodeProduct", this.barcode).then((barcodeRes) => {
                    this.props.search.product_id = barcodeRes.data.data.product_id;

                    this.$store.dispatch("posProduct/show", this.props.search).then((res) => {
                        this.initProduct = {
                            isVariation: false,
                            variationId: null,
                            sku: res.data.data.sku,
                            stock: res.data.data.stock,
                            quantity: 1,
                            discount: 0,
                            price: res.data.data.price,
                            oldPrice: res.data.data.old_price,
                            totalPrice: res.data.data.price
                        };
                        this.temp = {
                            name: res.data.data.name,
                            image: res.data.data.image,
                            isVariation: false,
                            variationId: null,
                            productId: res.data.data.id,
                            sku: res.data.data.sku,
                            stock: res.data.data.stock,
                            taxes: res.data.data.taxes,
                            quantity: 1,
                            discount: 0,
                            price: res.data.data.price,
                            oldPrice: res.data.data.old_price,
                            totalPrice: res.data.data.price
                        };

                        if (barcodeRes.data.data.variation_id) {
                            this.selectedVariation = barcodeRes.data.data.variation_id;
                            this.$store.dispatch("posProductVariation/barcodeVariationProduct", barcodeRes.data.data.variation_id).then((variationRes) => {

                                this.temp.isVariation = true;
                                this.temp.variationId = variationRes.data.data.id;
                                this.temp.sku = variationRes.data.data.sku;
                                this.temp.stock = variationRes.data.data.stock;
                                this.temp.quantity = 1;
                                this.temp.discount = 0;
                                this.temp.price = variationRes.data.data.price;
                                this.temp.oldPrice = variationRes.data.data.old_price;
                                this.temp.totalPrice = variationRes.data.data.price;

                                if (this.temp.stock > 0) {
                                    this.addToCart();
                                } else {
                                    alertService.error(this.$t('label.stock_out'));
                                    this.barcode = null;
                                }
                            }).catch((err) => {
                                this.loading.isActive = false;
                            });
                        } else {
                            if (this.temp.stock > 0) {
                                this.addToCart();
                            } else {
                                alertService.error(this.$t('label.stock_out'));
                                this.barcode = null;
                            }
                        }
                    }).catch((err) => {
                        this.loading.isActive = false;
                    });

                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                    this.barcode = null;
                });
            }
        },
        addToCart: function () {
            this.productArray = {
                name: this.temp.name,
                product_id: this.temp.productId,
                image: this.temp.image,
                variation_names: '',
                variation_id: this.temp.variationId,
                sku: this.temp.sku,
                stock: this.temp.stock,
                taxes: this.temp.taxes,
                quantity: this.temp.quantity,
                discount: this.temp.discount,
                price: this.temp.price,
                old_price: this.temp.oldPrice,
                total_price: this.temp.totalPrice
            }

            if (this.selectedVariation) {
                this.$store.dispatch("posProductVariation/ancestorsToString", this.selectedVariation).then((res) => {
                    this.productArray.variation_names = res.data.data;
                    this.$store.dispatch("posCart/lists", this.productArray).then((res) => {
                        alertService.success(this.$t('message.add_to_cart'));
                        this.barcode = null;
                        this.productArray = {};
                        this.selectedVariation = null;
                        this.temp.isVariation = this.initProduct.isVariation;
                        this.temp.variationId = this.initProduct.variationId;
                        this.temp.sku = this.initProduct.sku;
                        this.temp.stock = this.initProduct.stock;
                        this.temp.quantity = this.initProduct.quantity;
                        this.temp.discount = this.initProduct.discount;
                        this.temp.price = this.initProduct.price;
                        this.temp.oldPrice = this.initProduct.oldPrice;
                        this.temp.totalPrice = this.initProduct.price;
                    }).catch((err) => {
                        alertService.error(this.$t('message.maximum_quantity'));
                        this.barcode = null;
                        this.variationComponent = true;
                        this.selectedVariation = null;
                        this.temp.stock = this.initProduct.stock;
                        this.temp.quantity = this.initProduct.quantity;
                    });
                }).catch((err) => {
                });
            } else {
                this.$store.dispatch("posCart/lists", this.productArray).then((res) => {
                    alertService.success(this.$t('message.add_to_cart'));
                    this.barcode = null;
                    this.productArray = {};
                    this.selectedVariation = null;
                    this.temp.isVariation = this.initProduct.isVariation;
                    this.temp.variationId = this.initProduct.variationId;
                    this.temp.sku = this.initProduct.sku;
                    this.temp.stock = this.initProduct.stock;
                    this.temp.quantity = this.initProduct.quantity;
                    this.temp.discount = this.initProduct.discount;
                    this.temp.price = this.initProduct.price;
                    this.temp.oldPrice = this.initProduct.oldPrice;
                    this.temp.totalPrice = this.initProduct.price;
                }).catch((err) => {
                    alertService.error(this.$t('message.maximum_quantity'));
                    this.barcode = null;
                    this.selectedVariation = null;
                    this.temp.stock = this.initProduct.stock;
                    this.temp.quantity = this.initProduct.quantity;
                });
            }
        }
    }
}
</script>