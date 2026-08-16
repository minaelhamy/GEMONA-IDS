<template>
    <LoadingComponent :props="loading" />
    <section v-if="displayProducts.length > 0" class="marketplace-section">
        <div class="marketplace-container marketplace-panel">
            <div class="marketplace-section-heading">
                <div>
                    <span class="marketplace-eyebrow">Popular right now</span>
                    <h2>{{ $t("label.most_popular") }}</h2>
                </div>
                <router-link :to="{ name: 'frontend.mostPopular.products' }" class="marketplace-view-all">
                    {{ $t("label.show_more") }}
                    <i class="lab-line-chevron-right"></i>
                </router-link>
            </div>
            <MarketplaceProductShelf :products="displayProducts" />
        </div>
    </section>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent.vue";
import MarketplaceProductShelf from "./MarketplaceProductShelf.vue";

export default {
    name: "MostPopularComponent",
    components: { MarketplaceProductShelf, LoadingComponent },
    data() {
        return { loading: { isActive: false } };
    },
    computed: {
        products() {
            return this.$store.getters["frontendProduct/popularProducts"] || [];
        },
        displayProducts() {
            return this.products.filter((product) => {
                if (Object.prototype.hasOwnProperty.call(product, "has_image")) {
                    return product.has_image === true;
                }
                return product.cover && !product.cover.includes("/images/default/product/");
            }).slice(0, 12);
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendProduct/popularProducts", {
            paginate: 0,
            limit: 12,
            order_column: "id",
            order_type: "desc",
        }).finally(() => {
            this.loading.isActive = false;
        });
    },
};
</script>

<style scoped>
.marketplace-section { padding: 0 0 22px; }
.marketplace-container { width: min(100% - 24px, 1440px); margin-inline: auto; }
.marketplace-panel {
    padding: 20px;
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    background: #fff;
}
.marketplace-section-heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 16px;
}
.marketplace-section-heading h2 { color: #171a2d; font-size: 26px; font-weight: 760; line-height: 1.15; }
.marketplace-eyebrow { margin-bottom: 3px; color: rgb(var(--primary)); font-size: 11px; font-weight: 750; text-transform: uppercase; }
.marketplace-view-all { display: flex; align-items: center; gap: 7px; color: #343952; font-size: 13px; font-weight: 700; white-space: nowrap; }
.marketplace-view-all:hover { color: rgb(var(--primary)); }
@media (max-width: 639px) {
    .marketplace-container { width: min(100% - 20px, 1440px); }
    .marketplace-panel { padding: 16px 14px; }
    .marketplace-section-heading h2 { font-size: 23px; }
}
</style>
