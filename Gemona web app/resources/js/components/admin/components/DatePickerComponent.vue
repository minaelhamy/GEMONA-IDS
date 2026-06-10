<template>
    <VueDatePicker
        v-model="modelValue"
        :inputClassName="className"
        menuClassName="shopperz-menu"
        :placeholder="inputStyle == 'filter' ? '' : $t('label.select_date_range')"
        :presetDates="range ? presetDates : []"
        :enableTimePicker="false"
        :autoApply="true"
        :range="range"
        utc="false"
        :teleport="inputStyle == 'filter' ? true : false"
    >
        <template #input-icon>
            <i class="lab-line-calendar"></i>
            <i class="lab-line-chevron-down"></i>
        </template>
        <template #clear-icon="{ clear }"><i @click="clear" class="lab-line-cross"></i></template>
        <template #preset-date-range-button="{ label, value, presetDate }">
            <button type="button" @click="presetDate(value)" @keyup.enter.prevent="presetDate(value)"
                @keyup.space.prevent="presetDate(value)"
                class="text-xs font-medium px-2 py-2 rounded-md tracking-wide capitalize text-center bg-gray-100">
                {{ label }}
            </button>
        </template>
    </VueDatePicker>
</template>


<script>
import { endOfMonth, endOfYear, startOfMonth, startOfYear, startOfWeek, endOfWeek, subWeeks, subMonths, subYears } from 'date-fns';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    props: {
        inputStyle: {
            type: String,
            required: true,
            validator: (propValue) => ['box', 'read', 'filter'].includes(propValue)
        },
        range: {
            type: Boolean
        }
    },
    components: {
        VueDatePicker
    },
    data() {
        return {
            modelValue: null,
            presetDates: [
                {
                    label: 'Today',
                    value: [new Date(), new Date()],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Week',
                    value: [startOfWeek(subWeeks(new Date(), 1)), endOfWeek(subWeeks(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'This Month',
                    value: [startOfMonth(new Date()), endOfMonth(new Date())],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Month',
                    value: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'This Year',
                    value: [startOfYear(new Date()), endOfYear(new Date())],
                    slot: 'preset-date-range-button'
                },
                {
                    label: 'Last Year',
                    value: [startOfYear(subYears(new Date(), 1)), endOfYear(subYears(new Date(), 1))],
                    slot: 'preset-date-range-button'
                },
            ]
        }
    },
    computed: {
        className(state) {
            if (state.inputStyle == 'box') {
                return 'shopperz-input box'
            }
            else if (state.inputStyle == 'read') {
                const date = new Date();
                const startDate = new Date(date.getFullYear(), date.getMonth(), 1);
                const endDate = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                this.modelValue = [startDate, endDate];
                return 'shopperz-input read'
            }
            else if (state.inputStyle == 'filter') {
                return 'shopperz-input filter'
            }
            else return 'shopperz-input'
        }
    },
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
          BOX STYLE CSS
=================================*/
.dp__main:has(.box) {
    max-width: 260px;
}

.dp__main:has(.box) .dp__input {
    text-align: left;
    border-radius: 8px;
    padding: 0px 38px;
    line-height: 40px;
    height: 42px;
    border-color: rgb(var(--primary) / 50%);
}

.dp__main:has(.box) .dp__input_icon {
    flex-direction: row;
    padding: 0px 12px;
}

.dp__main:has(.box) .dp__input_icon i {
    font-size: 18px;
}

.dp__main:has(.box) .dp__clear_icon {
    inset-inline-end: 8px;
}

.dp__main:has(.box) .dp__clear_icon i {
    padding: 2px;
    background-color: white;
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

/*===============================
          FILTER STYLE CSS
=================================*/
.dp__main:has(.filter) {
    max-width: 100%;
}

.dp__main:has(.filter) .dp__input {
    text-align: left;
    border-radius: 8px;
    padding: 0px 38px;
    line-height: 40px;
    height: 42px;
    border-color: rgb(229 231 235);
    color: rgb(86 106 127);
}

.dp__main:has(.filter) .dp__input_icon {
    display: none;
    padding-inline: 12px;
}


.dp__main:has(.filter) .dp__clear_icon {
    inset-inline-end: 8px;
}

.dp__main:has(.filter) .dp__clear_icon i {
    padding: 2px;
    color: rgb(86 106 127);
    background-color: white;
}
</style>
