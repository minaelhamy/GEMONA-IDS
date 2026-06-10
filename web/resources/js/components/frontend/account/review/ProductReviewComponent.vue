<template>
    <LoadingComponent :props="loading" />
    <h2 class="text-2xl font-bold mb-7 text-primary">{{ $t('menu.write_review') }}</h2>
    <form class="w-full mobile:mb-20" @submit.prevent="save">
        <div class="rounded-2xl shadow-card mb-6 bg-white">
            <div class="flex items-center gap-3 p-6 border-b border-gray-200">
                <img :src="productData.image" alt="product" class="w-14 h-14 object-cover rounded-md flex-shrink-0"
                 />
                <div>
                    <h4 class="text-lg font-medium capitalize mb-1.5">{{ productData.name }}</h4>
                    <h5 class="font-bold" v-if="productData.is_offer">{{ productData.currency_price }}</h5>
                    <h5 class="font-bold" v-else>{{ productData.old_currency_price }}</h5>
                </div>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <h4 class="capitalize font-semibold mb-3">{{ $t('label.your_review') }} ({{ activeRate }})</h4>
                    <nav class="flex items-center gap-3">
                        <button v-for="rate in 5" @click="activeRate = rate" type="button"
                            :class="{ '!text-[#F6A609]': activeRate >= rate }"
                            class="lab-fill-star text-4xl text-[#D9DBE9]"></button>
                    </nav>
                    <small class="db-field-alert" v-if="errors.star">
                        {{ errors.star[0] }}
                    </small>
                </div>
                <div class="mb-6">
                    <h4 class="capitalize font-semibold mb-3">{{ $t('label.review_details') }} <span
                            class="text-danger">*</span></h4>
                    <textarea class="field-control" v-model="props.form.review" v-bind:class="errors.review ? 'invalid' : ''"></textarea>
                    <small class="db-field-alert" v-if="errors.review">
                        {{ errors.review[0] }}
                    </small>
                </div>
                <div>
                    <h4 class="capitalize font-semibold mb-3">{{ $t('label.upload_images') }}</h4>
                    <div class="flex flex-wrap gap-3">
                        <div class="relative" v-for="(image, index) in 5" :key="index">
                            <button v-if="imageUrl[index]" @click="removeImage(index)" type="button"
                                class="lab-fill-close text-xs w-5 h-5 leading-5 rounded-full shadow absolute -top-1 -right-1 text-danger bg-white"></button>
                            <img v-if="imageUrl[index]" :src="imageUrl[index]" alt="product"
                                class="rounded-lg w-20 h-20 object-cover" />
                            <label v-if="!imageUrl[index]"
                                class="relative rounded-lg w-20 h-20 flex flex-col items-center justify-center gap-1 cursor-pointer bg-[#EFF0F6]">
                                <input @change="handleImageUpload($event, index)" type="file"
                                    class="absolute inset-0 -z-10 rounded-lg opacity-0">
                                <i class="lab-fill-image text-xl text-text cursor-pointer"></i>
                                <span class="text-xs font-medium capitalize cursor-pointer text-text">{{
                                    $t('label.add_image') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="field-button w-fit font-semibold tracking-wide">{{ $t('button.submit_review')
        }}</button>
    </form>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
export default {
    name: "ProductReviewComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            activeRate: 1,
            imageUrl: Array(5).fill(null),
            selectFile: null,
            productData: {},
            props: {
                search: {
                },
                form: {
                    star: '',
                    review: '',
                }
            },
            images: {},
            errors: {},
        }
    },
    mounted() {
        this.show();

        if (typeof this.$route.params.id !== "undefined") {
            this.reviewShow();
        }
    },
    computed: {
        product: function () {
            return this.$store.getters["frontendProduct/show"];
        },
    },
    methods: {
        handleImageUpload(event, index) {
            if (typeof this.$route.params.id !== "undefined") {
                try {
                    this.loading.isActive = true;
                    const input = event.target;
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imageUrl[index] = URL.createObjectURL(event.target.files[0]);
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                    const formData = new FormData();
                    formData.append("image", event.target.files[0]);
                    this.$store.dispatch("frontendProductReview/uploadImage", {
                        id: this.$route.params.id,
                        form: formData
                    }).then((res) => {
                        alertService.success(this.$t("message.image_update"));
                        this.loading.isActive = false;
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.errors.image[0]);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            } else {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imageUrl[index] = URL.createObjectURL(event.target.files[0]);
                    };
                    reader.readAsDataURL(input.files[0]);
                    this.images[index] = event.target.files[0];
                }
            }

        },
        removeImage(index) {
            if (typeof this.$route.params.id !== "undefined") {
                appService.destroyConfirmation().then((res) => {
                    try {
                        this.loading.isActive = true;
                        this.imageUrl[index] = null;
                        this.images[index] = null;
                        this.$store.dispatch("frontendProductReview/deleteImage", {
                            id: this.$route.params.id,
                            index: index,
                        }).then((res) => {
                            this.loading.isActive = false;
                            alertService.success(this.$t("message.image_delete"));
                        }).catch((err) => {
                            this.loading.isActive = false;
                            alertService.error(err.response.data.message);
                        });
                    } catch (err) {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.imageUrl[index] = null;
                this.images[index] = null;
            }
        },
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        show: function () {
            if (typeof this.$route.params.slug !== "undefined") {
                this.loading.isActive = true;
                this.props.search.slug = this.$route.params.slug;
                this.$store.dispatch("frontendProduct/showWithTrashed", this.props.search).then((res) => {
                    this.productData = res.data.data;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },
        reviewShow: function () {
            if (typeof this.$route.params.id !== "undefined") {
                this.loading.isActive = true;
                this.$store.dispatch("frontendProductReview/show", this.$route.params.id).then((res) => {
                    this.props.form.star = res.data.data.star;
                    this.activeRate = res.data.data.star;
                    this.props.form.review = res.data.data.review;
                    this.images = res.data.data.images;
                    this.images.forEach((image, index) => {
                        this.imageUrl[index] = image;
                    });
                    this.$store.getters["frontendProductReview/temp"].temp_id = res.data.data.id;
                    this.$store.getters["frontendProductReview/temp"].isEditing = true;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append('star', this.activeRate);
                fd.append('review', this.props.form.review);
                fd.append('product_id', this.productData.id);
                if (this.images) {
                    for (const key in this.images) {
                        if (Object.hasOwnProperty.call(this.images, key)) {
                            if (this.images[key] !== null) {
                                fd.append('images[]', this.images[key]);
                            }
                        }
                    }
                }
                const tempId = this.$store.getters["frontendProductReview/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch('frontendProductReview/save', {
                    form: fd,
                    search: this.search
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        tempId === null ? 0 : 1,
                        this.$t("menu.product_review")
                    );
                    this.$store.dispatch("frontendProductReview/reset").then().catch();
                    this.props.form = {
                        star: '',
                        review: '',
                    };
                    this.images = {};
                    this.errors = {};
                    this.$router.push({ name: "frontend.account.orderHistory" });
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },

    }
}
</script>
