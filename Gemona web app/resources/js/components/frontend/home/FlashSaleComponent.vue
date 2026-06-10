<template>
    <LoadingComponent :props="loading"/>
    <section v-if="products.length > 0" class="mb-7 sm:mb-12">
        <div class="container">
            <div class="flex items-center justify-between gap-4 mb-5 sm:mb-7">
                <h2 class="text-2xl sm:text-4xl font-bold capitalize">
                    {{ $t('label.flash_sale') }}
                </h2>
                <router-link v-if="products.length === 8" :to="{name: 'frontend.flashSale.products'}" class="py-2 px-4 text-sm sm:py-3 sm:px-6 rounded-3xl capitalize sm:text-base font-semibold whitespace-nowrap bg-primary-slate text-primary transition-all duration-300 hover:bg-primary hover:text-white">
                    {{ $t('label.show_more') }}
                </router-link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                <ProductListComponent v-if="products.length > 0" :products="products"/>
            </div>
        </div>
    </section>
</template>

<script>
import ProductListComponent from "../components/ProductListComponent.vue";
import LoadingComponent from "../components/LoadingComponent.vue";

export default {
    name: "FlashSaleComponent",
    components: {
        ProductListComponent,
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
        products: function () {
            return this.$store.getters["frontendProduct/flashSaleProducts"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendProduct/flashSaleProducts", {
            paginate: 0,
            rand: 8
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
}
</script>