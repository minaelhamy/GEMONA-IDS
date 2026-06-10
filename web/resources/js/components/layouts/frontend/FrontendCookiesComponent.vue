<template>
    <div v-if="status && setting.cookies_summary"  :class="activeClass" 
        class=" translate-y-0 w-full max-w-full sm:max-w-xs p-4 fixed bottom-0 ltr:left-0 rtl:right-0 sm:bottom-6 ltr:sm:left-6 rtl:sm:right-6 z-50 rounded-t-2xl sm:rounded-2xl shadow-cookies bg-white transition-all duration-500">
        <div class="flex items-center gap-2 mb-4">
            <i class="lab-fill-cookie-bite text-heading"></i>
            <h3 class="font-bold capitalize">{{ $t('label.about_our_privacy') }}</h3>
        </div>
        <p class="text-sm mb-4 text-heading">
            {{ setting.cookies_summary }}
        </p>
        <div class="flex items-center gap-4">
            <button @click.prevent="change(false)" type="button"
                class="w-full h-[38px] rounded-r ltr:rounded-l-3xl rtl:rounded-r-3xl tracking-wide text-center font-bold capitalize border border-[#D9DBE9] bg-white text-heading">{{
                    $t('button.decline') }}</button>
            <button @click.prevent="change(true)" type="button"
                class="w-full h-[38px] rounded-l ltr:rounded-r-3xl rtl:rounded-l-3xl tracking-wide text-center font-bold capitalize border border-primary bg-primary text-white">{{
                    $t('button.accept') }}</button>
        </div>
        <router-link v-if="slug !== 'not-found'" class="capitalize text-sm leading-6 underline text-primary"
                     :to="{ name : 'frontend.page', params: {slug: slug} }">
            {{ $t('label.cookies_settings') }}
        </router-link>
    </div>
</template>
<script>
import axios from "axios";

export default {
    name: "FrontendCookiesComponent",
    data() {
        return {
            setting: {},
            status: false,
            activeClass: "",
            slug: "not-found"
        }
    },
    mounted() {
        window.setTimeout(() => {
            axios.get('frontend/cookies').then((res) => {
                if (res.data.data.cookies_notification === null) {
                    this.status = true;
                    this.activeClass = 'active';
                    this.setting = this.$store.getters['frontendSetting/lists'];
                    if (this.setting.cookies_details_page_id > 0) {
                        this.$store.dispatch('frontendPage/pageInfo', this.setting.cookies_details_page_id).then(res => {
                            this.slug = res.data.data.slug;
                        }).catch();
                    }
                }
            }).catch((err) => {
            });
        }, 3000);
    },
    methods: {
        change: function (status) {
            axios.post('frontend/cookies', { cookies_notification: status }).then((res) => {
                this.status = false;
                this.activeClass = '';
            }).catch((err) => {
            });
        }
    }
}
</script>
