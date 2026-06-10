<template>
    <div id="orderPayment" class="modal">
        <div class="modal-dialog max-w-[428px] w-full">
            <div class="modal-header pb-3 border-b border-[#D9DBE9]">
                <h3 class="capitalize font-medium">{{ $t("label.order_payment") }}</h3>
                <button @click="closeModal" class="modal-close fa-regular fa-circle-xmark"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div
                        class="flex justify-between items-center h-12 w-full rounded-lg py-1.5 px-2 placeholder:text-[10px] placeholder:text-[#6E7191] bg-[#F7F7FC]">
                        <span class="text-sm font-normal text-[#2E2F38]">{{ $t("label.total_amount") }}</span>
                        <span class="text-primary text-base font-medium">{{ total }}</span>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 class="capitalize font-medium mb-2">{{ $t("label.select_payment_method") }}</h3>
                    <nav class="flex flex-wrap gap-4 active-group">
                        <button @click.prevent="updatePaymentMethod(posPaymentMethodEnum.CASH)" data-tab="#cash"
                            type="button" :class="pos_payment_method === posPaymentMethodEnum.CASH ? 'active' : ''"
                            class="db-tab-btn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M19.1709 6.64002C18.7409 4.47002 17.1309 3.52002 14.8909 3.52002H6.11094C3.47094 3.52002 1.71094 4.84002 1.71094 7.92002V13.07C1.71094 15.29 2.62094 16.59 4.12094 17.15C4.34094 17.23 4.58094 17.3 4.83094 17.34C5.23094 17.43 5.66094 17.47 6.11094 17.47H14.9009C17.5409 17.47 19.3009 16.15 19.3009 13.07V7.92002C19.3009 7.45002 19.2609 7.03002 19.1709 6.64002ZM5.53094 12C5.53094 12.41 5.19094 12.75 4.78094 12.75C4.37094 12.75 4.03094 12.41 4.03094 12V9.00002C4.03094 8.59002 4.37094 8.25002 4.78094 8.25002C5.19094 8.25002 5.53094 8.59002 5.53094 9.00002V12ZM10.5009 13.14C9.04094 13.14 7.86094 11.96 7.86094 10.5C7.86094 9.04002 9.04094 7.86002 10.5009 7.86002C11.9609 7.86002 13.1409 9.04002 13.1409 10.5C13.1409 11.96 11.9609 13.14 10.5009 13.14ZM16.9609 12C16.9609 12.41 16.6209 12.75 16.2109 12.75C15.8009 12.75 15.4609 12.41 15.4609 12V9.00002C15.4609 8.59002 15.8009 8.25002 16.2109 8.25002C16.6209 8.25002 16.9609 8.59002 16.9609 9.00002V12Z"
                                    fill="#6E7191" />
                                <path
                                    d="M22.2998 10.92V16.07C22.2998 19.15 20.5398 20.48 17.8898 20.48H9.10977C8.35977 20.48 7.68977 20.37 7.10977 20.15C6.63977 19.98 6.22977 19.73 5.89977 19.41C5.71977 19.24 5.85977 18.97 6.10977 18.97H14.8898C18.5898 18.97 20.7898 16.77 20.7898 13.08V7.92003C20.7898 7.68003 21.0598 7.53003 21.2298 7.71003C21.9098 8.43003 22.2998 9.48003 22.2998 10.92Z"
                                    fill="#6E7191" />
                            </svg>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.cash") }}</span>
                        </button>
                        <button @click.prevent="updatePaymentMethod(posPaymentMethodEnum.CARD)" data-tab="#card"
                            type="button" :class="pos_payment_method === posPaymentMethodEnum.CARD ? 'active' : ''"
                            class="db-tab-btn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M22 7.5499C22 8.2099 21.46 8.7499 20.8 8.7499H3.2C2.54 8.7499 2 8.2099 2 7.5499V7.5399C2 5.2499 3.85 3.3999 6.14 3.3999H17.85C20.14 3.3999 22 5.2599 22 7.5499Z"
                                    fill="#6E7191" />
                                <path
                                    d="M2 11.45V16.46C2 18.75 3.85 20.6 6.14 20.6H17.85C20.14 20.6 22 18.74 22 16.45V11.45C22 10.79 21.46 10.25 20.8 10.25H3.2C2.54 10.25 2 10.79 2 11.45ZM8 17.25H6C5.59 17.25 5.25 16.91 5.25 16.5C5.25 16.09 5.59 15.75 6 15.75H8C8.41 15.75 8.75 16.09 8.75 16.5C8.75 16.91 8.41 17.25 8 17.25ZM14.5 17.25H10.5C10.09 17.25 9.75 16.91 9.75 16.5C9.75 16.09 10.09 15.75 10.5 15.75H14.5C14.91 15.75 15.25 16.09 15.25 16.5C15.25 16.91 14.91 17.25 14.5 17.25Z"
                                    fill="#6E7191" />
                            </svg>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.card") }}</span>
                        </button>
                        <button @click.prevent="updatePaymentMethod(posPaymentMethodEnum.MOBILE_BANKING)"
                            data-tab="#mfs" type="button"
                            :class="pos_payment_method === posPaymentMethodEnum.MOBILE_BANKING ? 'active' : ''"
                            class="db-tab-btn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M16.24 2H7.76C5 2 4 3 4 5.81V18.19C4 21 5 22 7.76 22H16.23C19 22 20 21 20 18.19V5.81C20 3 19 2 16.24 2ZM12 19.3C11.04 19.3 10.25 18.51 10.25 17.55C10.25 16.59 11.04 15.8 12 15.8C12.96 15.8 13.75 16.59 13.75 17.55C13.75 18.51 12.96 19.3 12 19.3ZM14 6.25H10C9.59 6.25 9.25 5.91 9.25 5.5C9.25 5.09 9.59 4.75 10 4.75H14C14.41 4.75 14.75 5.09 14.75 5.5C14.75 5.91 14.41 6.25 14 6.25Z"
                                    fill="#6E7191" />
                            </svg>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.mfs") }}</span>
                        </button>
                        <button @click.prevent="updatePaymentMethod(posPaymentMethodEnum.OTHER)" data-tab="#otherpay"
                            type="button" :class="pos_payment_method === posPaymentMethodEnum.OTHER ? 'active' : ''"
                            class="db-tab-btn w-fit flex flex-col items-center gap-2 rounded-lg py-3 px-7 border bg-[#F7F7FC] border-[#F7F7FC]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M15.78 2H8.22C4.44 2 3.5 3.01 3.5 7.04V18.3C3.5 20.96 4.96 21.59 6.73 19.69L6.74 19.68C7.56 18.81 8.81 18.88 9.52 19.83L10.53 21.18C11.34 22.25 12.65 22.25 13.46 21.18L14.47 19.83C15.19 18.87 16.44 18.8 17.26 19.68C19.04 21.58 20.49 20.95 20.49 18.29V7.04C20.5 3.01 19.56 2 15.78 2ZM15 11.75H9C8.59 11.75 8.25 11.41 8.25 11C8.25 10.59 8.59 10.25 9 10.25H15C15.41 10.25 15.75 10.59 15.75 11C15.75 11.41 15.41 11.75 15 11.75ZM16 7.75H8C7.59 7.75 7.25 7.41 7.25 7C7.25 6.59 7.59 6.25 8 6.25H16C16.41 6.25 16.75 6.59 16.75 7C16.75 7.41 16.41 7.75 16 7.75Z"
                                    fill="#6E7191" />
                            </svg>
                            <span class="text-xs font-normal leading-none text-heading">{{ $t("label.other") }}</span>
                        </button>
                    </nav>
                </div>
                <div id="cash" :class="pos_payment_method === posPaymentMethodEnum.CASH ? 'active' : ''"
                    class="db-tab-div">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t("label.input_amount") }}</h3>
                        <input ref="pos_received_amount" v-model="pos_received_amount" id="cashInput" type="float"
                            class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9] text-black">
                    </div>
                    <div class="grid grid-cols-4 gap-x-4 gap-y-3.5 mb-6">
                        <button @click="solve('1', 'cashInput')" value="1"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">1</button>
                        <button @click="solve('2', 'cashInput')" value="2"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">2</button>
                        <button @click="solve('3', 'cashInput')" value="3"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">3</button>
                        <button @click="back('cashInput')" value="cut"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M16.9997 3.75H10.2797C8.86969 3.75 7.52969 4.34 6.57969 5.39L3.04969 9.27C1.63969 10.82 1.63969 13.18 3.04969 14.73L6.57969 18.61C7.52969 19.65 8.86969 20.25 10.2797 20.25H16.9997C19.7597 20.25 21.9997 18.01 21.9997 15.25V8.75C21.9997 5.99 19.7597 3.75 16.9997 3.75ZM16.5297 13.94C16.8197 14.23 16.8197 14.71 16.5297 15C16.3797 15.15 16.1897 15.22 15.9997 15.22C15.8097 15.22 15.6197 15.15 15.4697 15L13.5297 13.06L11.5897 15C11.4397 15.15 11.2497 15.22 11.0597 15.22C10.8697 15.22 10.6797 15.15 10.5297 15C10.2397 14.71 10.2397 14.23 10.5297 13.94L12.4697 12L10.5297 10.06C10.2397 9.77 10.2397 9.29 10.5297 9C10.8197 8.71 11.2997 8.71 11.5897 9L13.5297 10.94L15.4697 9C15.7597 8.71 16.2397 8.71 16.5297 9C16.8197 9.29 16.8197 9.77 16.5297 10.06L14.5897 12L16.5297 13.94Z"
                                    fill="#1F1F39" />
                            </svg>
                        </button>
                        <button @click="solve('4', 'cashInput')" value="4"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">4</button>
                        <button @click="solve('5', 'cashInput')" value="5"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">5</button>
                        <button @click="solve('6', 'cashInput')" value="6"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">6</button>
                        <button @click="solve('7', 'cashInput')" value="7"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">7</button>
                        <button @click="solve('8', 'cashInput')" value="8"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">8</button>
                        <button @click="solve('9', 'cashInput')" value="9"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">9</button>
                        <button @click="clear('cashInput')" type="reset"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                            Clear
                        </button>
                        <button @click="solve('00', 'cashInput')" value="00"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">00</button>
                        <button @click="solve('0', 'cashInput')" value="0"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">0</button>
                        <button @click="solve('.', 'cashInput')" value="point"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">.</button>
                    </div>
                </div>
                <div id="card" :class="pos_payment_method === posPaymentMethodEnum.CARD ? 'active' : ''"
                    class="db-tab-div">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t("label.enter_card_last_4_digits") }}</h3>
                        <input ref="pos_payment_note" v-model="pos_payment_note"  id="cardInput" max="4" type="number"
                            class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9] text-black"
                            required>
                    </div>
                    <div class="grid grid-cols-4 gap-x-4 gap-y-3.5 mb-6">
                        <button @click="solve('1', 'cardInput')"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">1</button>
                        <button @click="solve('2', 'cardInput')" value="2"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">2</button>
                        <button @click="solve('3', 'cardInput')" value="3"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">3</button>
                        <button @click="back('cardInput')" value="cut"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M16.9997 3.75H10.2797C8.86969 3.75 7.52969 4.34 6.57969 5.39L3.04969 9.27C1.63969 10.82 1.63969 13.18 3.04969 14.73L6.57969 18.61C7.52969 19.65 8.86969 20.25 10.2797 20.25H16.9997C19.7597 20.25 21.9997 18.01 21.9997 15.25V8.75C21.9997 5.99 19.7597 3.75 16.9997 3.75ZM16.5297 13.94C16.8197 14.23 16.8197 14.71 16.5297 15C16.3797 15.15 16.1897 15.22 15.9997 15.22C15.8097 15.22 15.6197 15.15 15.4697 15L13.5297 13.06L11.5897 15C11.4397 15.15 11.2497 15.22 11.0597 15.22C10.8697 15.22 10.6797 15.15 10.5297 15C10.2397 14.71 10.2397 14.23 10.5297 13.94L12.4697 12L10.5297 10.06C10.2397 9.77 10.2397 9.29 10.5297 9C10.8197 8.71 11.2997 8.71 11.5897 9L13.5297 10.94L15.4697 9C15.7597 8.71 16.2397 8.71 16.5297 9C16.8197 9.29 16.8197 9.77 16.5297 10.06L14.5897 12L16.5297 13.94Z"
                                    fill="#1F1F39" />
                            </svg>
                        </button>
                        <button @click="solve('4', 'cardInput')" value="4"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">4</button>
                        <button @click="solve('5', 'cardInput')" value="5"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">5</button>
                        <button @click="solve('6', 'cardInput')" value="6"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">6</button>
                        <button @click="solve('7', 'cardInput')" value="7"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">7</button>
                        <button @click="solve('8', 'cardInput')" value="8"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">8</button>
                        <button @click="solve('9', 'cardInput')" value="9"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">9</button>
                        <button @click="clear('cardInput')" value="clear" type="reset"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39] row-span-2">
                            Clear
                        </button>
                        <button @click="solve('00', 'cardInput')" value="00"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">00</button>
                        <button @click="solve('0', 'cardInput')" value="0"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">0</button>
                        <button  value="point"
                            class="num bg-[#F7F7FC] rounded-lg p-2.5 flex items-center justify-center text-base font-medium text-[#1F1F39]">.</button>
                    </div>
                </div>
                <div id="mfs" :class="pos_payment_method === posPaymentMethodEnum.MOBILE_BANKING ? 'active' : ''"
                    class="db-tab-div">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t("label.enter_transaction_id") }}</h3>
                        <input ref="pos_payment_note" v-model="pos_payment_note" id="mfs-trans" type="text"
                            class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9]">
                    </div>
                    <div class="board grid grid-cols-10 justify-between gap-1.5 mb-6"></div>
                </div>
                <div id="otherpay" :class="pos_payment_method === posPaymentMethodEnum.OTHER ? 'active' : ''"
                    class="db-tab-div">
                    <div class="mb-4">
                        <h3 class="capitalize font-medium mb-2">{{ $t("label.enter_note") }}</h3>
                        <input ref="pos_payment_note" @change="getData" v-model="pos_payment_note" id="other-trans"
                            type="text"
                            class="h-12 w-full rounded-lg border py-1.5 px-4 placeholder:text-xs border-[#D9DBE9]">
                    </div>
                    <div class="board grid grid-cols-10 justify-between gap-1.5 mb-6"></div>
                </div>
                <button
                    @click.prevent="$emit('orderSubmit', { pos_payment_note: pos_payment_note, pos_payment_method: pos_payment_method, pos_received_amount: pos_received_amount });reset()"
                    data-modal="#receipt"
                    class="rounded-3xl text-base py-2 px-3 font-medium w-full text-white bg-primary">{{
                        $t("button.confirm_and_print_receipt") }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import appService from "../../../services/appService";
import posPaymentMethodEnum from "../../../enums/modules/posPaymentMethodEnum";
import { ref } from "vue";

export default {
    name: "PaymentComponent",
    props: {
        total: String,
    },
    data() {
        return {
            changesht: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "q", "w", "e", "r", "t", "y", "u", "i", "o", "p", '⇧', "a", "s", "d", "f", "g", "h", "j", "k", '↩', "l", "z", "x", "c", "v", "b", "n", "m"],
            originalsht: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, "Q", "W", "E", "R", "T", "Y", "U", "I", "O", "P", '⇧', "A", "S", "D", "F", "G", "H", "J", "K", '↩', "L", "Z", "X", "C", "V", "B", "N", "M"],
            posPaymentMethodEnum: posPaymentMethodEnum,
            pos_payment_method: posPaymentMethodEnum.CASH,
            pos_payment_note: '',
            pos_received_amount: null,
            keyboard: false
        };
    },
    computed: {
        company: function () {
            return this.$store.getters['company/lists'];
        },
        orderProducts: function () {
            return this.$store.getters['posOrder/orderProducts'];
        },
    },
    mounted() {
        this.$store.dispatch("company/lists").then().catch();
    },
    methods: {
        reset: function () {
            this.pos_payment_note = '';
            this.pos_received_amount = null;
            ["cashInput", "cardInput", "mfs-trans", "other-trans"].forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });
        },
        closeModal: function (){
           appService.modalHide('#orderPayment');
        },
        updatePaymentMethod: function (method) {
            this.pos_payment_method = method;
            this.pos_payment_note = '';
            this.pos_received_amount = null;

            ["cashInput", "cardInput", "mfs-trans", "other-trans"].forEach(id => {
                const input = document.getElementById(id);
                if (input) input.value = '';
            });

            if ([posPaymentMethodEnum.MOBILE_BANKING, posPaymentMethodEnum.OTHER].includes(method) && !this.keyboard) {
                this.initializeKeyboard();
                this.keyboard = true;
            }
        },

        solve: function (val, id) {
            const v = document.getElementById(id);
            v.value += val;
            if (this.pos_payment_method === posPaymentMethodEnum.CASH) {
                this.pos_received_amount = v.value;
            } else {
                this.pos_payment_note = v.value;
            }
        },

        clear: function (id) {
            const inp = document.getElementById(id);
            inp.value = '';
        },
        back: function (inputId) {
            if (inputId === "cashInput") {
                this.pos_received_amount = this.pos_received_amount.slice(0, -1);
            } else if (inputId === "cardInput") {
                this.pos_payment_note = this.pos_payment_note.slice(0, -1);
            }
        },

        backspace: function (id) {
            const textBoard = document.getElementById(id);
            textBoard.value = textBoard.value.slice(0, textBoard.value.length - 1);
        },
        evaluateClick: function (e) {
            const btnclicked = e.target.classList[0];
            if (btnclicked !== "board" && btnclicked !== "rows") {
                const btnText = e.target.innerText;
                const btnId = e.target.parentElement.parentElement.querySelector('input').getAttribute('id');
                this.action(btnText, btnId);
            }
        },
        shift: function (btnId) {
            const btn = document.getElementById(btnId).parentElement.parentElement.querySelector('.shifter');
            if (btn?.classList.contains("noshift")) {
                this.shifton(this.changesht, btnId);
            } else {
                this.shifton(this.originalsht, btnId);
            }
        },
        shifton: function (change, btnId) {
            const shift = document.getElementById(btnId).parentElement.parentElement.querySelector('.shifter');
            shift?.classList.toggle("noshift");
            const btnchng = document.getElementById(btnId).parentElement.parentElement.querySelectorAll(".cng");
            Array.from(btnchng).forEach((value, index) => {
                value.innerText = change[index];
            });
        },
        action: function (btnText, btnId) {
            switch (btnText) {
                case '↩':
                    this.backspace(btnId);
                    break;
                case "⇧":
                    this.shift(btnId);
                    break;
                default:
                    this.setText(btnText, btnId);
            }
        },

        clear: function (id) {
            this.pos_received_amount = '';
            this.pos_payment_note = '';
        },
        
        setText: function (text, id) {
            const element = document.getElementById(id);
            const cursorPosition = element.selectionStart;
            const currentText = element.value;
            element.value = currentText.substring(0, cursorPosition) + text + currentText.substring(cursorPosition);
            element.selectionStart = element.selectionEnd = cursorPosition + text.length;
            element.focus();
            if (this.pos_payment_method === posPaymentMethodEnum.MOBILE_BANKING || this.pos_payment_method === posPaymentMethodEnum.OTHER) {
                this.pos_payment_note = element.value;
            }
        },
        initializeKeyboard: function () {
            if (!this.keyboard) {
                const boards = document.querySelectorAll(".board");
                boards.forEach(board => {
                    board.addEventListener("click", this.evaluateClick);
                    this.originalsht.forEach(sht => {
                        const div = document.createElement('div');
                        div.classList.add('btnr', 'cng');
                        if (sht === '⇧') {
                            div.classList.add('noshift', 'row-span-2', 'col-span-1', 'shifter');
                            div.textContent = sht;
                        } else if (sht === '↩') {
                            div.classList.add('row-span-2', 'col-span-1');
                            div.textContent = sht;
                        } else {
                            div.textContent = sht;
                        }
                        board.appendChild(div);
                    });
                });
            }

        }

    },
}
</script>
