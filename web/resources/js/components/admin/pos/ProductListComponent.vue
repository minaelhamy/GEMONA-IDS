<template>
    <div
        class="grid gap-3 sm:gap-[18px] grid-cols-[repeat(auto-fill,_minmax(140px,_1fr))] sm:grid-cols-[repeat(auto-fill,_minmax(185px,_1fr))] mb-8 md:mb-0">
        <div v-if="products.length > 0" v-for="product in products" @click.prevent="handleProductModal(product)"
            data-modal="#modal"
            class="sm:p-2 rounded-2xl sm:shadow-card transition-all duration-300 sm:hover:shadow-hover group bg-white cursor-pointer">
            <div class="relative overflow-hidden rounded-xl isolate">
                <label
                    class="capitalize text-xs font-semibold rounded-xl py-1 px-2 shadow-badge absolute top-3 left-3 z-10 bg-secondary text-white"
                    v-if="product.is_offer && product.flash_sale">{{ $t('label.flash_sale') }}</label>

                <img :src="product.cover" alt="product"
                    class="w-full rounded-xl transition-all duration-300 group-hover:scale-105 group-hover:rotate-3">
            </div>

            <div class="px-1 sm:px-0 pt-4 pb-2">
                <h3
                    class="capitalize text-base font-semibold whitespace-nowrap mb-1.5 transition-all duration-300 hover:text-primary overflow-hidden">
                    {{ product.name }}
                </h3>

                <div class="flex flex-wrap items-center gap-2 mb-5">
                    <div class="flex items-center gap-1">
                        <starRating border-color="#FFBC1F" :rounded-corners="true" :padding="2.5" :border-width="2.5"
                            :star-size="9" class="mt-[2px]" inactive-color="#FFFFFF" active-color="#FFBC1F"
                            :round-start-rating="false" :show-rating="false" :read-only="true" :max-rating="5"
                            :rating="(product.rating_star / product.rating_star_count)" />
                    </div>
                    <div v-if="product.rating_star_count > 0" class="flex items-center gap-1 mt-[5px]">
                        <span class="text-xs font-medium whitespace-nowrap text-text">{{ (product.rating_star /
                            product.rating_star_count).toFixed(1) }}</span>
                        <span class="text-xs font-medium whitespace-nowrap text-text hover:text-primary">({{
                            product.rating_star_count }} {{ product.rating_star_count > 1 ? $t('label.reviews') :
                                $t('label.review') }})</span>
                    </div>
                </div>

                <div class="flex flex-wrap-reverse items-center gap-x-3 gap-y-1" v-if="product.is_offer">
                    <h3 class="text-xl sm:text-[22px] font-bold">
                        <span>{{ product.discounted_price }}</span>
                    </h3>
                    <h4 class="text-sm sm:text-base font-semibold text-shopperz-red">
                        <del>{{ product.currency_price }}</del>
                    </h4>
                </div>
                <h4 class="text-xl sm:text-[22px] font-bold" v-else>
                    <span>{{ product.currency_price }}</span>
                </h4>
            </div>
        </div>
    </div>


    <div id="variation-modal"
        class="fixed inset-0 z-50 p-3 w-screen h-dvh overflow-y-auto bg-black/50 transition-all duration-500 opacity-0 invisible">
        <div class="w-full rounded-xl mx-auto bg-white transition-all duration-500 max-w-4xl">
            <div class="flex items-center justify-between gap-2 py-4 px-4 border-b border-slate-100">
                <h3 class="text-lg font-bold capitalize">{{ $t('label.product_variation') }}</h3>
                <button @click="reset" type="button" class="lab-line-circle-cross text-lg text-[#E93C3C]"></button>
            </div>
            <ProductDetailsComponent v-if="productId" :method="reset" :productId="productId" />
        </div>
    </div>
</template>

<script>

import starRating from "vue-star-rating";
import ProductDetailsComponent from "./ProductDetailsComponent";
import targetService from "../../../services/targetService";
export default {
    name: "ProductListComponent",
    components: {
        starRating,
        ProductDetailsComponent
    },
    props: {
        "products": "object",
    },
    data() {
        return {
            rating: [],
            productId: ""
        }
    },
    methods: {
        handleProductModal: function (product) {
            this.productId = product.id;
        },
        reset: function () {
            targetService.hideTarget("variation-modal", 'modal-active');
            setTimeout(() => {
                this.productId = "";
            }, 500);
        },
    }
}
</script>