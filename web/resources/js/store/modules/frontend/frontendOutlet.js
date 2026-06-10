import axios from "axios";
import appService from "../../../services/appService";

export const frontendOutlet = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: []
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
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "frontend/outlet";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    context.commit("lists", res.data.data);
                    context.commit("page", res.data.meta);
                    context.commit("pagination", res.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    },
    mutations: {
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
                    total: payload.total,
                };
            }
        }
    },
};
