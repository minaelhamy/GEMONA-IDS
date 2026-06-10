import axios from 'axios'
import appService from '../../services/appService';


export const purchase = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        show: {},
        viewPayment: {},
        edit: {},
        pagination: [],
        temp: {
            temp_id: null,
            isEditing: false,
        },
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
        viewPayment: function (state) {
            return state.viewPayment;
        },
        edit: function (state) {
            return state.edit;
        },
        temp: function (state) {
            return state.temp;
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/purchase';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('lists', res.data.data);
                        context.commit('page', res.data.meta);
                        context.commit('pagination', res.data)
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = 'admin/purchase';
                if (this.state['purchase'].temp.isEditing) {
                    method = axios.post;
                    url = `admin/purchase/update/${this.state['purchase'].temp.temp_id}`;
                }

                method(url, payload.form).then(res => {
                    context.dispatch('lists', {vuex: true}).then().catch();
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            })
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/purchase/show/${payload}`).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        edit: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/purchase/edit/${payload}`).then((res) => {
                    context.commit('edit', res.data.data);
                    context.commit('temp', payload);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/purchase/${payload.id}`).then((res) => {
                    context.dispatch("lists", payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        export: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/purchase/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        download: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/purchase/download-attachment/' + payload;
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        payment: function (context, payload) {
            context.commit("temp", payload);
        },
        addPayment: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = `admin/purchase/payment/${this.state['purchase'].temp.temp_id}`;

                method(url, payload.form).then(res => {
                    context.dispatch('lists', payload.search).then().catch();
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            })
        },
        viewPayment: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/purchase/payment/${this.state['purchase'].temp.temp_id}`).then((res) => {
                    context.commit('viewPayment', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                })
            })
        },
        paymentDestroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/purchase/payment/${payload.purchase_id}/${payload.id}`).then((res) => {
                    context.dispatch("lists", payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        paymentDownload: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/purchase/payment/download-attachment/' + payload;
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function (context) {
            context.commit('reset');
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload
        },
        pagination: function (state, payload) {
            state.pagination = payload
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
        viewPayment: function (state, payload) {
            state.viewPayment = payload;
        },
        edit: function (state, payload) {
            state.edit = payload;
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
}
