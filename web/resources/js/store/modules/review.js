import axios from "axios";
import appService from "../../services/appService";

export const review = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        show: {},
        pagination: [],
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
        },
        show: function (state) {
            return state.show;
        },
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/reviews";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios
                    .get(url)
                    .then((res) => {
                        if (
                            typeof payload.vuex === "undefined" ||
                            payload.vuex === true
                        ) {
                            context.commit("lists", res.data.data);
                            context.commit("page", res.data.meta);
                            context.commit("pagination", res.data);
                        }
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(`admin/reviews/show/${payload}`)
                    .then((res) => {
                        context.commit("show", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        export: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "admin/reviews/export";
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios
                    .get(url, { responseType: "blob" })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        reset: function (context) {
            context.commit("reset");
        },
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
        },
        show: function (state, payload) {
            state.show = payload;
        },
        reset: function (state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
    },
};
