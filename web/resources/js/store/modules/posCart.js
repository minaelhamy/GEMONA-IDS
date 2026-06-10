import _ from "lodash";

export const posCart = {
    namespaced: true,
    state: {
        lists: [],
        subtotal: 0,
        total: 0,
        discount: 0,
        totalTax: 0
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        subtotal: function (state) {
            return state.subtotal;
        },
        discount: function (state) {
            return state.discount;
        },
        total: function (state) {
            return state.total;
        },
        totalTax: function (state) {
            return state.totalTax;
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                if (Object.keys(payload).length > 0) {
                    let isNew        = false;
                    let productMatch = false;
                    if (context.state.lists.length === 0) {
                        isNew = true;
                    } else {
                        _.forEach(context.state.lists, (list, listKey) => {
                            if (list.product_id === payload.product_id && list.variation_id === payload.variation_id) {
                                productMatch = true;
                                if ((payload.quantity + list.quantity) <= list.stock) {
                                    context.state.lists[listKey].quantity += payload.quantity;
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
                            total_price: payload.total_price
                        });
                        isNew = false;
                    }
                }
                context.commit("taxCalculation");
                context.commit("subtotal");
                resolve({data: context.state.lists, status: true});
            });
        },
        quantity: function (context, payload) {
            context.commit("quantity", payload);
            context.commit("taxCalculation");
            context.commit("subtotal");
        },
        remove: function (context, payload) {
            context.commit("remove", payload);
            context.commit("taxCalculation");
            context.commit("subtotal");
        },
        resetCart: function (context) {
            context.commit('resetCart');
        },
        discount: function (context, payload) {
            context.commit("discount", payload);
            context.commit("subtotal");
        }
    },
    mutations: {
        subtotal: function (state) {
            state.total = 0;
            if (state.lists.length > 0) {
                let subtotal = 0;
                let total    = 0;
                _.forEach(state.lists, (list, listKey) => {
                    state.lists[listKey].subtotal = state.lists[listKey].price * state.lists[listKey].quantity;
                    state.lists[listKey].total    = ((state.lists[listKey].price * state.lists[listKey].quantity) + state.lists[listKey].total_tax) - state.lists[listKey].discount;
                    subtotal                      += state.lists[listKey].subtotal;
                    total                         += state.lists[listKey].total;
                });
                state.subtotal = subtotal;
                state.total    = total;
            } else {
                state.subtotal = 0;
                state.total    = 0;
            }

            if(state.discount > 0 && state.total > 0) {
                state.total -= state.discount;
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
        discount: function (state, payload) {
            state.discount = Number(payload);
        },
        taxCalculation: function (state) {
            let stateTotalTax = 0;
            _.forEach(state.lists, (list, listKey) => {
                if (list.taxes.length > 0) {
                    let taxes     = [];
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
                    state.lists[listKey].taxes     = taxes;
                    state.lists[listKey].total_tax = (total_tax * state.lists[listKey].quantity);
                }
                stateTotalTax += state.lists[listKey].total_tax;
            });
            state.totalTax = stateTotalTax;
        },
        resetCart: function (state) {
            state.lists          = [];
            state.subtotal       = 0;
            state.total          = 0;
            state.discount       = 0;
            state.totalTax       = 0;
        }
    },
};
