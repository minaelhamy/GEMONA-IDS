<template>
    <SliderComponent />

    <CategoryComponent />

    <ProductSectionComponent />

    <MostPopularComponent />

    <ProductBrandComponent />

    <BenefitComponent />
</template>

<script>

import SliderComponent from "./SliderComponent.vue";
import CategoryComponent from "./CategoryComponent.vue";
import BenefitComponent from "./BenefitComponent.vue";
import ProductSectionComponent from "./ProductSectionComponent.vue";
import ProductBrandComponent from "./ProductBrandComponent.vue";
import MostPopularComponent from "./MostPopularComponent.vue";

export default {
    name: "HomeComponent",
    components: {
        MostPopularComponent,
        BenefitComponent,
        SliderComponent,
        CategoryComponent,
        ProductSectionComponent,
        ProductBrandComponent
    }
};
</script>
