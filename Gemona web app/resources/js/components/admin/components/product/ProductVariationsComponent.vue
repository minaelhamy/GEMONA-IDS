<template>
    <div class="form-col-12 sm:form-col-6" v-if="variations.length > 0">
        <label v-for="variationLabel in variations.slice(0, 1)" :key="variationLabel" class="db-field-title required">
            {{ variationLabel.product_attribute_name }}</label>
        <select class="db-field-control" @change="selectVariation(variation)" v-model="selectedVariationIndex">
            <option value="-1">{{ $t('label.please_select') }}</option>
            <option :value="index" v-for="(variation, index) in variations" :key="index">
                {{ variation.product_attribute_option_name }}
            </option>
        </select>
    </div>
    <ProductVariationsComponent v-on:method="getFinalVariation" :mode="mode" :item="item" :key="selectedVariations"
                               v-if="selectedVariations.length > 0" :variations="selectedVariations"/>
</template>

<script>

export default {
    name: 'ProductVariationsComponent',
    props: ['variations', 'mode', 'item'],
    emits: ['method'],
    data() {
        return {
            loading: {
                isActive: false
            },
            selectedVariationIndex: "-1",
            selectedVariations: [],
            finalSelectedVariation: null
        }
    },
    mounted() {
        if (this.mode === 'edit') {
            this.loading.isActive = true;
            this.$store.dispatch("productVariation/ancestorsAndSelfId", this.item.variation_id).then((resAncestor) => {
                this.loading.isActive = false;
                this.variations.map((variation, variationIndex) => {
                    resAncestor.data.data.map((list, index) => {
                        if (variation.id === list) {
                            this.loading.isActive = true;
                            this.$store.dispatch("productVariation/childrenVariation", list).then((res) => {
                                this.loading.isActive = false;
                                this.selectedVariationIndex = variationIndex;
                                this.selectedVariations = res.data.data;
                                if (this.selectedVariationIndex !== "-1") {
                                    if (!this.variations[this.selectedVariationIndex].sku) {
                                        this.finalSelectedVariation = null;
                                        this.getFinalVariation(this.finalSelectedVariation);
                                    } else {
                                        this.finalSelectedVariation = this.variations[this.selectedVariationIndex];
                                        this.getFinalVariation(this.finalSelectedVariation);
                                    }
                                }
                            }).catch((err) => {
                                this.loading.isActive = false;
                            });
                        }
                    });
                });
            }).catch((err) => {
                this.loading.isActive = false;
            })
        }
    },
    methods: {
        selectVariation: function () {
            if (this.selectedVariationIndex !== "-1") {
                if (!this.variations[this.selectedVariationIndex].sku) {
                    this.finalSelectedVariation = null;
                    this.getFinalVariation(this.finalSelectedVariation);
                } else {
                    this.finalSelectedVariation = this.variations[this.selectedVariationIndex];
                    this.getFinalVariation(this.finalSelectedVariation);
                }
                this.$store.dispatch("productVariation/childrenVariation", this.variations[this.selectedVariationIndex].id).then((res) => {
                    this.selectedVariations = res.data.data;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.finalSelectedVariation = null;
                this.getFinalVariation(this.finalSelectedVariation);
                this.selectedVariations = [];
            }
        },
        getFinalVariation: function (variation) {
            this.$emit('method', variation);
        }
    }
}
</script>

<style scoped>
.save-btn {
    text-align: end;
    margin-top: 20px;
}
</style>
