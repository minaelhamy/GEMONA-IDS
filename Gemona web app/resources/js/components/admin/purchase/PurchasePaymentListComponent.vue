<template>
    <LoadingComponent :props="loading" />
    <div class="modal-body p-0">
        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">{{ $t("label.date") }}</th>
                        <th class="db-table-head-th">{{ $t("label.reference_no") }}</th>
                        <th class="db-table-head-th">{{ $t("label.amount") }}</th>
                        <th class="db-table-head-th">{{ $t("label.payment_method") }}</th>
                        <th class="db-table-head-th">
                            {{ $t("label.action") }}
                        </th>
                    </tr>
                </thead>
                <tbody class="db-table-body" v-if="purchasePaymentList.length > 0">
                    <tr class="db-table-body-tr" v-for="purchasePayment in purchasePaymentList" :key="purchasePayment">
                        <td class="db-table-body-td">
                            {{ purchasePayment.converted_date }}
                        </td>
                        <td class="db-table-body-td">
                            {{ purchasePayment.reference_no }}
                        </td>
                        <td class="db-table-body-td">
                            {{ purchasePayment.amount }} <span v-if="purchasePayment.file">
                            </span>

                        </td>
                        <td class="db-table-body-td">
                            {{ enums.purchasePaymentMethodEnumArray[purchasePayment.payment_method] }}
                        </td>
                        <td class="db-table-body-td">
                                <button v-if="purchasePayment.file" class="db-table-action edit"  @click="download(purchasePayment.id)">
                                    <i class="lab lab-line-download"></i>
                                    <span class="db-tooltip">{{ $t('label.download') }}</span>
                                </button>

                                <SmIconDeleteComponent @click="destroy(purchasePayment.id)"
                                v-if="permissionChecker('purchase_delete')" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <div class="modal-btns">
            <button type="button" class="modal-btn-outline modal-close" @click="reset">
                <i class="lab lab-fill-close-circle"></i>
                <span>{{ $t("button.close") }}</span>
            </button>
        </div>
    </div>
</template>
<script>
import SmModalCreateComponent from "../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../components/LoadingComponent";
import purchasePaymentMethodEnum from "../../../enums/modules/purchasePaymentMethodEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";

export default {
    name: "PurchasePaymentListComponent",
    components: { SmModalCreateComponent, LoadingComponent, Datepicker, SmIconDeleteComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                purchasePaymentMethodEnum: purchasePaymentMethodEnum,
                purchasePaymentMethodEnumArray: {
                    [purchasePaymentMethodEnum.CASH]: this.$t("label.cash"),
                    [purchasePaymentMethodEnum.CHEQUE]: this.$t("label.cheque"),
                    [purchasePaymentMethodEnum.CREDIT_CARD]: this.$t("label.credit_card"),
                    [purchasePaymentMethodEnum.OTHERS]: this.$t("label.others")
                },
            },
            search: {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: 'id',
                order_type: 'desc',
            },
            file: "",
            errors: {},
        };
    },
    computed: {
        purchasePaymentList: function () {
            return this.$store.getters['purchase/viewPayment'];
        },
    },
    mounted() {
        this.loading.isActive = true;
        this.list();
    },
    methods: {
        permissionChecker(e) {
            return appService.permissionChecker(e);
        },
        changefile: function (e) {
            this.file = e.target.files[0];
        },
        close: function () {
            this.errors.global = ""
        },
        list: function () {
            this.$store
                .dispatch("purchase/viewPayment")
                .then((res) => {
                    this.loading.isActive = false;
                })
                .catch((err) => {
                    this.loading.isActive = false;
                });
        },
        reset: function () {
            this.$store.dispatch("purchase/reset");
            appService.modalHide('#purchasePaymentList');
        },

        download: function (purchasePaymentId) {
            if (purchasePaymentId) {
                this.loading.isActive = true;
                this.$store.dispatch("purchase/paymentDownload", purchasePaymentId).then((res) => {
                    this.loading.isActive = false;

                    let fileType = "";
                    if (res.data.type) {
                        let type = res.data.type;
                        type = type.split("/");
                        fileType = type[1];
                    }

                    if (res.data.size > 0) {
                        const url = window.URL.createObjectURL(
                            new Blob([res.data])
                        );
                        const link = document.createElement("a");
                        link.href = url;
                        link.download =
                            "" + this.$t("menu.purchase_payments") + "." + fileType;
                        link.click();
                        URL.revokeObjectURL(link.href);
                    } else {
                        alertService.info(this.$t("menu.purchase_payments") + " " + this.$t('message.attachment_not_found'));
                    }

                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },

        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    this.$store.dispatch("purchase/paymentDestroy", {
                        purchase_id: this.$store.getters["purchase/temp"].temp_id,
                        id: id,
                        search: this.search,
                    }).then((res) => {
                        appService.modalHide('#purchasePaymentList');
                        this.$store.dispatch("purchase/reset");
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t("menu.purchase_payments"));
                    }).catch((err) => {
                        this.loading.isActive = false;
                        alertService.error(err.response.data.message);
                    });
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.message);
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    },
};
</script>