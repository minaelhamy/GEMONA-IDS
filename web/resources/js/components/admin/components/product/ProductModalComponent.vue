<template>
    <div id="modal-demo" :class="{ 'modal-active': modal.isShowModal }"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-300 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-300 max-w-3xl">
            <div class="flex items-center justify-between gap-2 py-4 px-4 border-b border-slate-100">
                <h3 class="text-lg font-bold capitalize">{{ item.name }}</h3>
                <button @click="closeItemModal()" type="button"
                    class="lab-line-circle-cross text-lg text-danger"></button>
            </div>
            <form class="d-block w-full p-4">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title ">{{
                            $t("label.tax")
                            }}</label>
                        <vue-select v-model="item.tax_id" class="db-field-control f-b-custom-select" :options="taxes"
                            label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                            :placeholder="$t('label.select_one')" search-placeholder="--" :multiple="true" />
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">{{
                            $t("label.quantity")
                            }}</label>
                        <input v-model="item.quantity" v-on:keypress="onlyNumber($event)" min="1" type="number"
                            class="db-field-control" />
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">
                            {{ $t("label.discount") }}
                        </label>
                        <input v-model="item.discount" type="number" v-on:keypress="floatNumber($event)" min="0"
                            class="db-field-control" />
                    </div>
                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">
                            {{ $t("label.unit_cost") }}
                        </label>
                        <input v-model="item.price" type="number" v-on:keypress="floatNumber($event)" min="0"
                            class="db-field-control" />
                    </div>
                </div>
                <div class="form-row pt-1.5" v-if="item.is_variation">
                    <ProductVariationsComponent v-if="initialVariations.length > 0" v-on:method="selectedVariation"
                        :variations="initialVariations" :mode="item.mode" :item="item" />
                </div>

                <div class="modal-btns mt-8">
                    <button :disabled="item.is_variation && !finalVariation" @click="submitItem(item)" type="button"
                        class="modal-btn-fill disabled:opacity-25">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ $t("button.save") }}</span>
                    </button>
                    <button type="button" class="modal-btn-outline modal-close" @click="closeItemModal()">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>{{ $t("button.close") }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</template>

<script lang="js">
import appService from '../../../../services/appService';
import ProductVariationsComponent from './ProductVariationsComponent.vue'

export default {
    components: {
        ProductVariationsComponent,
    },
    name: 'ProductModalComponent',
    props: ['item', 'modal'],
    data() {
        return {
            finalVariation: null,
        }
    },
    mounted() {
        this.taxList();
    },
    computed: {
        taxes: function () {
            return this.$store.getters["tax/lists"];
        },
        initialVariations: function () {
            return this.$store.getters["productVariation/initialVariation"];
        },
    },
    watch: {
        item: function () {
            this.finalVariation = null;
        }
    },
    methods: {
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        floatNumber: function (e) {
            return appService.floatNumber(e);
        },
        taxList: function (page = 1) {
            this.$store.dispatch("tax/lists", { page });
        },
        taxRateById: function (id) {
            for (let i = 0; i < this.taxes.length; i++) {
                const tax = this.taxes[i];
                if (tax.id === id) {
                    return tax.tax_rate;
                }
            };
            return 0;
        },
        submitItem: function (item) {
            if (this.item.is_variation) {
                this.item.variation_id = this.finalVariation.id;
                this.item.sku = this.finalVariation.sku;
            }

            this.$emit('submitItem');
        },
        selectedVariation: function (variation) {
            this.finalVariation = variation;
            if (this.finalVariation) {
                this.$store.dispatch("productVariation/ancestorsToString", this.finalVariation.id).then((res) => {
                    this.item.variation_names = res.data.data;
                });
            }
        },
        closeItemModal: function () {
            this.modal.isShowModal = false;
        }
    }
}
</script>