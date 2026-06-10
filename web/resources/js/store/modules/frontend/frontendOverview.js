import axios from "axios";

export const frontendOverview = {
    namespaced: true,
    state: {
        totalOrders: [],
        totalCompletedOrders: [],
        totalReturnedOrders: [],
        walletBalance: [],
        
    },

    getters: {
        totalOrders: function (state) {
            return state.totalOrders;
        },
    },

    actions: {
        totalOrders: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("frontend/overview/total-orders")
                    .then((res) => {
                        context.commit("totalOrders", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        totalCompletedOrders: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("frontend/overview/total-complete-orders")
                    .then((res) => {
                        context.commit("totalCompletedOrders", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        totalReturnedOrders: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("frontend/overview/total-return-orders")
                    .then((res) => {
                        context.commit("totalReturnedOrders", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        walletBalance: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .get("frontend/overview/wallet-balance")
                    .then((res) => {
                        context.commit("walletBalance", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
    },

    mutations: {
        totalOrders: function (state, payload) {
            state.totalOrders = payload;
        },
        totalCompletedOrders: function (state, payload) {
            state.totalCompletedOrders = payload;
        },
        totalReturnedOrders: function (state, payload) {
            state.totalReturnedOrders = payload;
        },
        walletBalance: function (state, payload) {
            state.walletBalance = payload;
        },
    },
};
