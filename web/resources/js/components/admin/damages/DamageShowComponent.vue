<template>
    <LoadingComponent :props="loading"/>
    <div class="col-12" id="print">
        <div class="db-card p-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                <div class="p-4 rounded-xl border border-gray-100">
                    <i class="lab-fill-file text-2xl text-success mb-2 lab-font-size-24"></i>
                    <h4 class="text-sm capitalize font-semibold text-secondary mb-1.5">
                        {{ $t("label.reference") }}:
                        {{ damage.reference_no }}
                    </h4>
                    <ul class="flex flex-col gap-1">
                        <li class="flex items-start gap-1">
                            <span class="text-sm capitalize">{{ $t("label.date") }}:</span>
                            <span class="text-sm">
                                {{ damage.converted_date }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-6 pt-4" v-if="damage.note">
                <div class="p-4 rounded-xl border border-gray-100">
                    <h4 class="text-sm capitalize font-semibold text-secondary mb-1.5">
                        {{ $t('label.note') }}:
                    </h4>
                    <span v-html="damage.note"></span>
                </div>
            </div>
        </div>
        <div class="db-card overflow-hidden">
            <div class="db-table-responsive">
                <table class="db-table stripe">
                    <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th">
                            {{ $t("label.product") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.unit_cost") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.quantity") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.discount") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.taxes") }}
                        </th>
                        <th class="db-table-head-th">
                            {{ $t("label.sub_total") }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="db-table-body border-b border-gray-200">
                    <tr v-for="(item, index) of damage.products" :key="index" class="db-table-body-tr">
                        <td class="db-table-body-td font-medium">
                            {{ item.product_name }}
                            <span v-if="item.variation_names">
                                ( {{ $t('label.variation') }} : {{ item.variation_names }} )
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            {{ floatFormat(item.price) }}
                        </td>
                        <td class="db-table-body-td">
                            {{ item.quantity }}
                        </td>
                        <td class="db-table-body-td">
                            {{ Number(item.discount) === 0 ? "" : "-" }}
                            {{ floatFormat(item.discount) }}
                        </td>
                        <td class="db-table-body-td">
                            {{ floatFormat(item.tax) }}
                        </td>
                        <td class="db-table-body-td">
                            {{ floatFormat(item.total) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start py-6 px-4">
                <div class="p-4 rounded-xl flex flex-col gap-2.5 bg-gray-100">
                    <dl v-if="damage.creator && damage.creator.name" class="flex items-start gap-1.5">
                        <dt class="text-base font-medium text-secondary">
                            {{ $t('label.created_by') }}:
                        </dt>
                        <dd class="text-base font-medium text-secondary">
                            {{ damage.creator ? damage.creator.name : '' }}
                        </dd>
                    </dl>
                    <dl class="flex items-start gap-1.5">
                        <dt class="text-base font-medium text-secondary">
                            {{ $t('label.created_date') }}:
                        </dt>
                        <dd class="text-base font-medium text-secondary">
                            {{ damage.created_at }}
                        </dd>
                    </dl>
                </div>

                <ul class="w-full flex flex-col gap-3.5 p-4 rounded-xl border border-gray-100">
                    <li class="w-full flex items-center justify-between">
                        <span class="text-base font-semibold text-secondary">{{ $t('label.subtotal') }}</span>
                        <span class="text-base font-semibold text-secondary">{{ damage.subtotal_currency }}</span>
                    </li>
                    <li class="w-full flex items-center justify-between">
                        <span class="text-base font-semibold text-secondary">{{ $t("label.tax_fee") }}</span>
                        <span class="text-base font-semibold text-secondary">{{ damage.tax_currency }}</span>
                    </li>
                    <li class="w-full flex items-center justify-between">
                        <span class="text-base font-semibold text-secondary">{{ $t("label.discount") }}</span>
                        <span class="text-base font-semibold text-secondary">{{ damage.discount_currency }}</span>
                    </li>
                    <li class="w-full flex items-center justify-between">
                        <span class="text-base font-semibold text-secondary">{{ $t("label.total") }}</span>
                        <span class="text-base font-semibold text-secondary">{{ damage.total_currency }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-12 hidden-print">
        <div class="flex items-center justify-end gap-6">
            <button v-if="damage.file" @click="download" type="button"
                    class="flex items-center justify-center gap-1.5 h-10 px-6 rounded-3xl text-white bg-primary">
                <i class="lab lab-fill-download"></i>
                <span class="capitalize text-sm font-medium">{{ $t('label.download') }}</span>
            </button>
            <PrintButtonComponent :props="printObj"
                                  :buttonClass="'flex items-center justify-center gap-1.5 h-10 px-6 rounded-3xl text-white bg-success'"/>
        </div>
    </div>
</template>

<script>
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import PrintButtonComponent from "../components/buttons/PrintButtonComponent";
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "DamageShowComponent",
    components: {
        LoadingComponent,
        PrintButtonComponent,
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            printObj: {
                id: "print",
                popTitle: this.$t("menu.damages"),
            },
        };
    },
    mounted() {
        this.show();
    },
    computed: {
        damage: function () {
            return this.$store.getters["damage/show"];
        },
        setting: function() {
            return this.$store.getters['frontendSetting/lists']
        }
    },
    methods: {
        floatFormat: function (num) {
            return appService.floatFormat(num, this.setting.site_digit_after_decimal_point);
        },
        show: function () {
            if (!isNaN(this.$route.params.id)) {
                this.loading.isActive = true;
                this.$store
                    .dispatch("damage/show", this.$route.params.id)
                    .then((res) => {
                        this.loading.isActive = false;
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                    });
            }
        },
        download: function () {
            if (!isNaN(this.$route.params.id)) {
                this.loading.isActive = true;
                this.$store.dispatch("damage/download", this.$route.params.id).then((res) => {
                    this.loading.isActive = false;

                    let fileType = "";
                    if (res.data.type) {
                        let type = res.data.type;
                        type     = type.split("/");
                        fileType = type[1];
                    }

                    if (res.data.size > 0) {
                        const url     = window.URL.createObjectURL(
                            new Blob([res.data])
                        );
                        const link    = document.createElement("a");
                        link.href     = url;
                        link.download = "" + this.$t("menu.damages") + "." + fileType;
                        link.click();
                        URL.revokeObjectURL(link.href);
                    } else {
                        alertService.info(this.$t("menu.damages") + " " + this.$t('message.attachment_not_found'));
                    }
                }).catch((err) => {
                    this.loading.isActive = false;
                });
            }
        },
    },
};
</script>

<style scoped>
@media print {
    .hidden-print {
        display: none !important;
    }
}
</style>
