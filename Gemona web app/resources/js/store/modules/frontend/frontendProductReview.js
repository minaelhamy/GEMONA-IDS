import axios from "axios";

export const frontendProductReview = {
    namespaced: true,
    state: {
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
    },
    getters: {
        show: function (state) {
            return state.show;
        },
        temp: function (state) {
            return state.temp;
        },
    },
    actions: {
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = "/frontend/product-review";
                if (this.state["frontendProductReview"].temp.isEditing) {
                    method = axios.post;
                    url = `/frontend/product-review/${this.state["frontendProductReview"].temp.temp_id}`;
                }
                method(url, payload.form).then((res) => {
                    context.commit("reset");
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function (context, payload) {
            context.commit("temp", payload);
        },
        reset: function (context) {
            context.commit("reset");
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/product-review/show/${payload}`).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        uploadImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`frontend/product-review/upload-image/${payload.id}`, payload.form, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        deleteImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`frontend/product-review/delete-image/${payload.id}/${payload.index}`).then(res => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        show: function (state, payload) {
            state.show = payload;
        },
        temp: function (state, payload) {
            state.temp.temp_id = payload;
            state.temp.isEditing = true;
        },
        reset: function (state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
    },
};
