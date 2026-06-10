<template>
    <LoadingComponent :props="loading" />

    <footer class="pt-12 bg-secondary mobile:hidden">
        <div class="container">
            <div class="row">
                <div class="col-12 md:col-4 lg:col-5 mb-6 md:mb-0">
                    <div class="tablet:text-center tablet:mx-auto w-full max-w-xs">
                        <router-link :to="{ name: 'frontend.home' }">
                            <img class="mb-8 w-36" :src="setting.theme_footer_logo" alt="logo">
                        </router-link>

                        <form @submit.prevent="saveSubscription" class="mt-5 mb-6 block">
                            <label class="mb-3 font-medium text-white">
                                {{ $t('message.subscribe_to_our_newsletter') }}
                            </label>
                            <div class="flex w-full h-10 rounded-3xl p-1 bg-white">
                                <input type="email" v-model="subscriptionProps.post.email"
                                    :placeholder="$t('label.your_email_address')" class="w-full h-full pl-3 pr-2">
                                <button type="submit"
                                    class="text-xs font-semibold capitalize flex-shrink-0 px-3 h-full rounded-3xl bg-primary text-white">
                                    {{ $t('button.subscribe') }}
                                </button>
                            </div>
                        </form>
                        <nav v-if="setting.social_media_facebook || setting.social_media_twitter || setting.social_media_instagram || setting.social_media_youtube"
                            class="flex flex-wrap items-center gap-6 tablet:justify-center">
                            <a v-if="setting.social_media_facebook" target="_blank"
                                :href="setting.social_media_facebook"
                                class="lab-fill-facebook w-7 h-7 !leading-7 text-center rounded-full text-sm text-secondary bg-white transition-all duration-300 hover:text-white hover:bg-primary"></a>
                            <a v-if="setting.social_media_twitter" target="_blank" :href="setting.social_media_twitter"
                                class="lab-fill-x w-7 h-7 !leading-7 text-center rounded-full text-sm text-secondary bg-white transition-all duration-300 hover:text-white hover:bg-primary"></a>
                            <a v-if="setting.social_media_instagram" target="_blank"
                                :href="setting.social_media_instagram"
                                class="lab-fill-instagram w-7 h-7 !leading-7 text-center rounded-full text-sm text-secondary bg-white transition-all duration-300 hover:text-white hover:bg-primary"></a>
                            <a v-if="setting.social_media_youtube" target="_blank" :href="setting.social_media_youtube"
                                class="lab-fill-youtube w-7 h-7 !leading-7 text-center rounded-full text-sm text-secondary bg-white transition-all duration-300 hover:text-white hover:bg-primary"></a>
                        </nav>
                    </div>
                </div>
                <div class="col-12 md:col-8 lg:col-7">
                    <div class="row">
                        <div class="col-6 sm:col-4 mb-4 sm:mb-0">
                            <h4 class="text-[22px] font-semibold capitalize mb-6 text-white">{{ $t('label.support') }}
                            </h4>
                            <nav v-if="supportPages.length > 0" class="flex flex-col gap-4">
                                <router-link v-for="supportPage in supportPages"
                                    class="w-fit text-sm font-medium capitalize text-white transition-all duration-300 hover:text-primary"
                                    :to="{ name: 'frontend.page', params: { slug: supportPage.slug } }">
                                    {{ supportPage.title }}
                                </router-link>
                            </nav>
                        </div>
                        <div class="col-6 sm:col-4 mb-4 sm:mb-0">
                            <h4 class="text-[22px] font-semibold capitalize mb-6 text-white">{{ $t('label.legal') }}
                            </h4>
                            <nav v-if="legalPages.length > 0" class="flex flex-col gap-4">
                                <router-link v-for="legalPage in legalPages"
                                    class="w-fit text-sm font-medium capitalize text-white transition-all duration-300 hover:text-primary"
                                    :to="{ name: 'frontend.page', params: { slug: legalPage.slug } }">
                                    {{ legalPage.title }}
                                </router-link>
                            </nav>
                        </div>
                        <div class="col-12 sm:col-4">
                            <h4 class="text-[22px] font-semibold capitalize mb-6 text-white">
                                {{ $t('label.contact') }}</h4>
                            <ul class="flex flex-col gap-5">
                                <li class="flex gap-3">
                                    <i class="lab-fill-location text-sm flex-shrink-0 text-white"></i>
                                    <span class="text-sm font-medium text-white">{{ setting.company_address }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <i class="lab-fill-mail text-sm flex-shrink-0 text-white"></i>
                                    <span class="text-sm font-medium text-white">{{ setting.company_email }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <i class="lab-fill-calling text-sm flex-shrink-0 text-white"></i>
                                    <span class="text-sm font-medium text-white">{{ setting.company_phone }}</span>
                                </li>
                            </ul>

                            <dl class="mt-6">
                                <dd class="flex gap-3">
                                    <a target="_blank" class="router-link-active router-link-exact-active"
                                        v-if="setting.site_android_app_link" :href="setting.site_android_app_link">
                                        <img class="h-8 rounded-lg" :src="setting.image_play_store" alt="app">
                                    </a>
                                    <a target="_blank" class="router-link-active router-link-exact-active"
                                        v-if="setting.site_ios_app_link" :href="setting.site_ios_app_link">
                                        <img class="h-8 rounded-lg" :src="setting.image_app_store" alt="app">
                                    </a>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-4 pb-24 lg:py-4 mt-8 text-center border-t border-white/5">
            <p class="text-xs font-medium text-white">{{ setting.site_copyright }}</p>
        </div>
    </footer>
</template>


<script>
import statusEnum from "../../../enums/modules/statusEnum";
import axios from "axios";
import alertService from "../../../services/alertService";
import LoadingComponent from "../../frontend/components/LoadingComponent";
import menuSectionEnum from "../../../enums/modules/menuSectionEnum";
import _ from "lodash";

export default {
    name: "FrontendFooterComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            legalPages: [],
            supportPages: [],
            enums: {
                statusEnum: statusEnum,
                menuSectionEnum: menuSectionEnum
            },
            subscriptionProps: {
                post: {
                    email: ""
                }
            },
            errors: {},
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.$store.dispatch("frontendPage/lists", {
            paginate: 0,
            order_column: "id",
            order_type: "asc",
            status: this.enums.statusEnum.ACTIVE
        }).then(res => {
            if (res.data.data.length > 0) {
                _.forEach(res.data.data, (page) => {
                    if (page.menu_section_id === this.enums.menuSectionEnum.LEGAL) {
                        this.legalPages.push(page);
                    } else {
                        this.supportPages.push(page);
                    }
                });
            }
            this.loading.isActive = false;
        }).catch((err) => {
            this.loading.isActive = false;
        });
    },
    methods: {
        saveSubscription: function () {
            try {
                const url = '/frontend/subscriber';
                this.loading.isActive = true;
                axios.post(url, this.subscriptionProps.post).then(res => {
                    this.loading.isActive = false;
                    this.subscriptionProps.post.email = "";
                    this.errors = {};
                    alertService.success(this.$t("message.subscribe"));
                }).catch((err) => {
                    this.loading.isActive = false;
                    alertService.error(err.response.data.errors.email[0]);
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        }
    }
}
</script>
