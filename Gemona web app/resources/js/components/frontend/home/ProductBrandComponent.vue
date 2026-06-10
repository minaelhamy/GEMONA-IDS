<template>
    <LoadingComponent :props="loading" />

    <section class="mb-3 sm:mb-10" v-if="brands.length > 1">
        <div class="container">
            <h2 class="capitalize text-2xl sm:text-4xl font-bold -mb-10">
                {{ $t('label.popular_brands') }}
            </h2>
            <Swiper dir="ltr" :speed="1000" :loop="true" :navigation="true" :modules="modules" class="navigate-swiper" :breakpoints="breakpoints">
                <SwiperSlide v-for="brand in brands" class="mobile:!w-[120px]">
                    <router-link :to="{name: 'frontend.product', query:{ brand: brand.id }}" class="w-full rounded-2xl shadow-xs group border border-gray-100">
                        <figure class="w-full h-[120px] flex items-center justify-center">
                            <img :src="brand.cover" alt="brand" class="w-14">
                        </figure>
                        <span class="text-sm sm:text-lg font-medium capitalize text-center pb-3 block group-hover:text-primary">
                                {{ brand.name }}
                            </span>
                    </router-link>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script>
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";
import {Swiper, SwiperSlide} from "swiper/vue";
import {Autoplay, Navigation, Pagination} from "swiper/modules";
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
    name: "ProductBrandComponent",
    components: {
        Swiper, SwiperSlide,
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            breakpoints: {
                0: {slidesPerView: 'auto', spaceBetween: 16},
                640: {slidesPerView: 4, spaceBetween: 24},
                768: {slidesPerView: 5, spaceBetween: 24},
                1024: {slidesPerView: 6, spaceBetween: 24}
            },
        }
    },
    setup() {
        return {
            modules: [Navigation, Pagination, Autoplay],
        }
    },
    computed: {
        brands: function () {
            return this.$store.getters["frontendProductBrand/lists"];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendProductBrand/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            status: statusEnum.ACTIVE,
        }).then(res => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>

