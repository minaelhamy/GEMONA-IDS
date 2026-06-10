<template>
    <SliderComponent />

    <CategoryComponent />

    <PromotionComponent />

    <ProductSectionComponent />

    <MostPopularComponent />

    <FlashSaleComponent />

    <ProductBrandComponent />

    <BenefitComponent />
</template>

<script>

import SliderComponent from "./SliderComponent.vue";
import CategoryComponent from "./CategoryComponent.vue";
import PromotionComponent from "./PromotionComponent.vue";
import BenefitComponent from "./BenefitComponent.vue";
import ProductSectionComponent from "./ProductSectionComponent.vue";
import FlashSaleComponent from "./FlashSaleComponent.vue";
import ProductBrandComponent from "./ProductBrandComponent.vue";
import MostPopularComponent from "./MostPopularComponent.vue";

export default {
    name: "HomeComponent",
    components: {
        MostPopularComponent,
        FlashSaleComponent,
        BenefitComponent,
        SliderComponent,
        CategoryComponent,
        PromotionComponent,
        ProductSectionComponent,
        ProductBrandComponent
    }
};
</script>
