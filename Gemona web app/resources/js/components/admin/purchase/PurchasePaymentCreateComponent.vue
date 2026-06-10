<template>
    <LoadingComponent :props="loading" />
    <div v-if="errors.global" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mt-4 mx-4 rounded relative"
        role="alert">
        <span class="block sm:inline">{{ errors.global[0] }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="close">
            <i class="lab lab-fill-close-circle margin-top-5-px"></i>
        </span>
    </div>
    <div class="modal-body">
        <form @submit.prevent="save">
            <div class="form-row">
                <div class="form-col-12 sm:form-col-6">
                    <label for="date" class="db-field-title required">{{
                        $t("label.date")
                    }}</label>
                    <Datepicker hideInputIcon autoApply v-model="form.date" :enableTimePicker="true" :is24="false" 
                        :monthChangeOnScroll="false" utc="false">
                        <template #am-pm-button="{ toggle, value }">
                            <button @click="toggle">{{ value }}</button>
                        </template>
                    </Datepicker>
                    <small class="db-field-alert" v-if="errors.date">{{
                        errors.date[0]
                    }}</small>
                </div>

                <div class="form-col-12 sm:form-col-6">
                    <label for="reference_no" class="db-field-title">{{
                        $t("label.reference_no")
                    }}</label>
                    <input v-model="form.reference_no" v-bind:class="errors.reference_no ? 'invalid' : ''" type="text"
                        id="name" class="db-field-control" />
                    <small class="db-field-alert" v-if="errors.reference_no">{{
                        errors.reference_no[0]
                    }}</small>
                </div>

                <div class="form-col-12 sm:form-col-6">
                    <label for="amount" class="db-field-title required">{{
                        $t("label.amount")
                    }}</label>
                    <input v-on:keypress="onlyNumber($event)" v-on:keyup="paymentAmount($event)" v-model="form.amount"
                        v-bind:class="errors.amount ? 'invalid' : ''" type="text" id="amount" class="db-field-control" />
                    <small class="db-field-alert" v-if="errors.amount">{{
                        errors.amount[0]
                    }}</small>
                </div>

                <div class="form-col-12 sm:form-col-6">
                    <label for="payment_method" class="db-field-title required">{{
                        $t('label.payment_method')
                    }}</label>
                    <vue-select class="db-field-control f-b-custom-select" id="payment_method" v-model="form.payment_method"
                        :options="[
                            { id: enums.purchasePaymentMethodEnum.CASH, name: $t('label.cash') },
                            { id: enums.purchasePaymentMethodEnum.CHEQUE, name: $t('label.cheque') },
                            { id: enums.purchasePaymentMethodEnum.CREDIT_CARD, name: $t('label.credit_card') },
                            { id: enums.purchasePaymentMethodEnum.OTHERS, name: $t('label.others') }
                        ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                        placeholder="--" search-placeholder="--" />
                    <small class="db-field-alert" v-if="errors.payment_method">{{
                        errors.payment_method[0]
                    }}</small>
                </div>

                <div class="form-col-12">
                    <label for="file" class="db-field-title">
                        {{ $t("label.file") }}
                    </label>
                    <input @change="changefile" v-bind:class="errors.file ? 'invalid' : ''" id="file" type="file"
                        class="db-field-control" ref="fileProperty" accept="file/png, file/jpeg, file/jpg" />
                    <small class="db-field-alert" v-if="errors.file">{{
                        errors.file[0]
                    }}</small>
                </div>

                <div class="form-col-12">
                    <div class="modal-btns">
                        <button type="button" class="modal-btn-outline modal-close" @click="reset">
                            <i class="lab lab-fill-close-circle"></i>
                            <span>{{ $t("button.close") }}</span>
                        </button>

                        <button type="submit" class="db-btn py-2 text-white bg-primary">
                            <i class="lab lab-fill-save"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
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

export default {
    name: "PurchasePaymentCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent, Datepicker },
    props:['search'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            form: {
                purchase_id: "",
                date: "",
                reference_no: "",
                amount: "",
                payment_method: null,
            },
            enums: {
                purchasePaymentMethodEnum: purchasePaymentMethodEnum,
            },
            file: "",
            errors: {},
            dueAmount: "",
        };
    },

    mounted() {
        this.loading.isActive = true;
        this.show();
    },
    methods: {
        changefile: function (e) {
            this.file = e.target.files[0];
        },
        onlyNumber(e) {
            return appService.onlyNumber(e);
        },
        close: function () {
            this.errors.global = ""
        },
        show: function () {
            if (this.$store.getters["purchase/temp"].temp_id) {
                this.loading.isActive = true;
                this.$store.dispatch("purchase/show", this.$store.getters["purchase/temp"].temp_id).then((res) => {
                    this.form.amount = res.data.data.due_payment;
                    this.dueAmount = res.data.data.due_payment;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                })
            }

        },
        paymentAmount: function (e) {
            if (e.target.value > this.dueAmount) {
                this.form.amount = this.dueAmount;
            }
        },
        reset: function () {
            appService.modalHide();
            this.errors = {};
            this.$store.dispatch("purchase/reset");
            this.form = {
                purchase_id: "",
                date: "",
                reference_no: "",
                amount: "",
                payment_method: null,
            };
            this.dueAmount = "";
            if (this.file) {
                this.file = "";
                this.$refs.fileProperty.value = null;
            }
        },

        save: function () {
            try {
                const tempId = this.$store.getters["purchase/temp"].temp_id;
                const fd = new FormData();
                fd.append("purchase_id", tempId);
                fd.append("date", this.form.date);
                fd.append("reference_no", this.form.reference_no);
                fd.append("amount", this.form.amount);
                fd.append("payment_method", this.form.payment_method ?? "");
                if (this.file) {
                    fd.append("file", this.file);
                }

                this.loading.isActive = true;
                this.$store
                    .dispatch("purchase/addPayment", {
                        form: fd,
                        search:this.search
                    })
                    .then((res) => {
                        appService.modalHide();
                        this.loading.isActive = false;
                        alertService.successFlip(
                            0,
                            this.$t("menu.add_payment")
                        );
                        this.$store.dispatch("purchase/reset");
                        this.form = {
                            purchase_id: "",
                            date: "",
                            reference_no: "",
                            amount: "",
                            payment_method: null,
                        };
                        this.dueAmount = "";
                        this.errors = {};
                        if (this.file) {
                            this.file = "";
                            this.$refs.fileProperty.value = null;
                        }
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>
