<template>
    <LoadingComponent :props="loading" />

    <div v-if="productSections.length > 0">
        <section class="marketplace-section" v-for="productSection in productSections" :key="productSection.id" v-show="productSection.products.length > 0">
            <div class="marketplace-container marketplace-panel">
                <div class="marketplace-section-heading">
                    <h2>
                        {{ productSection.name }}
                    </h2>
                    <router-link
                        :to="{ name: 'frontend.productSection.products', params: { slug: productSection.slug } }"
                        class="marketplace-view-all">
                        {{ $t('label.show_more') }}
                        <i class="lab-line-chevron-right"></i>
                    </router-link>
                </div>
                <MarketplaceProductShelf :products="productSection.products" />
            </div>
        </section>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent.vue";
import MarketplaceProductShelf from "./MarketplaceProductShelf.vue";

export default {
    name: "ProductSectionComponent",
    components: {
        MarketplaceProductShelf,
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            }
        }
    },
    computed: {
        productSections: function () {
            return this.$store.getters["frontendProductSection/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendProductSection/lists").then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>

<style scoped>
.marketplace-section { padding: 0 0 22px; }
.marketplace-container { width: min(100% - 24px, 1440px); margin-inline: auto; }
.marketplace-panel { padding: 20px; border: 1px solid #e1e5e9; border-radius: 6px; background: #fff; }
.marketplace-section-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
.marketplace-section-heading h2 { color: #171a2d; font-size: 26px; font-weight: 760; line-height: 1.15; text-transform: capitalize; }
.marketplace-view-all { display: flex; align-items: center; gap: 7px; color: #343952; font-size: 13px; font-weight: 700; white-space: nowrap; }
.marketplace-view-all:hover { color: rgb(var(--primary)); }
@media (max-width: 639px) {
    .marketplace-container { width: min(100% - 20px, 1440px); }
    .marketplace-panel { padding: 16px 14px; }
    .marketplace-section-heading h2 { font-size: 23px; }
}
</style>
