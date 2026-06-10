import axios from 'axios'

export const ai = {
    namespaced: true,
    state: () => ({
        status: false,
        name: null,
        description: null,
        chatHistory: [],
        usageLimit: null,
    }),
    getters: {
        status: function (state) {
            return state.status;
        },
        name: function (state) {
            return state.name;
        },
        description: function (state) {
            return state.description;
        },
        chatHistory: function (state) {
            return state.chatHistory;
        },
        usageLimit: function (state) {
            return state.usageLimit;
        }
    },
    actions: {
        fetchStatus: function (context) {
            return new Promise((resolve, reject) => {
                axios.get("/admin/ai/status").then((res) => {
                    context.commit('status', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchName: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/ai/name", payload).then((res) => {
                    context.commit('name', res.data.data.response);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchDescription: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/ai/description", payload).then((res) => {
                    context.commit('description', res.data.data.response);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchTags: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post("/admin/ai/tags", payload).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        sendChatMessage: function (context, message) {
            return new Promise((resolve, reject) => {
                let url = "/admin/ai/chat";
                axios.post(url, {name: message}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        setChatResponse: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `/admin/ai/chat-response/${payload.id}`;
                axios.post(url, {name: payload.name}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        fetchChatHistory: function (context) {
            return new Promise((resolve, reject) => {
                let url = "/admin/ai/chat-history";
                axios.get(url).then((res) => {
                    if (res.data.data) {
                        context.commit('chatHistory', res.data.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        }
    },
     mutations: {
        status: function (state, payload) {
            state.status = payload;
        },
        name: function (state, payload) {
            state.name = payload;
        },
        description: function (state, payload) {
            state.description = payload;
        },
        chatHistory: function (state, payload) {
            state.chatHistory = payload;
        }
    },
}

