<template>
    <section class="sm:pt-6 pb-24">
        <div class="container max-w-3xl">
            <div class="flex flex-col-reverse sm:flex-row items-center justify-center gap-10 sm:gap-16 md:gap-40">
                <div class="text-center sm:ltr:text-left sm:rtl:text-right">
                    <h2 class="text-5xl font-bold mb-2">
                        {{ $t('number.403') }}
                    </h2>
                    <h3 class="text-2xl font-medium capitalize mb-3">{{ $t('message.access_denied') }}</h3>
                    <p class="text-lg leading-6 font-medium w-full max-w-[290px] mb-6 text-paragraph">
                        {{ $t('message.you_try_to_access') }}
                    </p>
                    <button @click.prevent="goBack" class="py-3 px-12 rounded-full font-semibold capitalize bg-primary text-white">
                        {{ $t('button.go_back') }}
                    </button>
                </div>
                <div>
                    <img :src="setting.image_403" alt="status" class="w-full">
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import router from "../../router";

export default {
    name: "ExceptionComponent",
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    methods: {
        goBack: function () {
            router.go(-1);
        }
    }
}
</script>
