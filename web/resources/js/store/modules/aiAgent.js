import axios from 'axios'
import appService from "../../services/appService";


export const aiAgent = {
    namespaced: true,
    state: {
        lists: []
    },
    getters: {
        lists: function (state) {
            return state.lists;
        }
    },
    actions: {
        lists: function (context, payload = {}) {
            return new Promise((resolve, reject) => {
                let url = 'admin/setting/ai-agent';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    context.commit('lists', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetch: function (context, payload = {}) {
            return context.dispatch('lists', payload);
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/setting/ai-agent/update`, payload.form).then(res => {
                    context.dispatch('lists', payload.search).then().catch();
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
        }
    },
}

