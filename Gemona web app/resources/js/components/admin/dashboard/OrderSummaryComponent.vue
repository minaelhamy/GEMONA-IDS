<template>
    <LoadingComponent :props="loading" />
    <div class="col-12 xl:col-6">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="font-semibold text-lg capitalize text-heading">{{ $t('label.orders_summary') }}</h3>
                <div id="order-range" class="cursor-pointer flex items-center gap-3">
                    <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                        :placeholder="$t('label.select_date_range')" @update:modelValue="orderSummary"
                        v-model="modelValue" :range="true">
                    </Datepicker>
                </div>
            </div>
            <div class="db-card-body">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                    <apexchart v-if="options" :options="options" :series="options.series"></apexchart>
                    <ul class="flex flex-col gap-8 w-full sm:w-36">
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.delivered") }} ({{ delivered }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#FF6946]"></span>
                        </li>
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.canceled") }} ({{ canceled }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#A953FF]"></span>
                        </li>
                        <li class="w-full">
                            <span class="block capitalize mb-1 text-heading">
                                {{ $t("label.rejected") }} ({{ rejected }}%)
                            </span>
                            <span class="block w-full h-2 rounded bg-[#FB4E4E]"></span>
                        </li>
                    </ul>
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
    name: "OrderSummaryComponent",
    components: { LoadingComponent, Datepicker },
    data() {
        return {
            loading: {
                isActive: false,
            },
            first_date: null,
            last_date: null,
            delivered: null,
            canceled: null,
            rejected: null,
            modelValue: null,
            options: null
        };
    },
    mounted() {
        this.orderSummary();
    },
    methods: {
        orderSummary: function (e) {
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
            this.$store.dispatch("dashboard/orderSummary", date).then((res) => {
                this.delivered = res.data.data.delivered;
                this.canceled = res.data.data.canceled;
                this.rejected = res.data.data.rejected;

                this.options = {
                    series: [parseInt(this.delivered), parseInt(this.canceled), parseInt(this.rejected)],
                    chart: {
                        height: 320,
                        type: 'radialBar',
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '25%' },
                            track: { margin: 10 },
                            dataLabels: {
                                name: {
                                    fontSize: '14px',
                                    fontFamily: 'inherit',
                                },
                                value: {
                                    fontSize: '14px',
                                    fontFamily: 'inherit',
                                    fontWeight: 'bold',
                                    color: '#1F1F39',
                                    offsetY: 5,
                                },
                                total: {
                                    show: true,
                                    label: this.$t('label.total'),
                                    formatter: function (w) {
                                        return w.config.series.reduce((a, b) => a + b, 0);
                                    }
                                }
                            }
                        },
                    },
                    stroke: { lineCap: 'round' },
                    colors: ['#FF6946', '#A953FF', '#FB4E4E'],
                    labels: [this.$t('label.delivered'), this.$t('label.canceled'), this.$t('label.rejected')],
                };

                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    },
}
</script>