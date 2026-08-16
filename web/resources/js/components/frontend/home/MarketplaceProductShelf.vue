<template>
    <div class="marketplace-shelf-wrap">
        <button
            v-if="displayProducts.length > 4"
            type="button"
            class="marketplace-shelf-arrow marketplace-shelf-arrow-left"
            aria-label="Scroll products left"
            @click="scrollShelf(-1)"
        >
            <i class="lab-line-chevron-left"></i>
        </button>

        <div ref="shelf" class="marketplace-shelf" tabindex="0">
            <article
                v-for="product in displayProducts"
                :key="product.id"
                class="marketplace-product"
            >
                <div class="marketplace-product-media">
                    <button
                        type="button"
                        :aria-label="product.wishlist ? 'Remove from wishlist' : 'Add to wishlist'"
                        :class="product.wishlist ? 'lab-fill-heart text-primary' : 'lab-line-heart'"
                        class="marketplace-wishlist"
                        @click.prevent="wishlist(product, product.wishlist = !product.wishlist)"
                    ></button>

                    <router-link
                        :to="{ name: 'frontend.product.details', params: { slug: product.slug } }"
                        class="block w-full h-full"
                    >
                        <img
                            :src="product.cover"
                            :alt="product.name"
                            class="marketplace-product-image"
                            loading="lazy"
                            decoding="async"
                        >
                    </router-link>
                </div>

                <router-link
                    :to="{ name: 'frontend.product.details', params: { slug: product.slug } }"
                    class="marketplace-product-name"
                >
                    {{ product.name }}
                </router-link>

                <div class="marketplace-rating" aria-label="Product rating">
                    <starRating
                        border-color="#F2A900"
                        :rounded-corners="true"
                        :padding="2"
                        :border-width="2"
                        :star-size="8"
                        inactive-color="#FFFFFF"
                        active-color="#F2A900"
                        :round-start-rating="false"
                        :show-rating="false"
                        :read-only="true"
                        :max-rating="5"
                        :rating="rating(product)"
                    />
                    <span v-if="product.rating_star_count > 0">({{ product.rating_star_count }})</span>
                </div>

                <div v-if="product.is_offer" class="flex items-baseline gap-2">
                    <strong class="marketplace-price">{{ product.discounted_price }}</strong>
                    <del class="text-xs text-gray-400">{{ product.currency_price }}</del>
                </div>
                <strong v-else class="marketplace-price">{{ product.currency_price }}</strong>
            </article>
        </div>

        <button
            v-if="displayProducts.length > 4"
            type="button"
            class="marketplace-shelf-arrow marketplace-shelf-arrow-right"
            aria-label="Scroll products right"
            @click="scrollShelf(1)"
        >
            <i class="lab-line-chevron-right"></i>
        </button>
    </div>
</template>

<script>
import starRating from "vue-star-rating";
import router from "../../../router";

export default {
    name: "MarketplaceProductShelf",
    components: { starRating },
    props: {
        products: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        displayProducts() {
            return this.products.filter((product) => {
                if (Object.prototype.hasOwnProperty.call(product, "has_image")) {
                    return product.has_image === true;
                }
                return product.cover && !product.cover.includes("/images/default/product/");
            });
        },
    },
    methods: {
        rating(product) {
            return product.rating_star_count > 0
                ? product.rating_star / product.rating_star_count
                : 0;
        },
        scrollShelf(direction) {
            const shelf = this.$refs.shelf;
            if (!shelf) return;
            shelf.scrollBy({ left: direction * Math.max(320, shelf.clientWidth * 0.75), behavior: "smooth" });
        },
        wishlist(product, toggle) {
            this.$store.dispatch("frontendWishlist/toggle", {
                product_id: product.id,
                toggle,
            }).catch((err) => {
                if (err.response && err.response.status === 401) {
                    product.wishlist = false;
                    router.push({ name: "auth.login" });
                }
            });
        },
    },
};
</script>

<style scoped>
.marketplace-shelf-wrap {
    position: relative;
}

.marketplace-shelf {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(180px, 1fr);
    gap: 18px;
    overflow-x: auto;
    overscroll-behavior-inline: contain;
    scroll-snap-type: inline mandatory;
    scrollbar-width: thin;
    padding: 2px 2px 12px;
}

.marketplace-product {
    min-width: 0;
    scroll-snap-align: start;
}

.marketplace-product-media {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f7f8fa;
    border: 1px solid #edf0f2;
    border-radius: 6px;
}

.marketplace-product-image {
    width: 100%;
    height: 100%;
    padding: 12px;
    object-fit: contain;
    transition: transform 220ms ease;
}

.marketplace-product:hover .marketplace-product-image {
    transform: scale(1.045);
}

.marketplace-wishlist {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
    width: 32px;
    height: 32px;
    border: 1px solid #e4e7eb;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 2px 8px rgba(23, 26, 45, 0.08);
}

.marketplace-product-name {
    display: -webkit-box;
    min-height: 44px;
    margin-top: 11px;
    overflow: hidden;
    color: #171a2d;
    font-size: 15px;
    font-weight: 650;
    line-height: 1.4;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}

.marketplace-product-name:hover {
    color: rgb(var(--primary));
}

.marketplace-rating {
    display: flex;
    align-items: center;
    min-height: 20px;
    gap: 6px;
    margin: 5px 0;
    color: #69707a;
    font-size: 11px;
}

.marketplace-price {
    display: block;
    color: #171a2d;
    font-size: 19px;
    line-height: 1.3;
}

.marketplace-shelf-arrow {
    position: absolute;
    top: 91px;
    z-index: 4;
    width: 42px;
    height: 58px;
    border: 1px solid #dfe3e8;
    border-radius: 5px;
    color: #171a2d;
    background: rgba(255, 255, 255, 0.97);
    box-shadow: 0 3px 12px rgba(23, 26, 45, 0.1);
    opacity: 0;
    transition: opacity 180ms ease, border-color 180ms ease;
}

.marketplace-shelf-wrap:hover .marketplace-shelf-arrow,
.marketplace-shelf-arrow:focus-visible {
    opacity: 1;
}

.marketplace-shelf-arrow:hover {
    border-color: rgb(var(--primary));
    color: rgb(var(--primary));
}

.marketplace-shelf-arrow-left { left: -8px; }
.marketplace-shelf-arrow-right { right: -8px; }

@media (min-width: 1280px) {
    .marketplace-shelf { grid-auto-columns: minmax(190px, 1fr); }
}

@media (max-width: 639px) {
    .marketplace-shelf {
        grid-auto-columns: 68vw;
        gap: 12px;
    }
    .marketplace-product-media { height: 210px; }
    .marketplace-shelf-arrow { display: none; }
}
</style>
