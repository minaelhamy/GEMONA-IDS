import axios from 'axios'
import appService from "../../services/appService";


export const productVariation = {
    namespaced: true,
    state: {
        tree: [],
        singleTree: [],
        treeWithSelected: [],
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_date: [],
            temp_id: null,
            isEditing: false,
        },
        initialVariation: [],
        childrenVariation: [],
        ancestorsToString: "",
        ancestorsAndSelfId: []
    },
    getters: {
        tree: function (state) {
            return state.tree;
        },
        singleTree: function (state) {
            return state.singleTree;
        },
        treeWithSelected: function (state) {
            return state.treeWithSelected;
        },
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
        temp: function (state) {
            return state.temp;
        },
        initialVariation: function (state) {
            return state.initialVariation;
        },
        childrenVariation: function (state) {
            return state.childrenVariation;
        },
        ancestorsToString: function (state) {
            return state.ancestorsToString;
        },
        ancestorsAndSelfId: function (state) {
            return state.ancestorsAndSelfId
        }
    },
    actions: {
        tree: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/variation/${payload.productId}/tree`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('tree', res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        singleTree: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/variation/${payload}/single-tree`;
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('singleTree', res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        treeWithSelected: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/variation/${payload.productId}/tree-with-selected`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('treeWithSelected', res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/variation/${payload.productId}`;
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
                let url = `/admin/product/variation/${payload.productId}/store`;
                if (this.state['productVariation'].temp.isEditing) {
                    method = axios.put;
                    url = `/admin/product/variation/${payload.productId}/update/${this.state['productVariation'].temp.temp_id}`;
                }
                method(url, payload.form).then(res => {
                    context.dispatch('singleTree', payload.productId).then().catch();
                    context.dispatch('tree', { productId: payload.productId }).then().catch();
                    context.dispatch('lists', { productId: payload.productId }).then().catch();
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/variation/${payload.productId}/tree`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('temp', { payload: payload, data: res.data.data });
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });

        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/product/variation/${payload.productId}/destroy/${payload.productVariationId}`).then((res) => {
                    context.dispatch('singleTree', payload.productId).then().catch();
                    context.dispatch('tree', { productId: payload.productId }).then().catch();
                    context.dispatch('lists', { productId: payload.productId }).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/product/variation/${payload.productId}/show/${payload.productVariationId}`).then((res) => {
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
        initialVariation: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/initial-variation/${payload}`;
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
                let url = `admin/product/children-variation/${payload}`;
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
                let url = `admin/product/variation/ancestors-and-self/${payload}`;
                axios.get(url).then((res) => {
                    context.commit("ancestorsToString", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        ancestorsAndSelfId: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/product/ancestors-and-self-id/${payload}`;
                axios.get(url).then((res) => {
                    context.commit("ancestorsAndSelfId", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        downloadBarcode: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/product/variation/download-barcode/' + payload;
                axios.get(url, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        tree: function (state, payload) {
            state.tree = payload;
        },
        singleTree: function (state, payload) {
            state.singleTree = payload;
        },
        treeWithSelected: function (state, payload) {
            state.treeWithSelected = payload;
        },
        lists: function (state, payload) {
            state.lists = payload;
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
        temp: function (state, payload) {
            state.temp.temp_date = payload.data;
            state.temp.temp_id = payload.payload.id;
            state.temp.isEditing = true;
        },
        reset: function (state) {
            state.temp.temp_data = [];
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
        initialVariation: function (state, payload) {
            state.initialVariation = payload;
        },
        childrenVariation: function (state, payload) {
            state.childrenVariation = payload;
        },
        ancestorsToString: function (state, payload) {
            state.ancestorsToString = payload;
        },
        ancestorsAndSelfId: function (state, payload) {
            state.ancestorsAndSelfId = payload;
        }
    },
}
