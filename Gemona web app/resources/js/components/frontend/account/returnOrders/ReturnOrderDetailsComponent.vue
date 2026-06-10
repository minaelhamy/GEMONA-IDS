<template>
    <LoadingComponent :props="loading" />

    <div class="flex items-center gap-4 mb-7">
        <button @click.prevent="$router.back()" class="lab-line-undo text-xl font-bold text-primary"></button>
        <h2 class="capitalize text-xl font-bold text-primary">{{ $t('label.return_order_details') }}</h2>
    </div>

    <div class="rounded-2xl shadow-card mb-6 bg-white">
        <h3 class="font-semibold p-4">{{ $t('label.order_id') }}: #{{ returnOrder.order_serial_no }}</h3>
        <div class="mobile:overflow-x-auto">
            <table class="w-full text-left text-sm capitalize">
                <thead class="font-semibold border-b border-t border-gray-200">
                    <tr>
                        <th class="p-4">{{ $t('label.products') }}</th>
                        <th class="p-4">{{ $t('label.quantity') }}</th>
                        <th class="p-4">{{ $t('label.price') }}</th>
                        <th class="p-4">{{ $t('label.total') }}</th>
                    </tr>
                </thead>
                <tbody class="font-medium" v-if="returnProducts.length > 0">
                    <tr v-for="returnProduct in returnProducts" class="group">
                        <td class="p-4 border-b group-last:border-0 border-gray-100">
                            <div class="inline-flex items-center gap-3">
                                <img :src="returnProduct.product_image" alt="product"
                                    class="w-12 h-12 object-cover rounded-md flex-shrink-0" />
                                <div class="overflow-hidden">
                                    <h4 class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis mb-0.5">
                                        {{ returnProduct.product_name }}
                                    </h4>
                                    <p class="text-sm capitalize whitespace-nowrap overflow-hidden text-ellipsis"
                                        v-if="returnProduct.variation_names">
                                        {{ returnProduct.variation_names }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 border-b group-last:border-0 border-gray-100">{{ returnProduct.quantity }}
                        </td>
                        <td class="p-4 border-b group-last:border-0 border-gray-100">{{ returnProduct.currency_price }}</td>
                        <td class="p-4 border-b group-last:border-0 border-gray-100">{{ returnProduct.return_currency_price
                        }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl shadow-card p-4 mb-6 bg-white mobile:mb-20">
        <dl class="mb-5" v-if="returnOrder.return_reason_name">
            <dt class="font-semibold capitalize mb-2">{{ $t('label.return_reason') }}</dt>
            <dd class="text-sm">{{ returnOrder.return_reason_name }}</dd>
        </dl>
        <dl class="mb-5" v-if="returnOrder.note">
            <dt class="font-semibold capitalize mb-2">{{ $t('label.return_note') }}</dt>
            <dd class="text-sm">{{ returnOrder.note }}</dd>
        </dl>
        <dl v-if="returnOrder.images?.length > 0">
            <dt class="font-semibold capitalize mb-3">{{ $t('label.attachment') }}</dt>
            <dd class="inline-flex flex-wrap gap-4">
                <img :src="image" alt="return" class="w-20 h-20 rounded-lg object-cover"
                    v-for="image in returnOrder.images" />
            </dd>
        </dl>
    </div>
    <div class="rounded-2xl shadow-card p-4 bg-white mobile:mb-20">
        <h3 class="capitalize font-semibold mb-2 text-focus">{{ $t('label.return_response') }}</h3>
        <dl class="flex items-center gap-1 mb-3">
            <dt class="capitalize text-sm">{{ $t('label.status') }}:</dt>
            <dd class="capitalize text-sm font-semibold text-success">
                <span :class="returnStatusClass(returnOrder.status)">
                    {{ enums.returnStatusEnumArray[returnOrder.status] }}
                </span>
            </dd>
        </dl>
        <p class="text-sm" v-if="returnOrder.status === enums.returnStatusEnum.REJECTED"> {{ returnOrder.reject_reason }}
        </p>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import returnStatusEnum from "../../../../enums/modules/returnStatusEnum";
import appService from "../../../../services/appService";
export default {
    name: "ReturnOrderDetailsComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                returnStatusEnum: returnStatusEnum,
                returnStatusEnumArray: {
                    [returnStatusEnum.PENDING]: this.$t("label.pending"),
                    [returnStatusEnum.ACCEPT]: this.$t("label.accepted"),
                    [returnStatusEnum.REJECTED]: this.$t("label.rejected"),
                },
            },

        };
    },
    mounted() {
        this.loading.isActive = true;
        if (this.$route.params.id) {
            this.loading.isActive = true;
            this.$store.dispatch("frontendReturnAndRefund/show", this.$route.params.id).then(res => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
            });
        }
    },
    computed: {
        returnOrder: function () {
            return this.$store.getters["frontendReturnAndRefund/show"];
        },
        returnProducts: function () {
            return this.$store.getters['frontendReturnAndRefund/returnProducts'];
        },
    },
    methods: {
        returnStatusClass: function (status) {
            return appService.returnStatusClass(status);
        },
    }
}
</script>
