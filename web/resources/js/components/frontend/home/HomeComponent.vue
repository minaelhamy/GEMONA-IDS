<template>
    <main class="marketplace-home">
        <SliderComponent />
        <CategoryComponent />
        <MostPopularComponent />
        <ProductSectionComponent />
    </main>
</template>

<script>

import SliderComponent from "./SliderComponent.vue";
import CategoryComponent from "./CategoryComponent.vue";
import ProductSectionComponent from "./ProductSectionComponent.vue";
import MostPopularComponent from "./MostPopularComponent.vue";

export default {
    name: "HomeComponent",
    components: {
        MostPopularComponent,
        SliderComponent,
        CategoryComponent,
        ProductSectionComponent
    }
};
</script>

<style scoped>
.marketplace-home {
    min-height: 70vh;
    padding-bottom: 36px;
    background: #eef0f2;
}
</style>
