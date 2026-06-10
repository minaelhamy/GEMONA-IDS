<template>
    <LoadingComponent :props="loading"/>
    <section class="mb-10 sm:mb-20">
        <div class="container">
            <div class="flex items-center justify-between gap-5 mb-6 max-md:mb-8">
                <div class="flex flex-wrap items-end gap-3 max-md:flex-col max-md:items-start max-md:gap-1.5">
                    <h3 class="text-3xl font-bold capitalize max-sm:text-lg">
                        {{ promotion.name }}
                    </h3>
                    <span class="text-xl font-medium capitalize max-sm:text-sm">
                        ({{
                            products.length
                        }} {{
                            products.length > 1 ? $t('label.products_found') : $t('label.product_found')
                        }})
                    </span>
                </div>
            </div>

            <div class="w-full max-md:p-0">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
                    <LoadingContentComponent :props="loadingContent" />
                    <ProductListComponent v-if="products.length > 0" :products="products"/>
                </div>
                <PaginationComponent @pagination-change-page="promotionProducts" :data="pagination" :limit="1" :keep-length="false"/>
            </div>
        </div>
    </section>
</template>

<script>
import ProductListComponent from "../components/ProductListComponent.vue";
import LoadingContentComponent from "../components/LoadingContentComponent.vue";
import LoadingComponent from "../components/LoadingComponent.vue";
import PaginationComponent from "../components/PaginationComponent.vue";

export default {
    name: "PromotionProductComponent",
    components: {
        PaginationComponent,
        LoadingComponent,
        LoadingContentComponent,
        ProductListComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            loadingContent: {
                isActive: false
            },
        }
    },
    computed: {
        promotion: function () {
            return this.$store.getters["frontendPromotion/show"];
        },
        pagination: function () {
            return this.$store.getters["frontendPromotion/productPagination"];
        },
        products: function () {
            return this.$store.getters["frontendPromotion/products"];
        }
    },
    mounted() {
        if (typeof this.$route.params.slug !== "undefined") {
            this.loading.isActive = true;
            this.$store.dispatch("frontendPromotion/show", this.$route.params.slug).then((res) => {
                this.loading.isActive = true;
                this.$store.dispatch("frontendPromotion/products", {
                    slug: this.$route.params.slug
                }).then((res) => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    },
    methods: {
         promotionProducts: function(page = 1) {
            if (typeof this.$route.params.slug !== "undefined") {
                this.loadingContent.isActive = true;
                this.$store.dispatch("frontendPromotion/products", {
                    slug: this.$route.params.slug,
                    page : page
                }).then((res) => {
                    this.loadingContent.isActive = false;
                }).catch((err) => {
                    this.loadingContent.isActive = false;
                });
            }
        },
    }
}
</script>