<template>
    <LoadingComponent :props="loading" />
    <div class="col-12 xl:col-6">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="font-semibold text-lg capitalize text-heading">{{ $t('label.customer_stats') }}</h3>
                <div id="customer-range" class="cursor-pointer flex items-center gap-3">
                    <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                        :placeholder="$t('label.select_date_range')" @update:modelValue="customerStates"
                        v-model="modelValue" :range="true">
                    </Datepicker>
                </div>
            </div>
            <div class="db-card-body">
                <apexchart height="270" v-if="options" :options="options" :series="options.series"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
export default {
    name: "CustomerStatsComponent",
    components: { LoadingComponent, Datepicker },
    data() {
        return {
            loading: {
                isActive: false,
            },
            first_date: null,
            last_date: null,
            range: true,
            modelValue: null,
            options: null
        };
    },
    mounted() {
        this.customerStates();
    },
    methods: {
        customerStates: function (e) {
            let date = {
                first_date: '',
                last_date: '',
            };
            if (e) {
                this.first_date = e[0];
                this.last_date = e[1];
                date.first_date = e[0];
                date.last_date = e[1];
            }

            this.loading.isActive = true;

            this.$store.dispatch("dashboard/customerStates", date).then((res) => {
                this.options = {
                    series: [{
                        name: this.$t('menu.customers'),
                        data: res.data.data.total_customers,
                    }],
                    chart: {
                        type: 'bar',
                        height: 276,
                        parentHeightOffset: 0,
                        zoom: { enabled: false },
                        toolbar: { show: false },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '40%',
                            endingShape: 'rounded'
                        },
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['#567DFF']
                    },
                    xaxis: {
                        categories: res.data.data.times,
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        style: {
                            fontSize: '14px',
                            fontFamily: 'inherit',
                        }
                    },
                    colors: ['#567DFF'],
                    grid: { show: false, },
                    yaxis: { show: false },
                    dataLabels: { enabled: false },
                };
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    },
}
</script>