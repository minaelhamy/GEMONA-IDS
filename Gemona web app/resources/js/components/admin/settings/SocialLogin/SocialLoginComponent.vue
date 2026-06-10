<template>
    <LoadingComponent :props="loading" />
    <div id="sms" class="db-tab-div active">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 mb-5">
            <button @click="selectActive(index)"
                class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5"
                :data-tab="'#' + loginProvider.slug" v-for="(loginProvider, index) in loginProviders.slice(0, 3)" :key="loginProvider"
                :class="index === selectIndex ? 'active' : ''">
                <span class="capitalize whitespace-nowrap text-[15px]">
                    {{ loginProvider.name }}
                </span>
            </button>

            <div v-if="loginProviders.length > 3" class="dropdown-group w-full">{{ loginProviders }}
                <button
                    class="dropdown-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5">
                    <i class="fa-solid fa-circle-chevron-down text-sm"></i>
                    <span class="capitalize whitespace-nowrap text-[15px]"> {{ $t('label.more_gateway') }}</span>
                </button>
                <div class="w-full dropdown-list absolute top-[42px] right-0 z-30 p-2 rounded-md bg-white shadow-lg">
                    <button @click="selectActive(index + 3)"
                        class="db-tab-sub-btn w-full flex items-center gap-3 h-10 px-4 rounded-lg transition bg-white hover:text-primary hover:bg-primary/5"
                        :data-tab="'#' + loginProvider.slug"
                        v-for="(loginProvider, index) in loginProviders.slice(3, loginProviders.length)" :key="loginProvider"
                        :class="index + 3 === selectIndex ? 'active' : ''">
                        {{ loginProvider.name }}
                    </button>
                </div>
            </div>
        </div>
        <div :id="loginProvider.slug" class="db-card db-tab-sub-div" v-for="(loginProvider, index) in loginProviders"
            :key="loginProvider" :class="index === selectIndex ? 'active' : ''">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ loginProvider.name }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save(index)" :id="'formElem' + index" class="w-full d-block">
                    <div class="form-row">
                        <input type="hidden" :value="loginProvider.slug" name="provider_type">

                        <div class="form-col-12 sm:form-col-6" v-for="loginProviderOption in loginProvider.options"
                            :key="loginProviderOption">
                            <label :for="loginProviderOption.option" class="db-field-title">
                                {{ $t("label." + loginProviderOption.option) }}
                            </label>
                            <input v-if="loginProviderOption.type === enums.inputTypeEnum.TEXT" type="text"
                                :value="loginProviderOption.value"
                                v-bind:class="errors[loginProviderOption.option] ? 'invalid' : ''"
                                :name="loginProviderOption.option" :id="loginProviderOption.option" class="db-field-control" />

                            <select v-else class="db-field-control" :id="loginProviderOption.option"
                                :name="loginProviderOption.option"
                                v-bind:class="errors[loginProviderOption.option] ? 'invalid' : ''">
                                <option :value="index" :selected="index === loginProviderOption.value"
                                    v-for="(activity, index) in loginProviderOption.activities">
                                    {{ $t("label." + activity) }}
                                </option>
                            </select>

                            <small class="db-field-alert" v-if="errors[loginProviderOption.option]">{{
                                errors[loginProviderOption.option][0]
                            }}</small>
                        </div>
                        <div class="form-col-12">
                            <button type="submit" class="db-btn text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>{{ $t("button.save") }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import inputTypeEnum from "../../../../enums/modules/inputTypeEnum";

export default {
    name: "SocialLoginComponent",
    components: { LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false,
            },
            search: {
                paginate: 0,
                order_column: "id",
                order_type: "asc"
            },
            selectIndex: 0,
            enums: {
                inputTypeEnum: inputTypeEnum
            },
            errors: {},
        };
    },
    computed: {
        loginProviders: function () {
            return this.$store.getters["socialLogin/lists"];
        },
    },
    mounted() {
      
            this.loading.isActive = true;
            this.$store.dispatch("socialLogin/lists", this.search).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
       
    },
    methods: {
        save: function (index) {
            try {
                const form = document.getElementById("formElem" + index);
                const formDataObj = {};
                [...form.elements].filter((el) => el.tagName !== 'BUTTON').forEach((item) => {
                    formDataObj[item.name] = item.value;
                });

                this.loading.isActive = true;
                this.$store.dispatch("socialLogin/save", { form: formDataObj, search: this.search }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.social_login"));
                    this.errors = {};
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
        selectActive: function (index) {
            this.selectIndex = index;
        }
    },
};
</script>
