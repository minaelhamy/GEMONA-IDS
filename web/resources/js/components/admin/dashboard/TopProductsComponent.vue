<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="font-semibold text-lg capitalize text-heading">{{ $t('menu.top_products') }}</h3>
            </div>
            <div class="db-card-body">
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-4">

                    <ProductListComponent v-if="products.length > 0" :products="products" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import ProductListComponent from "../components/ProductListComponent";
export default {
    name: "TopProductsComponent",
    components: { LoadingComponent, ProductListComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
        };
    },
    mounted() {
        this.topProducts();
    },
    computed: {
        products: function () {
            return this.$store.getters["dashboard/topProducts"];
        }
    },
    methods: {
        topProducts: function () {
            this.loading.isActive = true;
            this.$store.dispatch('dashboard/topProducts').then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    },
}
</script>
