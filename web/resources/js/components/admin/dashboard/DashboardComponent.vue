<template>
    <LoadingComponent :props="loading" />
    <div v-if="demo === 'true' || demo === 'TRUE' || demo === 'True' || demo === '1' || demo === 1"
        class="p-2 pl-4 mb-4 bg-red-100 rounded">
        <h2 class="mb-1">{{ $t('label.reminder') }}</h2>
        <p>{{ $t('label.data_reset') }}</p>
    </div>

    <div class="mb-8">
        <h3 class="capitalize font-bold text-2xl text-primary mb-1.5">{{ visitorMessage() }}</h3>
        <div class="flex items-center justify-between">
            <h4 class="text-xl font-medium capitalize text-secondary">{{ authInfo.name }}</h4>
            <span class="inline-flex items-center px-2 text-xl font-semibold border rounded-full border-primary/20 bg-primary/10 text-primary">
               {{ $t('label.version') }} : {{ setting.app_version }}
            </span>
        </div>
    </div>
    <!--========OVERVIEW START=============-->
    <OverviewComponent />
    <!--========OVERVIEW END=============-->

    <!--========ORDER STATISTIC START=============-->
    <OrderStatisticsComponent />
    <!--========ORDER STATISTIC END=============-->
    <div class="row">
        <!--========SALES SUMMARY START=============-->
        <SalesSummaryComponent />
        <!--========SALES SUMMARY END=============-->

        <!--========ORDERS SUMMARY START=============-->
        <OrderSummaryComponent />
        <!--========ORDERS SUMMARY END=============-->

        <!--========CUSTOMER STATS START=============-->
        <CustomerStatsComponent />
        <!--========CUSTOMER STATS END=============-->

        <!--========TOP CUSTOMERS START=============-->
        <TopCustomersComponent />
        <!--========TOP CUSTOMERS END=============-->

        <!--========MOST POPULAR ITEMS START=============-->
        <TopProductsComponent />
        <!--========MOST POPULAR ITEMS END=============-->
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import OverviewComponent from "./OverviewComponent";
import OrderStatisticsComponent from "./OrderStatisticsComponent";
import SalesSummaryComponent from "./SalesSummaryComponent";
import OrderSummaryComponent from "./OrderSummaryComponent";
import CustomerStatsComponent from "./CustomerStatsComponent";
import TopCustomersComponent from "./TopCustomersComponent";
import TopProductsComponent from "./TopProductsComponent";
import ENV from "../../../config/env";

export default {
    name: "DashboardComponent",
    components: {
        LoadingComponent,
        OverviewComponent,
        OrderStatisticsComponent,
        SalesSummaryComponent,
        OrderSummaryComponent,
        CustomerStatsComponent,
        TopCustomersComponent,
        TopProductsComponent,
    },
    data() {
        return {
            loading: {
                isActive: true,
            },
            demo: ENV.DEMO
        };
    },
    mounted(){
            this.$store.dispatch("frontendSetting/lists").then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                console.log(err);
            });
    },
    computed: {
        authInfo: function () {
            return this.$store.getters.authInfo;
        },
        setting: function () {
            return this.$store.getters["frontendSetting/lists"];
        },
    },
    methods: {
        visitorMessage: function () {
            let greet;
            let myDate = new Date();
            let hrs = myDate.getHours();
            if (hrs < 12) {
                greet = this.$t('message.good_morning');
            } else if (hrs >= 12 && hrs <= 17) {
                greet = this.$t('message.good_afternoon');
            } else if (hrs >= 17 && hrs <= 24) {
                greet = this.$t('message.good_evening');
            }
            return greet;
        }
    }
}
</script>
