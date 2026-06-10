import axios from 'axios'
import appService from "../../services/appService";


export const product = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        barcodeProduct: {},
        getSku: [],
        temp: {
            temp_id: null,
            isEditing: false,
        },
        purchasableList: [],
        simpleList: []
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        barcodeProduct: function (state) {
            return state.barcodeProduct;
        },
        getSku: function (state) {
            return state.getSku;
        },
        temp: function (state) {
            return state.temp;
        },
        purchasableList: function (state) {
            return state.purchasableList;
        },
        simpleList: function (state) {
            return state.simpleList;
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/product';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('lists', res.data.data);
                        context.commit('page', res.data.meta);
                        context.commit('pagination', res.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = '/admin/product';
                if (this.state['product'].temp.isEditing) {
                    method = axios.post;
                    url = `/admin/product/${this.state['product'].temp.temp_id}`;
                }
                method(url, payload.form).then(res => {
                    context.dispatch('lists', payload.search).then().catch();
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function (context, payload) {
            context.commit('temp', payload);
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/product/${payload.id}`).then((res) => {
                    context.dispatch('lists', payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/product/show/${payload}`).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `/admin/product/change-image/${payload.id}`,
                        payload.form,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        }
                    )
                    .then((res) => {
                        context.commit("show", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        uploadImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/product/upload-image/${payload.id}`, payload.form, {
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
                axios.get(`/admin/product/delete-image/${payload.id}/${payload.index}`).then(res => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function (context) {
            context.commit('reset');
        },
        export: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/product/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        import: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post('admin/product/import/file', payload.form).then((res) => {
                    context.dispatch("lists", payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        download: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/product/download-attachment';
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },

        getSku: function (context) {
            return new Promise((resolve, reject) => {
                axios.get('admin/product/generate-sku').then((res) => {
                    context.commit('getSku', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },

        shippingAndReturn: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/product/shipping-and-return/${payload.id}`, payload.form).then(res => {
                    context.commit("show", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        productOffer: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/product/offer/${payload.id}`, payload.form).then(res => {
                    context.commit("show", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        getPurchasableProduct: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get('admin/product/purchasable-product')
                    .then((res) => {
                        context.commit('purchasableList', res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    })
            })
        },
        getSimpleProduct: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get('admin/product/simple-product')
                    .then((res) => {
                        context.commit('simpleList', res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    })
            })
        },
        downloadBarcode: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/product/download-barcode/' + payload;
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        barcodeProduct: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/product/barcode-product/${payload}`).then((res) => {
                    context.commit('barcodeProduct', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        clearOffer: function ( context, payload){
            return new Promise((resolve, reject) => {
                axios.post(`admin/product/clear-offer/${payload}`).then((res) => {
                    context.commit("show", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
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
                    total: payload.total
                }
            }
        },
        show: function (state, payload) {
            state.show = payload;
        },
        barcodeProduct: function (state, payload) {
            state.barcodeProduct = payload;
        },
        temp: function (state, payload) {
            state.temp.temp_id = payload;
            state.temp.isEditing = true;
        },
        reset: function (state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
        getSku: function (state, payload) {
            state.getSku = payload;
        },
        purchasableList: function (state, payload) {
            state.purchasableList = payload;
        },
        simpleList: function (state, payload) {
            state.simpleList = payload;
        }
    },
}
