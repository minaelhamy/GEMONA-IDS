<template>
    <LoadingComponent :props="loading" />
    <section v-if="sliders.length > 0" class="marketplace-hero">
        <div class="marketplace-container">
            <Swiper
                v-if="sliders.length > 0"
                dir="rtl"
                :slides-per-view="1"
                :speed="1000"
                :loop="true"
                :navigation="true"
                :pagination="{ clickable: true }"
                :autoplay="{ delay: 2500 }"
                :modules="modules"
                class="banner-swiper"
            >
                <SwiperSlide v-for="slider in sliders" :key="slider.id">
                    <div v-if="slider.link">
                        <a :href="slider.link">
                            <img class="marketplace-hero-image" :src="slider.image" :alt="slider.title || 'Featured collection'" decoding="async">
                        </a>
                    </div>
                    <div v-else>
                        <img class="marketplace-hero-image" :src="slider.image" :alt="slider.title || 'Featured collection'" decoding="async">
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script>
import 'swiper/css';
import {Navigation, Pagination, Autoplay} from 'swiper/modules';
import {Swiper, SwiperSlide} from 'swiper/vue';
import statusEnum from "../../../enums/modules/statusEnum";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "SliderComponent",
    components: {
        Swiper,
        SwiperSlide,
        LoadingComponent
    },
    setup() {
        return {
            modules: [Navigation, Pagination, Autoplay],
        }
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            sliderProps: {
                search: {
                    paginate: 0,
                    order_column: 'id',
                    order_type: 'desc',
                    status: statusEnum.ACTIVE
                }
            }
        }
    },
    computed: {
        sliders: function () {
            return this.$store.getters['frontendSlider/lists'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendSlider/lists", this.sliderProps.search).then((res) => {
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    }
}
</script>

<style scoped>
.marketplace-hero {
    position: relative;
    padding-top: 16px;
}

.marketplace-container {
    width: min(100% - 24px, 1440px);
    margin-inline: auto;
}

.marketplace-hero-image {
    display: block;
    width: 100%;
    height: clamp(240px, 31vw, 410px);
    object-fit: cover;
    border-radius: 6px;
}

.banner-swiper {
    overflow: hidden;
}

@media (max-width: 639px) {
    .marketplace-hero { padding-top: 8px; }
    .marketplace-container { width: 100%; }
    .marketplace-hero-image {
        height: 230px;
        border-radius: 0;
    }
}
</style>
