<template>
    <LoadingComponent :props="loading" />
    <section class="mb-10 sm:mb-20">
        <div class="container">
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
                <SwiperSlide v-for="slider in sliders">
                    <div v-if="slider.link">
                        <a :href="slider.link">
                            <img class="w-full rounded-2xl" :src="slider.image" alt="banner" >
                        </a>
                    </div>
                    <div v-else>
                        <img class="w-full rounded-2xl" :src="slider.image" alt="banner" >
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
