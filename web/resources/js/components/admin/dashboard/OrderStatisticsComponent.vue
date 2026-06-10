<template>
    <LoadingComponent :props="loading" />
    <div class="flex items-center justify-between gap-3 mb-4">
        <h4 class="font-semibold text-xl capitalize text-heading">{{ $t('menu.order_statistics') }}</h4>
        <div class="relative cursor-pointer">
            <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false" 
                :placeholder="$t('label.select_date_range')" @update:modelValue="handleDate" v-model="modelValue"
                :range="true">
            </Datepicker>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-orange/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box text-admin-orange text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">
                        {{ $t('label.total_orders') }}
                    </h3>
                    <h4 class="font-bold text-lg text-secondary">{{ total_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-yellow/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-time text-admin-yellow text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.pending') }}</h3>
                    <h4 class="font-bold text-lg text-secondary">{{ pending_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-green/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-search text-admin-green text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.confirmed') }}
                    </h3>
                    <h4 class="font-bold text-lg text-secondary">{{ confirmed_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-sky/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-shipping text-admin-sky text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">
                        {{ $t('label.ongoing') }}
                    </h3>
                    <h4 class="font-bold text-lg text-secondary">{{ ongoing_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-purple/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-tick text-admin-purple text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.delivered') }}</h3>
                    <h4 class="font-bold text-lg text-secondary">{{ delivered_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-red/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-cross text-admin-red text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.canceled') }}</h3>
                    <h4 class="font-bold text-lg text-secondary">{{ canceled_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-blue/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-rotate text-admin-blue text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.returned') }}</h3>
                    <h4 class="font-bold text-lg text-secondary">{{ returned_order }}</h4>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6 md:col-4 lg:col-6 xl:col-3">
            <div class="flex items-center gap-4 p-4 rounded-lg shadow-xs bg-white">
                <div class="bg-admin-red/10 w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="lab-fill-box-cross text-admin-red text-2xl lab-font-size-24"></i>
                </div>
                <div>
                    <h3 class="font-medium text-sm capitalize tracking-wide mb-1">{{ $t('label.rejected') }}</h3>
                    <h4 class="font-bold text-lg text-secondary">{{ rejected_order }}</h4>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
export default {
    name: "OrderStatisticsComponent",
    components: { LoadingComponent, Datepicker },
    data() {
        return {
            loading: {
                isActive: false,
            },
            first_date: null,
            last_date: null,
            total_order: null,
            pending_order: null,
            confirmed_order: null,
            ongoing_order: null,
            delivered_order: null,
            canceled_order: null,
            returned_order: null,
            rejected_order: null,
            modelValue: null
        };
    },
    mounted() {
        this.orderStatistic();
    },
    methods: {
        handleDate: function (e) {
            if (e) {
                this.first_date = e[0];
                this.last_date = e[1];

                this.loading.isActive = true;
                this.$store.dispatch("dashboard/orderStatistics", {
                    first_date: this.first_date,
                    last_date: this.last_date,
                }).then((res) => {
                    this.total_order = res.data.data.total_order;
                    this.pending_order = res.data.data.pending_order;
                    this.confirmed_order = res.data.data.confirmed_order;
                    this.ongoing_order = res.data.data.ongoing_order;
                    this.delivered_order = res.data.data.delivered_order;
                    this.canceled_order = res.data.data.canceled_order;
                    this.returned_order = res.data.data.returned_order;
                    this.rejected_order = res.data.data.rejected_order;
                    this.loading.isActive = false;
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            } else {
                this.date = null;
                this.first_date = null;
                this.last_date = null;
                this.orderStatistic();
            }
        },
        orderStatistic: function () {
            this.loading.isActive = true;
            this.$store.dispatch("dashboard/orderStatistics").then((res) => {
                this.total_order = res.data.data.total_order;
                this.pending_order = res.data.data.pending_order;
                this.confirmed_order = res.data.data.confirmed_order;
                this.ongoing_order = res.data.data.ongoing_order;
                this.delivered_order = res.data.data.delivered_order;
                this.canceled_order = res.data.data.canceled_order;
                this.returned_order = res.data.data.returned_order;
                this.rejected_order = res.data.data.rejected_order;
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        }
    }
}
</script>
