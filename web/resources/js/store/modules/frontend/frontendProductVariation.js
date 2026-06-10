import axios from "axios";

export const frontendProductVariation = {
    namespaced: true,
    state: {
        initialVariation: [],
        childrenVariation: [],
        ancestorsToString: "",
    },
    getters: {
        initialVariation: function (state) {
            return state.initialVariation;
        },
        childrenVariation: function (state) {
            return state.childrenVariation;
        },
        ancestorsToString: function (state) {
            return state.ancestorsToString;
        }
    },
    actions: {
        initialVariation: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/initial-variation/${payload}`;
                axios.get(url).then((res) => {
                    context.commit("initialVariation", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        childrenVariation: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/children-variation/${payload}`;
                axios.get(url).then((res) => {
                    context.commit("childrenVariation", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        ancestorsToString: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `frontend/product/variation/ancestors-and-self/${payload}`;
                axios.get(url).then((res) => {
                    context.commit("ancestorsToString", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    },
    mutations: {
        initialVariation: function (state, payload) {
            state.initialVariation = payload;
        },
        childrenVariation: function (state, payload) {
            state.childrenVariation = payload;
        },
        ancestorsToString: function (state, payload) {
            state.ancestorsToString = payload;
        }
    },
};
