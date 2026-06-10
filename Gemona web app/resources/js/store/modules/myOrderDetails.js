import axios from "axios";
import appService from "../../services/appService";

export const myOrderDetails = {
    namespaced: true,
    state: {
        orderDetails: {},
        orderProducts: {},
        orderUser: {},
        orderAddresses: {},
        outletAddress: {},
    },
    getters: {
        orderDetails: function (state) {
            return state.orderDetails;
        },
        orderProducts: function (state) {
            return state.orderProducts;
        },
        orderUser: function (state) {
            return state.orderUser;
        },
        orderAddresses: function (state) {
            return state.orderAddresses;
        },
        outletAddress: function (state) {
            return state.outletAddress;
        },
    },
    actions: {
        orderDetails: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/my-order/show/${payload.id}/${payload.orderId}`).then((res) => {
                    context.commit("orderDetails", res.data.data);
                    context.commit("orderProducts", res.data.data.order_products);
                    context.commit("orderUser", res.data.data.user);
                    context.commit("orderAddresses", res.data.data.order_address);
                    context.commit("outletAddress", res.data.data.outlet_address);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        orderDetails: function (state, payload) {
            state.orderDetails = payload;
        },
        orderProducts: function (state, payload) {
            state.orderProducts = payload;
        },
        orderUser: function (state, payload) {
            state.orderUser = payload;
        },
        orderAddresses: function (state, payload) {
            state.orderAddresses = payload;
        },
        outletAddress: function (state, payload) {
            state.outletAddress = payload;
        },
    },
};
