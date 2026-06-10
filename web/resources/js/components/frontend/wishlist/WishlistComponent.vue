<template>
    <LoadingComponent :props="loading"/>
    <section v-if="logged" class="mb-10 sm:mb-20">
        <div class="container">

            <div class="max-md:hidden">
                <ul class="flex flex-wrap items-center">
                    <li class="after:content-['\e037'] after:font-icon rtl:after:rotate-180 after:text-sm after:mx-2 last:after:hidden">
                        <router-link class="text-base font-medium capitalize transition-all duration-500 hover:text-primary" :to="{name: 'frontend.home'}" >
                            {{ $t('label.home') }}
                        </router-link>
                    </li>
                    <li class="after:content-['\e037'] after:font-icon after:text-sm after:mx-2 last:after:hidden">
                        <span class="text-base font-medium capitalize text-text">
                            {{ $t('label.wishlist') }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="flex items-center justify-between gap-5 mb-6 max-md:mb-8">
                <div class="flex flex-wrap items-end gap-3 max-md:flex-col max-md:items-start max-md:gap-1.5">
                    <h3 class="text-3xl font-bold capitalize max-sm:text-lg">
                        {{ $t('label.my_wishlist') }}
                    </h3>
                    <span class="text-xl font-medium capitalize max-sm:text-sm">
                        ({{ products.length }} {{ products.length > 1 ? $t('label.products_found') : $t('label.product_found') }})
                    </span>
                </div>
            </div>

            <div class="w-full max-md:p-0">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
                    <LoadingContentComponent :props="loadingContent" />
                    <ProductListComponent v-if="products.length > 0" :products="products"/>
                </div>
                <PaginationComponent @pagination-change-page="wishlistProducts" :data="pagination" :limit="1" :keep-length="false"/>
            </div>
            <div v-if="products.length === 0" class="h-[calc(65vh_-_218px)] flex items-center justify-center">
                <img class="w-52" :src="setting.image_wishlist" alt="empty" >
            </div>
        </div>
    </section>
</template>

<script>
import PaginationComponent from "../components/PaginationComponent.vue";
import LoadingComponent from "../components/LoadingComponent.vue";
import LoadingContentComponent from "../components/LoadingContentComponent.vue";
import ProductListComponent from "../components/ProductListComponent.vue";

export default {
    name: "WishlistComponent",
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
        logged: function () {
            return this.$store.getters.authStatus;
        },
        products: function () {
            return this.$store.getters["frontendProduct/wishlistProducts"];
        },
        pagination: function () {
            return this.$store.getters["frontendProduct/wishlistProductPagination"];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
    },
    mounted() {
        this.wishlistProducts();
    },
    methods: {
        wishlistProducts: function (page = 1) {
            this.loadingContent.isActive = true;
            this.$store.dispatch("frontendProduct/wishlistProducts", {
                paginate: 1,
                per_page: 32,
                page: page
            }).then((res) => {
                this.loadingContent.isActive = false;
            }).catch((err) => {
                this.loadingContent.isActive = false;
            });
        }
    }
}
</script>
