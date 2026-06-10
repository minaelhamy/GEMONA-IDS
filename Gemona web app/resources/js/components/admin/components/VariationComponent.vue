<template>
    <dl v-if="variations.length > 0" class="flex flex-wrap items-center gap-x-6 gap-y-3 mb-6">
        <dt v-for="variationLabel in variations.slice(0, 1)" :key="variationLabel" class="capitalize text-lg font-semibold">
            {{ variationLabel.product_attribute_name }}:</dt>
        <dd class="flex flex-wrap items-center gap-3">
            <button @click="selectVariation(variation)"
                :class="selectedVariationId === variation.id ? 'text-white bg-primary' : ''" type="button"
                v-for="(variation, index) in variations" :key="index"
                class="px-3 h-8 leading-8 text-center rounded-full text-sm font-medium capitalize flex-shrink-0 cursor-pointer text-secondary bg-[#F7F7FC]">
                {{ variation.product_attribute_option_name }}
            </button>
        </dd>
    </dl>

    <VariationComponent :method="getFinalVariationId" :key="selectedVariations" v-if="selectedVariations.length > 0"
        :variations="selectedVariations" />
</template>

<script>

export default {
    name: "VariationComponent",
    props: {
        "variations": { type: Object },
        "method": { type: Function }
    },
    data() {
        return {
            selectedVariationId: null,
            selectedVariations: [],
            finalSelectedVariation: null
        }
    },
    methods: {
        selectVariation: function (variation) {
            this.selectedVariationId = variation.id;

            if (!variation.sku) {
                this.finalSelectedVariation = null;
                this.getFinalVariationId(this.finalSelectedVariation);
            } else {
                this.finalSelectedVariation = variation;
                this.getFinalVariationId(this.finalSelectedVariation);
            }

            this.$store.dispatch("posProductVariation/childrenVariation", this.selectedVariationId).then((res) => {
                this.selectedVariations = res.data.data;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        getFinalVariationId: function (id) {
            this.method(id);
        }
    }
}
</script>