import axios from "axios";
import appService from "../../../services/appService";

export const frontendProduct = {
    namespaced: true,
    state: {
        show : {},
        showImages: [],
        showReviews: [],
        showVideos: [],
        showSeo: {},
        popularProducts: [],
        popularProductPage: {},
        popularProductPagination: [],
        flashSaleProducts: [],
        flashSaleProductPage: {},
        flashSaleProductPagination: [],
        categoryWiseProducts: [],
        categoryWiseBands: [],
        categoryWiseVariations: [],
        categoryWiseProductPage: {},
        categoryWiseProductPagination: {},
        offerProducts: [],
        offerProductPage: {},
        offerProductPagination: [],
        relatedProducts: [],
        relatedProductPage: {},
        relatedProductPagination: [],
        wishlistProducts: [],
        wishlistProductPage: {},
        wishlistProductPagination: [],
    },
    getters: {
        show: function (state) {
            return state.show;
        },
        showImages: function (state) {
            return state.showImages;
        },
        showReviews: function (state) {
            return state.showReviews;
        },
        showVideos: function (state) {
            return state.showVideos;
        },
        showSeo: function (state) {
            return state.showSeo;
        },
        popularProducts: function (state) {
            return state.popularProducts;
        },
        popularProductPage: function (state) {
            return state.popularProductPage;
        },
        popularProductPagination: function (state) {
            return state.popularProductPagination;
        },
        flashSaleProducts: function (state) {
            return state.flashSaleProducts;
        },
        flashSaleProductPage: function (state) {
            return state.flashSaleProductPage;
        },
        flashSaleProductPagination: function (state) {
            return state.flashSaleProductPagination;
        },
        categoryWiseProducts: function (state) {
            return state.categoryWiseProducts;
        },
        categoryWiseBands: function (state) {
            return state.categoryWiseBands;
        },
        categoryWiseVariations: function (state) {
            return state.categoryWiseVariations;
        },
        categoryWiseProductPage: function (state) {
            return state.categoryWiseProductPage;
        },
        categoryWiseProductPagination: function (state) {
            return state.categoryWiseProductPagination;
        },
        offerProducts: function (state) {
            return state.offerProducts;
        },
        offerProductPage: function (state) {
            return state.offerProductPage;
        },
        offerProductPagination: function (state) {
            return state.offerProductPagination;
        },
        relatedProducts: function (state) {
            return state.relatedProducts;
        },
        relatedProductPage: function (state) {
            return state.relatedProductPage;
        },
        relatedProductPagination: function (state) {
            return state.relatedProductPagination;
        },
        wishlistProducts: function (state) {
            return state.wishlistProducts;
        },
        wishlistProductPage: function (state) {
            return state.wishlistProductPage;
        },
        wishlistProductPagination: function (state) {
            return state.wishlistProductPagination;
        },
    },
    actions: {
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/show/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    context.commit("show", res.data.data);
                    context.commit("showImages", res.data.data.images);
                    context.commit("showReviews", res.data.data.reviews);
                    context.commit("showVideos", res.data.data.videos);``
                    context.commit("showSeo", res.data.data.seo);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        showWithTrashed: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/show-with-trashed/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        categoryWiseProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/category-wise-products`;
                axios.post(url, payload).then((res) => {
                    context.commit("categoryWiseProducts", res.data.data);
                    context.commit("categoryWiseProductPage", res.data.data);
                    context.commit("categoryWiseProductPagination", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        popularProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/popular-products`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    context.commit("popularProducts", res.data.data);
                    context.commit("popularProductPage", res.data.meta);
                    context.commit("popularProductPagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        flashSaleProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/flash-sale-products`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    context.commit("flashSaleProducts", res.data.data);
                    context.commit("flashSaleProductPage", res.data.meta);
                    context.commit("flashSaleProductPagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        offerProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/offer-products`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    context.commit("offerProducts", res.data.data);
                    context.commit("offerProductPage", res.data.meta);
                    context.commit("offerProductPagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        relatedProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/related-products/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    context.commit("relatedProducts", res.data.data);
                    context.commit("relatedProductPage", res.data.meta);
                    context.commit("relatedProductPagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        wishlistProducts: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/wishlist-products`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, payload).then((res) => {
                    context.commit("wishlistProducts", res.data.data);
                    context.commit("wishlistProductPage", res.data.meta);
                    context.commit("wishlistProductPagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    },
    mutations: {
        show: function (state, payload) {
            state.show = payload;
        },
        showImages: function (state, payload) {
            state.showImages = payload;
        },
        showReviews: function(state, payload) {
            state.showReviews = payload;
        },
        showVideos:function (state, payload) {
            state.showVideos = payload;
        },
        showSeo:function (state, payload) {
            state.showSeo = payload;
        },
        popularProducts: function (state, payload) {
            state.popularProducts = payload;
        },
        popularProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.popularProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        popularProductPagination: function (state, payload) {
            state.popularProductPagination = payload;
        },
        flashSaleProducts: function (state, payload) {
            state.flashSaleProducts = payload;
        },
        flashSaleProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.flashSaleProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        flashSaleProductPagination: function (state, payload) {
            state.flashSaleProductPagination = payload;
        },
        categoryWiseProducts: function (state, payload) {
            state.categoryWiseProducts   = payload.products;
            state.categoryWiseBands      = payload.brands;
            state.categoryWiseVariations = payload.variations;
        },
        categoryWiseProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.categoryWiseProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        categoryWiseProductPagination: function (state, payload) {
            state.categoryWiseProductPagination = {
                data: payload.products,
                links: {
                    first: payload.first_page_url,
                    last: payload.last_page_url,
                    next: payload.next_page_url,
                    prev: payload.prev_page_url
                },
                meta: {
                    current_page: payload.current_page,
                    from: payload.from,
                    last_page: payload.last_page,
                    links: payload.links,
                    path: payload.path,
                    per_page: payload.per_page,
                    to: payload.to,
                    total: payload.total
                }
            }
        },
        offerProducts: function (state, payload) {
            state.offerProducts = payload;
        },
        offerProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.offerProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        offerProductPagination: function (state, payload) {
            state.offerProductPagination = payload;
        },
        relatedProducts: function (state, payload) {
            state.relatedProducts = payload;
        },
        relatedProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.relatedProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        relatedProductPagination: function (state, payload) {
            state.relatedProductPagination = payload;
        },
        wishlistProducts: function (state, payload) {
            state.wishlistProducts = payload;
        },
        wishlistProductPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.wishlistProductPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        wishlistProductPagination: function (state, payload) {
            state.wishlistProductPagination = payload;
        },
    },
};
