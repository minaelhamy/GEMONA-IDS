<template>
    <ul v-if="order.status !== enums.orderStatusEnum.CANCELED && order.status !== enums.orderStatusEnum.REJECTED && order.order_type !== enums.orderTypeEnum.PICK_UP"
        class="w-full flex items-center justify-center pb-12 mt-8 mb-5">
        <li v-for="track in tracks" class="w-full flex items-center justify-center gap-1 relative">
            <hr :class="{ '!bg-success': track.step <= order.status }"
                class="block border-none w-full h-1 rounded-xl bg-gray-200" />
            <i :class="{ 'lab-fill-save !bg-success text-white': track.step <= order.status }"
                class="flex-shrink-0 w-7 h-7 leading-7 text-center rounded-full bg-gray-200 lab-font-size-16" />
            <hr :class="{ '!bg-success': track.step <= order.status }"
                class="block border-none w-full h-1 rounded-xl bg-gray-200" />
            <span
                class="absolute top-10 left-1/2 -translate-x-1/2 w-14 sm:w-20 text-xs sm:text-sm leading-[18px] text-center capitalize">
                {{ track.title }}</span>
        </li>
    </ul>

    <ul v-if="order.status !== enums.orderStatusEnum.CANCELED && order.status !== enums.orderStatusEnum.REJECTED && order.order_type === enums.orderTypeEnum.PICK_UP"
        class="w-full flex items-center justify-center pb-12 mt-8 mb-5">
        <li v-for="track in pickupTracks" class="w-full flex items-center justify-center gap-1 relative">
            <hr :class="{ '!bg-success': track.step <= order.status }"
                class="block border-none w-full h-1 rounded-xl bg-gray-200" />
            <i :class="{ 'lab-fill-save !bg-success text-white': track.step <= order.status }"
                class="flex-shrink-0 w-7 h-7 leading-7 text-center rounded-full bg-gray-200 lab-font-size-16" />
            <hr :class="{ '!bg-success': track.step <= order.status }"
                class="block border-none w-full h-1 rounded-xl bg-gray-200" />
            <span
                class="absolute top-10 left-1/2 -translate-x-1/2 w-14 sm:w-20 text-xs sm:text-sm leading-[18px] text-center capitalize">
                {{ track.title }}</span>
        </li>
    </ul>
    <button v-if="order.status === enums.orderStatusEnum.CANCELED" type="button"
        class="flex items-center justify-center gap-2 py-3 sm:py-4 px-7 sm:px-10 mx-auto mt-6 rounded-2xl border border-[#FB4E4E] text-[#FB4E4E] bg-white transition-all duration-500 hover:bg-[#FB4E4E] hover:text-white mb-5">
        <i class="lab-fill-close-circle sm:text-xl"></i>
        <span class="sm:text-lg font-bold capitalize whitespace-nowrap">{{ $t('label.order_cancelled') }}</span>
    </button>

    <button v-if="order.status === enums.orderStatusEnum.REJECTED" type="button"
        class="flex items-center justify-center gap-2 py-3 sm:py-4 px-7 sm:px-10 mx-auto mt-6 rounded-2xl border border-[#FB4E4E] text-[#FB4E4E] bg-white transition-all duration-500 hover:bg-[#FB4E4E] hover:text-white">
        <i class="lab-fill-close-circle sm:text-xl"></i>
        <span class="sm:text-lg font-bold capitalize whitespace-nowrap">{{ $t('label.order_rejected') }}</span>
    </button>
</template>

<script>

import orderStatusEnum from "../../../enums/modules/orderStatusEnum";
import orderTypeEnum from "../../../enums/modules/orderTypeEnum";

export default {
    name: "OrderStatusComponent",
    props: {
        order: Object,
    },
    data() {
        return {
            tracks: [
                { step: 1, title: this.$t('label.order_pending') },
                { step: 5, title: this.$t('label.order_confirmed') },
                { step: 7, title: this.$t('label.order_on_the_way') },
                { step: 10, title: this.$t('label.order_delivered') },
            ],
            pickupTracks: [
                { step: 1, title: this.$t('label.order_pending') },
                { step: 5, title: this.$t('label.order_confirmed') },
                { step: 10, title: this.$t('label.order_delivered') },
            ],
            enums: {
                orderStatusEnum: orderStatusEnum,
                orderTypeEnum: orderTypeEnum,
                orderStatusEnumArray: {
                    [orderStatusEnum.PENDING]: this.$t("label.pending"),
                    [orderStatusEnum.CONFIRMED]: this.$t("label.confirmed"),
                    [orderStatusEnum.ON_THE_WAY]: this.$t("label.on_the_way"),
                    [orderStatusEnum.DELIVERED]: this.$t("label.delivered"),
                    [orderStatusEnum.CANCELED]: this.$t("label.canceled"),
                    [orderStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
            },
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
    },
}
</script>
