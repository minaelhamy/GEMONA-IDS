import axios from 'axios'

export const productSeo = {
    namespaced: true,
    state: {
        lists: [],
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/product/seo/${payload}`).then((res) => {
                    if(typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('lists', res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                    axios.post(`/admin/product/seo/${payload.productId}/update`, payload.form).then(res => {
                    context.dispatch('lists', payload.productId).then().catch();
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
    },
}
