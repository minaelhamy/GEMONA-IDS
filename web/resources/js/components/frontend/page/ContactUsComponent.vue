<template>
    <div class="row mb-7">
        <div class="col-12 sm:col-6 mb-2" v-if="outlets.length > 0" v-for="outlet in outlets">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-6 h-6 rounded-full flex items-center justify-center bg-primary">
                    <i class="lab lab-line-branches text-white !text-xs"></i>
                </span>
                <h3 class="font-medium leading-6">{{ outlet.name }}</h3>
            </div>
            <ul class="flex flex-col gap-2">
                <li class="flex items-center gap-2.5">
                    <i class="lab lab-line-location lab-font-size-14"></i>
                    <span class="text-sm leading-6 text-heading">
                        <span v-if="outlet.address">{{ outlet.address }}</span>
                        <span class="block">
                            <span v-if="outlet.city">{{ outlet.city }},</span>
                            <span v-if="outlet.state">{{ outlet.state }},</span>
                            <span v-if="outlet.country">{{ outlet.country }},</span>
                            <span v-if="outlet.zip_code">{{ outlet.zip_code }}</span>
                        </span>
                    </span>
                </li>
                <li class="flex items-center gap-2.5">
                    <i class="lab lab-line-call-calling lab-font-size-14"></i>
                    <span class="text-sm leading-6 text-heading">{{ outlet.country_code }}{{ outlet.phone }}</span>
                </li>
            </ul>
        </div>
    </div>
    <div>
        <h2 class="text-[22px] leading-[34px] font-medium capitalize mb-3">{{ $t('label.support') }}</h2>
        <ul class="flex flex-col gap-2">
            <li class="flex items-center gap-2.5">
                <i class="lab lab-line-mail lab-font-size-14"></i>
                <span class="text-sm leading-6 text-heading">{{ setting.company_email }}</span>
            </li>
            <li class="flex items-center gap-2.5">
                <i class="lab lab-line-call-calling lab-font-size-14"></i>
                <span class="text-sm font-medium leading-6 text-heading">{{ setting.company_calling_code }}{{ setting.company_phone }}</span>
            </li>
        </ul>
    </div>
</template>

<script>


import statusEnum from "../../../enums/modules/statusEnum";

export default {
    name: "ContactUsComponent",
    computed: {
        outlets: function () {
            return this.$store.getters['frontendOutlet/lists'];
        },
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
    },
    mounted() {
        this.$store.dispatch('frontendOutlet/lists', {order_column: 'id', order_type : 'asc', status : statusEnum.ACTIVE }).then().catch()
    }
}
</script>
