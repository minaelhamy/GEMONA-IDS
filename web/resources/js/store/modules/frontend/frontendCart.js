import _ from "lodash";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";
import shippingMethodEnum from "../../../enums/modules/shippingMethodEnum";
import ShippingTypeEnum from "../../../enums/modules/shippingTypeEnum";
import AskEnum from "../../../enums/modules/askEnum";


export const frontendCart = {
    namespaced: true,
    state: {
        lists: [],
        subtotal: 0,
        total: 0,
        coupon: {},
        discount: 0,
        orderType: null,
        shippingAddress: {},
        billingAddress: {},
        outletAddress: {},
        paymentMethod: {},
        totalTax: 0,
        shippingCharge: 0,
        isList: false,
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        subtotal: function (state) {
            return state.subtotal;
        },
        coupon: function (state) {
            return state.coupon;
        },
        discount: function (state) {
            return state.discount;
        },
        total: function (state) {
            return state.total;
        },
        orderType: function (state) {
            return state.orderType;
        },
        shippingAddress: function (state) {
            return state.shippingAddress;
        },
        billingAddress: function (state) {
            return state.billingAddress;
        },
        outletAddress: function (state) {
            return state.outletAddress;
        },
        paymentMethod: function (state) {
            return state.paymentMethod;
        },
        totalTax: function (state) {
            return state.totalTax;
        },
        shippingCharge: function (state) {
            return state.shippingCharge;
        },
        isList: function (state) {
            return state.isList;
        }
    },
    actions: {
        listChecker: function (context) {
            return new Promise((resolve, reject) => {
                if (context.state.lists.length > 0) {
                    context.commit('isList', true);
                    resolve({ status: true });
                } else {
                    context.commit('isList', false);
                    resolve({ status: false });
                }
                reject({
                    message: "no data found",
                    status: false
                });
            });
        },
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                if (Object.keys(payload).length > 0) {
                    let isNew = false;
                    let productMatch = false;
                    if (context.state.lists.length === 0) {
                        isNew = true;
                    } else {
                        _.forEach(context.state.lists, (list, listKey) => {
                            if (list.product_id === payload.product_id && list.variation_id === payload.variation_id) {
                                productMatch = true;
                                if ((payload.quantity + list.quantity) <= list.stock) {
                                    if ((payload.quantity + list.quantity) <= list.maximum_purchase_quantity) {
                                        context.state.lists[listKey].quantity += payload.quantity;
                                    } else {
                                        reject({
                                            message: "maximum_quantity",
                                            status: false
                                        });
                                    }
                                } else {
                                    reject({
                                        message: "stockOut",
                                        status: false
                                    });
                                }
                            }
                        });

                        if (!productMatch) {
                            isNew = true;
                        }
                        productMatch = false;
                    }

                    if (isNew) {
                        context.state.lists.push({
                            name: payload.name,
                            product_id: payload.product_id,
                            image: payload.image,
                            variation_names: payload.variation_names,
                            variation_id: payload.variation_id,
                            sku: payload.sku,
                            stock: payload.stock,
                            taxes: payload.taxes,
                            shipping: payload.shipping,
                            quantity: payload.quantity,
                            discount: payload.discount,
                            price: payload.price,
                            old_price: payload.old_price,
                            total_tax: 0,
                            subtotal: 0,
                            total: 0,
                            total_price: payload.total_price,
                            maximum_purchase_quantity: payload.maximum_purchase_quantity
                        });
                        isNew = false;
                    }
                }
                context.commit("taxCalculation");
                context.commit("shippingCharge", {
                    setting: context.rootState.frontendSetting.lists,
                    area: context.rootState.frontendOrderArea.lists
                });
                context.commit("subtotal");
                context.dispatch('listChecker').then().catch();
                resolve({ data: context.state.lists, status: true });
            });
        },
        quantity: function (context, payload) {
            context.commit("quantity", payload);
            context.commit("taxCalculation");
            context.commit("shippingCharge", {
                setting: context.rootState.frontendSetting.lists,
                area: context.rootState.frontendOrderArea.lists
            });
            context.commit("subtotal");
        },
        remove: function (context, payload) {
            context.commit("remove", payload);
            context.commit("taxCalculation");
            context.commit("shippingCharge", {
                setting: context.rootState.frontendSetting.lists,
                area: context.rootState.frontendOrderArea.lists
            });
            context.commit("subtotal");
            context.dispatch('listChecker').then().catch();
        },
        coupon: function (context, payload) {
            context.commit("coupon", payload);
            context.commit("subtotal");
        },
        destroyCoupon: function (context) {
            context.commit('coupon', {});
            context.commit("subtotal");
        },
        initOrderType: function (context, payload) {
            context.commit('orderTypeInit', payload);
            context.commit("shippingCharge", {
                setting: context.rootState.frontendSetting.lists,
                area: context.rootState.frontendOrderArea.lists
            });
            context.commit("subtotal");
        },
        updateOrderType: function (context, payload) {
            context.commit('updateOrderType', payload);
            context.commit("shippingCharge", {
                setting: context.rootState.frontendSetting.lists,
                area: context.rootState.frontendOrderArea.lists
            });
            context.commit("subtotal");
        },
        shippingAddress: function (context, payload) {
            context.commit('shippingAddress', payload);
            context.commit("shippingCharge", {
                setting: context.rootState.frontendSetting.lists,
                area: context.rootState.frontendOrderArea.lists
            });
            context.commit("subtotal");
        },
        billingAddress: function (context, payload) {
            context.commit('billingAddress', payload);
            context.commit("subtotal");
        },
        outletAddress: function (context, payload) {
            context.commit('outletAddress', payload);
            context.commit("subtotal");
        },
        paymentMethod: function (context, payload) {
            context.commit('paymentMethod', payload);
        },
        resetCart: function (context) {
            context.commit('resetCart');
        },
    },
    mutations: {
        subtotal: function (state) {
            state.total = 0;
            if (state.lists.length > 0) {
                let subtotal = 0;
                let total = 0;
                _.forEach(state.lists, (list, listKey) => {
                    state.lists[listKey].subtotal = state.lists[listKey].price * state.lists[listKey].quantity;
                    state.lists[listKey].total = ((state.lists[listKey].price * state.lists[listKey].quantity) + state.lists[listKey].total_tax) - state.lists[listKey].discount;
                    subtotal += state.lists[listKey].subtotal;
                    total += state.lists[listKey].total;
                });
                state.subtotal = subtotal;
                state.total = total;
            } else {
                state.subtotal = 0;
                state.total = 0;
            }

            if (state.shippingCharge > 0) {
                state.total += state.shippingCharge;
            }

            if (Object.keys(state.coupon).length > 0) {
                state.total -= state.coupon.convert_discount;
            }
        },
        quantity: function (state, payload) {
            if (payload.status === "increment") {
                state.lists[payload.id].quantity++;
            } else if (payload.status === "decrement") {
                if (state.lists[payload.id].quantity !== 1) {
                    state.lists[payload.id].quantity--;
                }
            } else {
                state.lists[payload.id].quantity = payload.status;
            }

            state.lists[payload.id].total_price = state.lists[payload.id].price * state.lists[payload.id].quantity;
        },
        remove: function (state, payload) {
            state.lists.splice(payload.id, 1);
        },
        coupon: function (state, payload) {
            state.coupon = payload;
            if (Object.keys(payload).length > 0) {
                state.discount = payload.convert_discount;
            } else {
                state.discount = 0;
            }
        },
        orderTypeInit: function (state, payload) {
            if (state.orderType === null) {
                state.orderType = payload.order_type;
            }
        },
        updateOrderType: function (state, payload) {
            if (orderTypeEnum.DELIVERY === payload || orderTypeEnum.PICK_UP === payload) {
                state.orderType = payload;
            } else {
                state.orderType = null;
            }
        },
        shippingAddress: function (state, payload) {
            state.shippingAddress = payload;
        },
        billingAddress: function (state, payload) {
            state.billingAddress = payload;
        },
        outletAddress: function (state, payload) {
            state.outletAddress = payload;
        },
        paymentMethod: function (state, payload) {
            state.paymentMethod = payload;
        },
        taxCalculation: function (state) {
            let stateTotalTax = 0;
            _.forEach(state.lists, (list, listKey) => {
                if (list.taxes.length > 0) {
                    let taxes = [];
                    let total_tax = 0;
                    _.forEach(list.taxes, (tax, taxKey) => {
                        if (tax.tax_rate > 0) {
                            let taxPercentagePrice = ((list.price / 100) * parseFloat(tax.tax_rate));
                            total_tax += taxPercentagePrice;
                            taxes.push({
                                id: tax.id,
                                name: tax.name,
                                code: tax.code,
                                tax_rate: parseFloat(tax.tax_rate),
                                tax_amount: parseFloat(taxPercentagePrice)
                            })
                        }
                    });
                    state.lists[listKey].taxes = taxes;
                    state.lists[listKey].total_tax = (total_tax * state.lists[listKey].quantity);
                }
                stateTotalTax += state.lists[listKey].total_tax;
            });
            state.totalTax = stateTotalTax;
        },
        shippingCharge: function (state, payload) {
            if (state.orderType === orderTypeEnum.DELIVERY) {
                if (payload.setting.shipping_setup_method === shippingMethodEnum.FLAT_RATE_WISE) {
                    state.shippingCharge = parseFloat(payload.setting.shipping_setup_flat_rate_wise_cost);
                } else if (payload.setting.shipping_setup_method === shippingMethodEnum.PRODUCT_WISE) {
                    let totalShippingCost = 0;
                    _.forEach(state.lists, (list, listKey) => {
                        if (list.shipping.shipping_type === ShippingTypeEnum.FLAT_RATE) {
                            if (list.shipping.is_product_quantity_multiply === AskEnum.YES) {
                                totalShippingCost += (parseFloat(list.shipping.shipping_cost) * list.quantity);
                            } else {
                                totalShippingCost += (parseFloat(list.shipping.shipping_cost));
                            }
                        }
                    });
                    state.shippingCharge = totalShippingCost;
                } else if (payload.setting.shipping_setup_method === shippingMethodEnum.AREA_WISE) {
                    if (Object.keys(state.shippingAddress).length > 0) {
                        let status = false;
                        _.forEach(payload.area, (list, listKey) => {
                            if (list.country === state.shippingAddress.country && list.state === state.shippingAddress.state && list.city === state.shippingAddress.city) {
                                status = true;
                                state.shippingCharge = parseFloat(list.shipping_cost);
                            }
                        });

                        if (!status) {
                            state.shippingCharge = parseFloat(payload.setting.shipping_setup_area_wise_default_cost);
                        }
                    }
                }
            } else {
                state.shippingCharge = 0;
            }
        },
        isList: function (state, payload) {
            state.isList = payload;
        },
        resetCart: function (state) {
            state.lists = [];
            state.subtotal = 0;
            state.total = 0;
            state.coupon = {};
            state.discount = 0;
            state.shippingAddress = {};
            state.billingAddress = {};
            state.outletAddress = {};
            state.paymentMethod = {};
            state.totalTax = 0;
            state.shippingCharge = 0;
        }
    },
};
