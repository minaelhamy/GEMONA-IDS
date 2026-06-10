import axios from 'axios'
import appService from "../../../services/appService";


export const frontendOrder = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        orderProducts: {},
        orderUser: {},
        orderAddress: {},
        outletAddress: {}
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination;
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        orderProducts: function (state) {
            return state.orderProducts;
        },
        orderUser: function (state) {
            return state.orderUser;
        },
        orderAddress: function (state) {
            return state.orderAddress;
        },
        outletAddress: function (state) {
            return state.outletAddress;
        },
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/order`;
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit("lists", res.data.data);
                        if(typeof payload.search.paginate !== 'undefined' && payload.search.paginate === 1) {
                            context.commit("pagination", res.data);
                            context.commit("page", res.data.meta);
                        }
                    }

                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post("/frontend/order", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/order/show/${payload}`).then((res) => {
                    context.commit("show", res.data.data);
                    context.commit("orderProducts", res.data.data.order_products);
                    context.commit("orderUser", res.data.data.user);
                    context.commit("orderAddress", res.data.data.order_address);
                    context.commit("outletAddress", res.data.data.outlet_address);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeStatus: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`frontend/order/change-status/${payload.id}`,payload).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload
        },
        pagination: function (state, payload) {
            state.pagination = payload;
        },
        page: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.page = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        show: function (state, payload) {
            state.show = payload;
        },
        orderProducts: function (state, payload) {
            state.orderProducts = payload;
        },
        orderUser: function (state, payload) {
            state.orderUser = payload;
        },
        orderAddress: function (state, payload) {
            state.orderAddress = payload;
        },
        outletAddress: function (state, payload) {
            state.outletAddress = payload;
        },
    },
}
