<template>
    <aside id="mobile-sidebar-canvas"
        class="fixed inset-0 z-30 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div
            class="w-full max-w-xs h-dvh overflow-x-hidden overflow-y-auto bg-white duration-500 transition-all ltr:-translate-x-full rtl:translate-x-full">
            <div class="py-4 flex items-center justify-between px-4 border-b border-slate-100">
                <router-link :to="{ name: 'frontend.home' }"
                    class="router-link-active router-link-exact-active flex-shrink-0">
                    <img class="w-28 sm:w-32" :src="setting.theme_logo" alt="logo">
                </router-link>

                <button type="button">
                    <i @click.prevent="hideTarget('mobile-sidebar-canvas', 'canvas-active')"
                        class="lab-line-circle-cross text-xl text-danger"></i>
                </button>
            </div>

            <div class="px-3 pb-10">
                <nav class="flex flex-col">
                    <router-link v-on:click="hideTarget('mobile-sidebar-canvas', 'canvas-active')"
                        class="text-base font-medium capitalize py-3 border-b border-slate-100 text-heading"
                        :to="{ name: 'frontend.home' }">
                        {{ $t("label.home") }}
                    </router-link>

                    <router-link v-on:click="hideTarget('mobile-sidebar-canvas', 'canvas-active')"
                        class="text-base font-medium capitalize py-3 border-b border-slate-100 text-heading"
                        :to="{ name: 'frontend.offers' }">
                        {{ $t("label.offers") }}
                    </router-link>

                    <router-link v-on:click="hideTarget('mobile-sidebar-canvas', 'canvas-active')"
                        v-if="pages.length > 0" v-for="page in pages" :key="page"
                        :to="{ name: 'frontend.page', params: { slug: page.slug } }"
                        class="text-base font-medium capitalize py-3 border-b border-slate-100 text-heading">{{
                            page.title }}</router-link>
                </nav>

                <div v-if="setting.site_language_switch === enums.activityEnum.ENABLE">
                    <button type="button"
                        class="language-toggle flex items-center justify-start text-left gap-2 py-3 w-full border-b border-slate-100"
                        @click="colspanHideShow($event, 'mobile-language-colspan')">
                        <img :src="language.image" alt="language" class="w-4 h-4 rounded-full" />
                        <span class="font-medium capitalize flex-auto text-left text-heading">{{ language.name }}</span>
                        <i class="lab-fill-arrow-down text-sm font-bold transition-all duration-500"></i>
                    </button>

                    <ul id="mobile-language-colspan"
                        class="w-full flex flex-col gap-2 mb-3 transition-all duration-500 h-0 overflow-hidden">
                        <li v-for="(LoopLanguage, index) in languages" :key="index"
                            @click.prevent="changeLanguage(LoopLanguage.id, LoopLanguage.code, LoopLanguage.display_mode)"
                            class="flex items-center gap-3 px-2.5 py-2 rounded-lg w-full cursor-pointer bg-slate-100">
                            <img :src="LoopLanguage.image" alt="flags" class="w-4 flex-shrink-0" />
                            <span class="text-sm font-medium capitalize flex-auto text-heading">{{ LoopLanguage.name
                                }}</span>
                        </li>
                    </ul>
                </div>

                <h4 class="text-base font-bold capitalize mb-3 !text-heading">
                    {{ $t('label.contact') }}</h4>
                <ul class="flex flex-col gap-5">
                    <li class="flex gap-3">
                        <i class="lab-fill-location text-sm flex-shrink-0 !text-heading"></i>
                        <span class="text-sm font-medium !text-heading">{{ setting.company_address }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="lab-fill-mail text-sm flex-shrink-0 !text-heading"></i>
                        <span class="text-sm font-medium !text-heading">{{ setting.company_email }}</span>
                    </li>
                    <li class="flex gap-3">
                        <i class="lab-fill-calling text-sm flex-shrink-0 !text-heading"></i>
                        <span class="text-sm font-medium !text-heading">{{ setting.company_phone }}</span>
                    </li>
                </ul>

                <h4 class="text-base font-bold capitalize mt-3 mb-3 !text-heading">{{ $t('label.follow_us') }}</h4>
                <nav class="flex flex-wrap items-center gap-6 mb-7"
                    v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube">
                    <a v-if="setting.social_media_facebook" target="_blank" :href="setting.social_media_facebook"
                        class="lab-fill-facebook w-7 h-7 !leading-7 text-center rounded-full text-sm !text-heading bg-slate-200 transition-all duration-300 hover:text-white hover:bg-primary"></a>
                    <a v-if="setting.social_media_twitter" target="_blank" :href="setting.social_media_twitter"
                        class="lab-fill-x w-7 h-7 !leading-7 text-center rounded-full text-sm !text-heading bg-slate-200 transition-all duration-300 hover:text-white hover:bg-primary"></a>
                    <a v-if="setting.social_media_instagram" target="_blank" :href="setting.social_media_instagram"
                        class="lab-fill-instagram w-7 h-7 !leading-7 text-center rounded-full text-sm !text-heading bg-slate-200 transition-all duration-300 hover:text-white hover:bg-primary"></a>
                    <a v-if="setting.social_media_youtube" target="_blank" :href="setting.social_media_youtube"
                        class="lab-fill-youtube w-7 h-7 !leading-7 text-center rounded-full text-sm !text-heading bg-slate-200 transition-all duration-300 hover:text-white hover:bg-primary"></a>
                </nav>
                <p class="text-xs font-medium !text-heading">{{ setting.site_copyright }}</p>
            </div>
        </div>
    </aside>
</template>

<script>
import targetService from "../../../services/targetService";
import activityEnum from "../../../enums/modules/activityEnum";

export default {
    name: "FrontendMobileSideBarComponent",
    data() {
        return {
            enums: {
                activityEnum: activityEnum
            },
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        },
        language: function () {
            return this.$store.getters['frontendLanguage/show'];
        },
        languages: function () {
            return this.$store.getters['frontendLanguage/lists'];
        },
        pages: function () {
            return this.$store.getters['frontendPage/lists'];
        }
    },
    methods: {
        hideTarget: function (id, cClass) {
            targetService.hideTarget(id, cClass);
        },
        colspanHideShow: function (e, targetId) {
            targetService.colspanHideShow(e, targetId)
        },
        changeLanguage: function (id, code, mode) {
            this.defaultLanguage = id;
            this.$store.dispatch("globalState/set", {
                language_id: id,
                language_code: code,
                display_mode: mode
            }).then(res => {
                this.$store.dispatch('frontendLanguage/show', id).then(res => {
                    this.$i18n.locale = res.data.data.code;
                }).catch();
            }).catch();
        },
    }
}
</script>