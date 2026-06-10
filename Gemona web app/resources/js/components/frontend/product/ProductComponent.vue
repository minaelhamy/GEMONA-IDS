<template>
    <LoadingComponent :props="loading" />
    <section class="mb-10 sm:mb-20">
        <div class="container">
            <CategoryBreadcrumbComponent
                v-if="typeof $route.query.category !== 'undefined' && $route.query.category !== ''"
                :categories="ancestorsAndSelfCategories" />

            <div class="flex items-center justify-between gap-5 mb-6 max-md:mb-8">
                <div class="flex flex-wrap items-end gap-3 max-md:flex-col max-md:items-start max-md:gap-1.5">
                    <h3 class="text-3xl font-bold capitalize max-sm:text-lg">
                        {{ $t('label.explore_all_products') }}
                    </h3>
                    <span class="text-xl font-medium capitalize max-sm:text-sm">
                        ({{
                            pagination.meta ? pagination.meta.total : 0
                        }} {{
                            categoryWiseProducts.length > 1 ? $t('label.products_found') : $t('label.product_found')
                        }})
                    </span>
                </div>
                <button @click.prevent="showTarget('filter-canvas', 'canvas-active')" type="button"
                    class="lab-line-filter md:invisible flex-shrink-0 text-2xl w-9 h-9 leading-9 text-center rounded-full border border-primary text-primary md:hidden"></button>
            </div>

            <div class="flex items-start border-t border-gray-100 max-md:border-none">
                <div id="filter-canvas"
                    class="max-md:fixed max-md:inset-0 max-md:z-30 max-md:bg-black/50 max-md:transition-all max-md:duration-500 max-md:opacity-0 max-md:invisible">
                    <div
                        class="w-[270px] ltr:md:border-r rtl:md:border-l md:border-gray-100 bg-white max-md:w-full max-md:max-w-xs max-md:transition-all max-md:duration-500 max-md:-translate-x-full">
                        <div class="max-md:h-dvh max-md:overflow-y-auto">
                            <div
                                class="md:hidden flex items-center justify-between py-5 px-4 border-b border-slate-100">
                                <h3 class="text-[22px] font-bold capitalize">{{ $t('label.filter_and_sorting') }}</h3>
                                <button @click.prevent="hideTarget('filter-canvas', 'canvas-active')" type="button"
                                    class="lab-line-circle-cross text-lg text-[#E93C3C]"></button>
                            </div>

                            <div class="filter-group border-b border-gray-100">
                                <button :key="'eventSortBy'" @click.prevent="colspanHideShow($event, 'sortBy')"
                                    type="button"
                                    class="filter-btn active group flex items-center justify-between w-full py-5 px-4 ltr:md:pl-0 rtl:pr-0">
                                    <span
                                        class="text-lg font-semibold capitalize transition-all duration-500 group-hover:text-primary">{{
                                            $t('label.sort_by')
                                        }}</span>
                                    <i
                                        class="lab-line-chevron-up text-base font-semibold transition-all duration-500 group-hover:text-primary"></i>
                                </button>
                                <div :key="'eventSortById'" id="sortBy"
                                    class="h-auto overflow-hidden transition-all duration-500">
                                    <div class="w-full mb-6 flex flex-col gap-4 px-6 md:px-0">
                                        <label for="sortByNewest" class="flex items-center gap-3 cursor-pointer group">
                                            <input @click="sortByOption($event, 'newest')" v-model="productSortBy"
                                                value="latest" type="radio" id="sortByNewest" class="cs-custom-radio">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ $t('label.newest') }}
                                            </span>
                                        </label>

                                        <label for="priceLowToHigh"
                                            class="flex items-center gap-3 cursor-pointer group">
                                            <input @click="sortByOption($event, 'price_low_to_high')"
                                                v-model="productSortBy" value="price_low_to_high" type="radio"
                                                id="priceLowToHigh" class="cs-custom-radio">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ $t('label.price_low_to_high') }}
                                            </span>
                                        </label>

                                        <label for="priceHighToLow"
                                            class="flex items-center gap-3 cursor-pointer group">
                                            <input @click="sortByOption($event, 'price_high_to_low')"
                                                v-model="productSortBy" value="price_high_to_low" type="radio"
                                                id="priceHighToLow" class="cs-custom-radio">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ $t('label.price_high_to_low') }}
                                            </span>
                                        </label>

                                        <label for="topRated" class="flex items-center gap-3 cursor-pointer group">
                                            <input @click="sortByOption($event, 'top_rated')" v-model="productSortBy"
                                                value="top_rated" type="radio" id="topRated" class="cs-custom-radio">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ $t('label.top_rated') }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-group border-b border-gray-100">
                                <button :key="'eventFilterPrice'"
                                    @click.prevent="colspanHideShow($event, 'filterPrice')" type="button"
                                    class="filter-btn active group flex items-center justify-between w-full py-5 px-4 ltr:md:pl-0 rtl:pr-0">
                                    <span
                                        class="text-lg font-semibold capitalize transition-all duration-500 group-hover:text-primary">{{
                                            $t('label.price')
                                        }}</span>
                                    <i
                                        class="lab-line-chevron-up text-base font-semibold transition-all duration-500 group-hover:text-primary"></i>
                                </button>
                                <div :key="'eventFilterPriceId'" id="filterPrice"
                                    class="h-auto overflow-hidden transition-all duration-500">
                                    <div class="w-full mb-6 flex flex-col gap-4 px-6 md:px-0">
                                        <div class="form-row sm:p-1">
                                            <div class="form-col-12 sm:form-col-6">
                                                <input @keyup="priceOptionUpdate" v-on:keypress="onlyNumber($event)"
                                                    class="db-field-control" type="number"
                                                    v-model="productPrice.range[0]">
                                            </div>
                                            <div class="form-col-12 sm:form-col-6">
                                                <input @keyup="priceOptionUpdate" v-on:keypress="onlyNumber($event)"
                                                    class="db-field-control" type="number"
                                                    v-model="productPrice.range[1]">
                                            </div>
                                        </div>
                                        <VueSimpleRangeSlider @mouseup="priceOptionRange" @touchend="priceOptionRange"
                                            :keepJustSignificantFigures="true" popover-content-editable="false"
                                            significant-figures="1" active-bar-color="#F23E14" bar-color="#D9DBE9"
                                            class="p-1 w-full" :min="0" :max="maxRange" v-model="productPrice.range" />
                                    </div>
                                </div>
                            </div>

                            <div v-if="categoryWiseBands.length > 0" class="filter-group border-b border-gray-100">
                                <button :key="'eventBrand'" @click.prevent="colspanHideShow($event, 'brand')"
                                    type="button"
                                    class="filter-btn active group flex items-center justify-between w-full py-5 px-4 ltr:md:pl-0 rtl:pr-0">
                                    <span
                                        class="text-lg font-semibold capitalize transition-all duration-500 group-hover:text-primary">
                                        {{ $t('label.brand') }}
                                    </span>
                                    <i
                                        class="lab-line-chevron-up text-base font-semibold transition-all duration-500 group-hover:text-primary"></i>
                                </button>

                                <div :key="'eventBrandId'" id="brand"
                                    class="h-auto overflow-hidden transition-all duration-500">
                                    <div class="w-full mb-6 flex flex-col gap-4 px-6 md:px-0">
                                        <label :for="'brand_' + categoryWiseBand.id"
                                            v-for="categoryWiseBand in categoryWiseBands"
                                            class="flex items-center gap-3 cursor-pointer group">
                                            <input @click="brandOption($event, categoryWiseBand.id)" type="checkbox"
                                                :id="'brand_' + categoryWiseBand.id" class="cs-custom-checkbox">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ categoryWiseBand.name }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div v-if="Object.keys(categoryWiseVariations).length > 0"
                                v-for="(categoryWiseVariation, categoryWiseVariationKey) in categoryWiseVariations"
                                class="filter-group border-b border-gray-100">
                                <button :key="'variation_' + categoryWiseVariationKey"
                                    @click.prevent="colspanHideShow($event, 'variation_' + categoryWiseVariationKey)"
                                    type="button"
                                    class="filter-btn active group flex items-center justify-between w-full py-5 px-4 ltr:md:pl-0 rtl:pr-0">
                                    <span
                                        class="text-lg font-semibold capitalize transition-all duration-500 group-hover:text-primary">
                                        {{ underscoreToSpace(categoryWiseVariationKey) }}
                                    </span>
                                    <i
                                        class="lab-line-chevron-up text-base font-semibold transition-all duration-500 group-hover:text-primary"></i>
                                </button>

                                <div :key="'variation_' + categoryWiseVariationKey + 'Id'"
                                    :id="'variation_' + categoryWiseVariationKey"
                                    class="h-auto overflow-hidden transition-all duration-500">
                                    <div class="w-full mb-6 flex flex-col gap-4 px-6 md:px-0">
                                        <label
                                            :for="variation.product_attribute_id + '_' + variation.product_attribute_option_id"
                                            v-for="variation in categoryWiseVariation"
                                            class="flex items-center gap-3 cursor-pointer group">
                                            <input type="checkbox"
                                                @click="variationOption($event, variation.product_attribute_id, variation.product_attribute_option_id)"
                                                :id="variation.product_attribute_id + '_' + variation.product_attribute_option_id"
                                                class="cs-custom-checkbox">
                                            <span
                                                class="font-medium capitalize transition-all duration-500 group-hover:text-primary">
                                                {{ variation.attribute_option_name }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full p-4 max-md:p-0">
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 mb-12 componentLoading">
                        <LoadingContentComponent :props="loadingContent" />
                        <ProductListComponent v-if="categoryWiseProducts.length > 0" :products="categoryWiseProducts" />
                    </div>

                    <PaginationComponent @pagination-change-page="products" :data="pagination" :limit="1"
                        :keep-length="false" />
                </div>
            </div>
        </div>
    </section>
</template>


<script>

import targetService from "../../../services/targetService";
import ProductListComponent from "../components/ProductListComponent";
import StatusEnum from "../../../enums/modules/statusEnum";
import appService from "../../../services/appService";
import VueSimpleRangeSlider from "vue-simple-range-slider";
import "vue-simple-range-slider/css";
import LoadingComponent from "../components/LoadingComponent";
import PaginationComponent from "../components/PaginationComponent";
import LoadingContentComponent from "../components/LoadingContentComponent.vue";
import CategoryBreadcrumbComponent from "../components/CategoryBreadcrumbComponent.vue";

export default {
    name: "ProductComponent",
    components: {
        CategoryBreadcrumbComponent,
        LoadingContentComponent,
        LoadingComponent,
        ProductListComponent,
        VueSimpleRangeSlider,
        PaginationComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            loadingContent: {
                isActive: false
            },
            productSortBy: null,
            productBrands: [],
            productVariations: [],
            productPrice: {
                range: [0, 0]
            },
            maxRange: 0,
            productSearchForm: {
                page: 1,
                status: StatusEnum.ACTIVE,
                sort_by: null,
                category: null,
                name: null,
                brand: [],
                variation: [],
                min_price: null,
                max_price: null
            }
        }
    },
    computed: {
        pagination: function () {
            return this.$store.getters["frontendProduct/categoryWiseProductPagination"];
        },
        ancestorsAndSelfCategories: function () {
            return this.$store.getters["frontendProductCategory/ancestorsAndSelf"];
        },
        categoryWiseProducts: function () {
            return this.$store.getters["frontendProduct/categoryWiseProducts"];
        },
        categoryWiseBands: function () {
            return this.$store.getters["frontendProduct/categoryWiseBands"];
        },
        categoryWiseVariations: function () {
            return this.$store.getters["frontendProduct/categoryWiseVariations"];
        }
    },
    mounted() {
        this.ancestorsAndSelf();
    },
    methods: {
        onlyNumber: function (e) {
            return appService.onlyNumber(e);
        },
        underscoreToSpace: function (s) {
            return appService.underscoreToSpace(s);
        },
        colspanHideShow: function (event, id) {
            targetService.colspanHideShow(event, id);
        },
        showTarget: function (id, cClass) {
            targetService.showTarget(id, cClass);
        },
        hideTarget: function (id, cClass) {
            targetService.hideTarget(id, cClass);
        },
        ancestorsAndSelf: function () {
            if (typeof this.$route.query.category !== "undefined" && this.$route.query.category !== "") {
                this.loading.isActive = true;
                this.productSearchForm.category = this.$route.query.category;
                this.$store.dispatch("frontendProductCategory/ancestorsAndSelf", this.$route.query.category).then(res => {
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.productSearchForm.category = null;
            }

            if (typeof this.$route.query.name !== "undefined" && this.$route.query.name !== "") {
                this.productSearchForm.name = this.$route.query.name;
            } else {
                this.productSearchForm.name = null;
            }

            if (typeof this.$route.query.brand !== "undefined" && this.$route.query.brand !== "") {
                this.productSearchForm.brand = JSON.stringify([this.$route.query.brand]);
            } else {
                this.productSearchForm.brand = [];
            }

            this.loading.isActive = true;
            this.$store.dispatch("frontendProduct/categoryWiseProducts", this.productSearchForm).then(res => {
                this.productPrice.range = [0, res.data.data.max_price];
                this.maxRange = res.data.data.max_price;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        async products(page = 1) {
            this.loadingContent.isActive = true;
            this.productSearchForm.page = page;
            await this.$store.dispatch("frontendProduct/categoryWiseProducts", this.productSearchForm).then(res => {
                this.loadingContent.isActive = false;
            }).catch((err) => {
                this.loadingContent.isActive = false;
            });
        },
        sortByOption: function (event, sortBy) {
            this.productSearchForm.sort_by = sortBy;
            this.products();
        },
        priceOptionRange() {
            this.productSearchForm.min_price = this.productPrice.range[0];
            this.productSearchForm.max_price = this.productPrice.range[1];
            this.products();
        },
        priceOptionUpdate() {
            this.productPrice.range = [this.productPrice.range[0], this.productPrice.range[1]];
            this.productSearchForm.min_price = this.productPrice.range[0];
            this.productSearchForm.max_price = this.productPrice.range[1];
            this.products();
        },
        brandOption: function (event, brand) {
            if (event.target.checked) {
                this.productBrands.push(brand);
            } else {
                for (let i = 0; i < this.productBrands.length; i++) {
                    if (this.productBrands[i] === brand) {
                        this.productBrands.splice(i, 1);
                    }
                }
            }
            this.productSearchForm.brand = JSON.stringify(this.productBrands);
            this.products();
        },
        variationOption: function (event, attribute, option) {
            if (event.target.checked) {
                this.productVariations.push({
                    attribute: attribute,
                    option: option
                });
            } else {
                for (let i = 0; i < this.productVariations.length; i++) {
                    if (this.productVariations[i].attribute === attribute && this.productVariations[i].option === option) {
                        this.productVariations.splice(i, 1);
                    }
                }
            }
            this.productSearchForm.variation = JSON.stringify(this.productVariations);
            this.products();
        },
    },
    watch: {
        $route() {
            this.ancestorsAndSelf();
        }
    }
}
</script>