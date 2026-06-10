<template>
    <LoadingComponent :props="loading" />
    <div class="col-12" id="print">
        <div class="db-card p-4">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <div class="p-4 rounded-xl border border-gray-100">
                    <div class="col-12">
                        <h4 class="text-lg font-semibold capitalize mb-2">{{ review.user_name }}</h4>
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <starRating border-color="#FFBC1F" inactive-color="#FFFFFF" active-color="#FFBC1F"
                                :rounded-corners="true" :padding="2.5" :border-width="2.5" :star-size="11"
                                class="-mt-0.5" :round-start-rating="false" :show-rating="false" :read-only="true"
                                :max-rating="5" :rating="review.star" />
                        </div>
                        <p>{{ review.review }}</p>
                        <div class="flex flex-wrap gap-4" v-if="review.images?.length > 0">
                            <img v-for="reviewImage in review.images" :src="reviewImage" alt="image"
                                class="w-20 rounded-lg">
                        </div>
                    </div>
                    <div class="col-12">
                        <h3 class="text-lg font-semibold capitalize mb-2">{{ $t('label.product_information') }}</h3>
                        <div class="row py-2">
                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.name")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.product_name
                                        }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.sku")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.product_sku
                                        }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.brand")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.product_brand
                                        }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.buying_price")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.buying_price }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{
                                        $t("label.selling_price")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.selling_price
                                        }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.warranty")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.warranty }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.weight")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.weight }}</span>
                                </div>
                            </div>

                            <div class="col-4 !py-1.5">
                                <div class="db-list-item p-0">
                                    <span class="db-list-item-title w-full sm:w-1/2">{{ $t("label.unit")
                                        }}</span>
                                    <span class="db-list-item-text w-full sm:w-1/2">{{ review.unit_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 hidden-print">
        <div class="flex items-center justify-end gap-6">
            <PrintButtonComponent :props="printObj"
                :buttonClass="'flex items-center justify-center gap-1.5 h-10 px-6 rounded-3xl text-white bg-success'" />
        </div>
    </div>
</template>

<script>
import starRating from "vue-star-rating";
import PrintButtonComponent from "../components/buttons/PrintButtonComponent";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "ReviewShowComponent",
    components: {
        LoadingComponent,
        PrintButtonComponent,
        starRating
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.reviews"),
            },
        };
    },
    mounted() {
        this.show();
    },
    computed: {
        review: function () {
            return this.$store.getters["review/show"];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists']
        }
    },
    methods: {
        show: function () {
            if (!isNaN(this.$route.params.id)) {
                this.loading.isActive = true;
                this.$store
                    .dispatch("review/show", this.$route.params.id)
                    .then((res) => {
                        this.loading.isActive = false;
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                    });
            }
        },
    },
};
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
