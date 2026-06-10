import axios from "axios";

export const auth = {
    state: {
        authStatus: false,
        authToken: null,
        authInfo: {},
        authMenu: [],
        resetInfo: {
            email: null,
        },
        authPermission: {},
        authDefaultPermission: {},
        phone: {},
        email: {},
        authDefaultMenu: {},
    },
    getters: {
        authStatus: function (state) {
            return state.authStatus;
        },
        authToken: function (state) {
            return state.authToken;
        },
        authInfo: function (state) {
            return state.authInfo;
        },
        authMenu: function (state) {
            return state.authMenu;
        },
        authPermission: function (state) {
            return state.authPermission;
        },
        authDefaultPermission: function (state) {
            return state.authDefaultPermission;
        },
        resetInfo: function (state) {
            return state.resetInfo;
        },
        phone: function (state) {
            return state.phone;
        },
        email: function (state) {
            return state.phone;
        },
        authDefaultMenu: function (state) {
            return state.authDefaultMenu;
        },
    },
    actions: {
        profile: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/profile", payload)
                    .then((res) => {
                        context.commit("authInfo", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        login: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/login", payload)
                    .then((res) => {
                        context.commit("authLogin", res.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        socialLogin: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/login/" + payload;
                axios.get(url).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        verifySocialLogin: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/login/" + payload.provider, payload.code)
                    .then((res) => {
                        context.commit("authLogin", res.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        authcheck: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/authcheck", payload)
                    .then((res) => {
                        if (res.data.status === false) {
                            context.commit("authLogout");
                        }
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        logout: function (context) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/logout")
                    .then((res) => {
                        context.commit("authLogout");
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        forgotPassword: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/forgot-password", payload)
                    .then((res) => {
                        context.commit("email", payload);
                        context.commit("phone", payload);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        forgotPasswordVerifyPhone: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/forgot-password/verify-phone";
                axios
                    .post(url, payload)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        forgotPasswordVerifyEmail: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/forgot-password/verify-email";
                axios
                    .post(url, payload)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        otpPhone: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/forgot-password/otp-phone";
                axios
                    .post(url, payload)
                    .then((res) => {
                        context.commit("phone", payload);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        otpEmail: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/forgot-password/otp-email";
                axios
                    .post(url, payload)
                    .then((res) => {
                        context.commit("email", payload);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        resetPassword: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/forgot-password/reset-password", payload)
                    .then((res) => {
                        context.commit("authLogin", res.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        updateAuthInfo: function (context, payload) {
            return new Promise((resolve, reject) => {
                if (context.state.authInfo.id === payload.id) {
                    context.commit("authInfo", payload);
                    resolve(payload);
                } else {
                    reject("user data not match");
                }
            });
        },
        verifyPhone: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/verify-phone";
                axios
                    .post(url, payload)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        verifyEmail: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = "auth/signup/verify-email";
                axios
                    .post(url, payload)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        signupLoginVerify: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("auth/signup/login-verify", payload)
                    .then((res) => {
                        context.commit("authLogin", res.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        loginDataReset: function (context) {
            context.commit("authLogout");
        },
        reset: function (context) {
            context.commit("reset");
        },
    },
    mutations: {
        authLogin: function (state, payload) {
            state.authStatus = true;
            state.authToken = payload.token;
            state.authInfo = payload.user;
            state.authMenu = payload.menu;
            state.authPermission = payload.permission;
            state.authDefaultPermission = payload.defaultPermission;
            state.authDefaultMenu = payload.defaultMenu;
        },
        authLogout: function (state) {
            state.authStatus = false;
            state.authToken = null;
            state.authInfo = {};
            state.authMenu = [];
            state.authPermission = {};
            state.authDefaultPermission = {};
            state.authDefaultMenu = {};
        },
        forgotPassword: function (state, payload) {
            state.resetInfo = {
                email: payload.email,
            };
        },
        resetPassword: function (state) {
            state.resetInfo = {
                email: null,
            };
        },
        authInfo: function (state, payload) {
            state.authInfo = payload;
        },
        phone: function (state, payload) {
            state.phone.otp = payload;
        },
        email: function (state, payload) {
            state.email.otp = payload;
        },
        reset: function (state) {
            state.phone = {};
            state.email = {};
        },
    },
};
