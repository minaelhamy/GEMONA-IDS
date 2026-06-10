import axios from "axios";
import appService from "../../../services/appService";

export const frontendProductSection = {
    namespaced: true,
    state: {
        lists: [],
        show: {},
        products: [],
        productPage: {},
        productPagination: []
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        show: function (state) {
            return state.show;
        },
        products: function (state) {
            return state.products;
        },
        productPage: function (state) {
            return state.productPage;
        },
        productPagination: function (state) {
            return state.productPagination;
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/product-section";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    context.commit("lists", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/product-section/show/${payload}`).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        products: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product-section/products/${payload.slug}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if(typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit("products", res.data.data);
                        context.commit("productPage", res.data.meta);
                        context.commit("productPagination", res.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        show: function (state, payload) {
            state.show = payload;
        },
        products: function (state, payload) {
            state.products = payload;
        },
        productPagination: function (state, payload) {
            state.productPagination = payload;
        },
        productPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.productPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
    },
};
