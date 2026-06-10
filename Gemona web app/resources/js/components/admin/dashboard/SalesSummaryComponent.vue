<template>
    <LoadingComponent :props="loading" />
    <div class="col-12 xl:col-6">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="font-semibold text-lg capitalize text-heading">{{ $t('label.sales_summary') }}</h3>
                <div id="sales-range" class="cursor-pointer flex items-center gap-3">

                    <Datepicker hideInputIcon autoApply :enableTimePicker="false" utc="false"
                        :placeholder="$t('label.select_date_range')" @update:modelValue="salesSummary"
                        v-model="modelValue" :range="true">
                    </Datepicker>
                </div>
            </div>
            <div class="db-card-body">
                <ul class="flex gap-11">
                    <li>
                        <div class="flex items-center gap-2.5">
                            <i class="lab lab-line-bar-chart lab-font-size-20 lab-font-color-2"></i>
                            <h3 class="font-bold text-xl text-secondary">{{ total_sales }}</h3>
                        </div>
                        <p class="text-xs capitalize">{{ $t("label.total_sales") }}</p>
                    </li>
                    <li>
                        <div class="flex items-center gap-2.5">
                            <i class="lab lab-line-bar-chart lab-font-size-20 lab-font-color-2"></i>
                            <h3 class="font-bold text-xl text-secondary">{{ avg_per_day }}</h3>
                        </div>
                        <p class="text-xs capitalize">{{ $t("label.avg_sales_per_day") }}</p>
                    </li>
                </ul>
                <apexchart height="188" v-if="options" :options="options" :series="options.series"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
export default {
    name: "SalesSummaryComponent",
    components: { LoadingComponent, Datepicker },
    data() {
        return {
            loading: {
                isActive: false,
            },
            modelValue: null,
            first_date: null,
            last_date: null,
            total_sales: null,
            avg_per_day: null,
            options: null
        };
    },
    mounted() {
        const date = new Date();
        const startDate = new Date(date.getFullYear(), date.getMonth(), 1);
        const endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        this.modelValue = [startDate, endDate];
        this.salesSummary();
    },
    methods: {
        salesSummary: function (e) {
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
            this.$store.dispatch("dashboard/salesSummary", date).then((res) => {
                this.total_sales = res.data.data.total_sales;
                this.avg_per_day = res.data.data.avg_per_day;
                this.options = {
                    series: [{
                        name: this.$t('label.sales'),
                        data: res.data.data.per_day_sales,
                    }],
                    chart: {
                        type: 'area',
                        height: 255,
                        fontFamily: 'inherit',
                        parentHeightOffset: 0,
                        zoom: { enabled: false },
                        toolbar: { show: false, },
                    },
                    xaxis: {
                        tooltip: { enabled: false },
                        axisBorder: { show: false },
                    },
                    stroke: {
                        width: 3,
                        lineCap: "round",
                        curve: "smooth",
                    },
                    colors: ["#FF6946"],
                    grid: { show: false },
                    yaxis: { show: false },
                    dataLabels: { enabled: false, },
                };
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
    }
}
</script>

<style>
/*===============================
        COMMON STYLE CSS
=================================*/
.dp__main:has(.shopperz-input) .dp__input {
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.3px;
    background-color: white;
    color: rgb(var(--primary));
    font-family: var(--client-font);
}

.dp__main:has(.shopperz-input) .dp__input::placeholder {
    font-size: 14px;
    font-weight: 500;
    opacity: 1;
    color: rgb(var(--primary));
}

.dp__main:has(.shopperz-input) .dp__input_icon {
    display: flex;
    align-items: center;
    justify-content: space-between;
    line-height: initial;
    width: 100%;
}

.dp__main:has(.shopperz-input) .dp__input_icon i {
    color: rgb(var(--primary));
}

.dp__main:has(.shopperz-input) .dp__clear_icon {
    line-height: initial;
}

.dp__main:has(.shopperz-input) .dp__clear_icon i {
    font-size: 22px;
    color: rgb(var(--primary));
}

.shopperz-menu .dp--preset-dates {
    border-inline-end: none;
    border-top: 1px solid #efefef;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
    grid-template-rows: auto;
    padding: 8px;
    gap: 6px;
}

@media only screen and (width <=600px) {
    .shopperz-menu .dp--preset-dates {
        align-self: normal;
        max-width: 100%;
    }
}

.shopperz-menu .dp--preset-range {
    font-size: 12px;
    font-weight: 500;
    padding: 6px 10px;
    border-radius: 6px;
    letter-spacing: 0.3px;
    font-family: var(--client-font);
    text-transform: capitalize;
    text-align: center;
    background-color: #f7f7f7;
}

.shopperz-menu .dp__calendar_header_separator {
    background-color: #efefef;
}

.shopperz-menu.dp__menu {
    width: 100%;
    max-width: 260px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 8px;
    letter-spacing: 0.3px;
    font-family: var(--client-font);
    color: rgb(86 106 127);
    text-transform: capitalize;
    border: none;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.04);
}

.shopperz-menu .dp__arrow_top,
.shopperz-menu .dp__arrow_bottom {
    border: none;
    border-radius: 3px;
}

.shopperz-menu .dp__action_row {
    border-top: 1px solid #efefef;
}

.shopperz-menu .dp__selection_preview {
    font-size: 12px;
}

.shopperz-menu .dp__action_buttons {
    gap: 6px;
}

.shopperz-menu .dp__action_button {
    margin: 0px;
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.3px;
    padding: 4px 10px;
    border-radius: 6px;
    height: initial;
    line-height: initial;
    font-family: var(--client-font);
}

.shopperz-menu .dp__action_select {
    background-color: rgb(var(--primary));
}

.shopperz-menu .dp__action_cancel:hover {
    border-color: rgb(var(--primary) / 45%);
}

.shopperz-menu .dp__today {
    border-color: rgb(var(--primary) / 45%);
}

.shopperz-menu .dp__range_between {
    background-color: rgb(var(--primary) / 5%);
    border-color: rgb(var(--primary) / 5%);
}

.shopperz-menu .dp__active_date,
.shopperz-menu .dp__range_start,
.shopperz-menu .dp__range_end {
    background-color: rgb(var(--primary));
}

.shopperz-menu .dp__menu_content_wrapper {
    flex-direction: column-reverse;
}


/*===============================
          READ STYLE CSS
=================================*/
.dp__main:has(.read) {
    max-width: fit-content;
}

.dp__main:has(.read) .dp__input {
    text-align: right;
    border: none;
    padding: 0px;
    line-height: 22px;
    padding-inline-end: 30px;
}

.dp__main:has(.read) .dp__input_icon {
    flex-direction: row-reverse;
}

.dp__main:has(.read) .dp__input_icon i {
    flex-direction: row-reverse;
    font-size: 20px;
}

.dp__main:has(.read) .dp__input_icon i:last-child {
    display: none;
}

.dp__main:has(.read) .dp__clear_icon {
    display: none;
}
</style>