<template>
    <LoadingComponent :props="loading" />

    <section v-if="promotions.length > 0" class="mb-10 sm:mb-20">
        <div class="container">
            <Swiper dir="ltr" :speed="1000" class="ad-swiper" :breakpoints="breakpoints">
                <SwiperSlide v-for="promotion in promotions" class="mobile:!w-52">
                    <router-link :to="{name: 'frontend.promotion.products', params: { slug: promotion.slug }}" class=" w-full">
                        <img class="w-full block rounded-2xl" :src="promotion.cover" alt="promotion">
                    </router-link>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script>

import statusEnum from "../../../enums/modules/statusEnum";
import {Swiper, SwiperSlide} from 'swiper/vue';
import promotionTypeEnum from "../../../enums/modules/promotionTypeEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "PromotionComponent",
    components: {
        Swiper,
        SwiperSlide,
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            breakpoints: {
                0: {slidesPerView: 'auto', spaceBetween: 16},
                640: {slidesPerView: 3, spaceBetween: 24},
            }
        }
    },
    computed: {
        promotions: function () {
            return this.$store.getters["frontendPromotion/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendPromotion/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            type: promotionTypeEnum.SMALL,
            status: statusEnum.ACTIVE,
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
}
</script>