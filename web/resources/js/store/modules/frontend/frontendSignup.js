import axios from "axios";

export const frontendSignup = {
    namespaced: true,
    state: {
        phone: {},
        email: {},
        formData:{},
        organizations: [],
    },
    getters: {
        phone: function (state) {
            return state.phone;
        },
        email: function (state) {
            return state.phone;
        },
        formData: function (state) {
            return state.formData;
        },
        organizations: function (state) {
            return state.organizations;
        }
    },
    actions: {
        organizations: function (context) {
            return new Promise((resolve, reject) => {
                axios.get("auth/signup/organizations").then((res) => {
                    context.commit("organizations", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        otpPhone: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/otp-phone";
                axios.post(url,payload).then((res) => {
                    context.commit("phone", payload);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        otpEmail: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/otp-email";
                axios.post(url,payload).then((res) => {
                    context.commit("email", payload);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        signupValidation: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/register-validation";
                axios.post(url,payload).then((res) => {
                    context.commit("formData", payload);
                    context.commit("phone", payload);
                    context.commit("email", payload);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        signup: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/register";
                axios.post(url,payload).then((res) => {
                    context.commit("phone", payload);
                    context.commit("email", payload);
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
        phone: function (state, payload) {
            state.phone.otp = payload;
        },
        email: function (state, payload) {
            state.email.otp = payload;
        },
        formData: function(state, payload) {
            state.formData = payload;
        },
        verify: function (state, payload) {
            state.phone.status = payload;
        },
        reset: function(state) {
            state.phone = {};
            state.email = {};
            state.formData = {};
        },
        organizations: function(state, payload) {
            state.organizations = payload;
        }
    },
};
