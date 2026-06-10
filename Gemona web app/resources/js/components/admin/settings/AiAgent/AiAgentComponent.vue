<template>
    <LoadingComponent :props="loading" />
     <div id="ai-agent" class="db-tab-div active">
        <div class="grid gap-3 mb-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <button @click="selectActive(index)"
                class="flex items-center w-full h-10 gap-3 px-4 transition bg-white rounded-lg db-tab-sub-btn hover:text-primary hover:bg-primary/5"
                :data-tab="'#' + aiAgent.slug" v-for="(aiAgent, index) in aiAgents.slice(0, 3)"
                :key="aiAgent" :class="index === selectIndex ? 'active' : ''">
                <span class="capitalize whitespace-nowrap text-[15px]">
                    {{ aiAgent.name }}
                </span>
            </button>

            <div v-if="aiAgents.length > 3" class="w-full dropdown-group">
                <button
                    class="flex items-center w-full h-10 gap-3 px-4 transition bg-white rounded-lg dropdown-btn hover:text-primary hover:bg-primary/5">
                    <i class="text-sm fa-solid fa-circle-chevron-down"></i>
                    <span class="capitalize whitespace-nowrap text-[15px]"> {{ $t('label.more_ai_agent') }}</span>
                </button>
                <div class="w-full dropdown-list absolute top-[42px] right-0 z-30 p-2 rounded-md bg-white shadow-lg">
                    <button @click="selectActive(index + 3)"
                        class="db-tab-sub-btn w-full flex items-center whitespace-nowrap justify-start my-0.5 gap-2.5 pl-3 pr-6 py-1.5 text-sm rounded-md capitalize transition text-gray-500 hover:text-primary hover:bg-primary/5"
                        :data-tab="'#' + aiAgent.slug"
                        v-for="(aiAgent, index) in aiAgents.slice(3, aiAgents.length)"
                        :key="aiAgent" :class="index + 3 === selectIndex ? 'active' : ''">
                        {{ aiAgent.name }}
                    </button>
                </div>
            </div>
        </div>
        <div :id="aiAgent.slug" class="db-card db-tab-sub-div" v-for="(aiAgent, index) in aiAgents"
            :key="aiAgent" :class="index === selectIndex ? 'active' : ''">
            <div class="db-card-header">
                <h3 class="db-card-title">{{ aiAgent.name }}</h3>
            </div>
            <div class="db-card-body">
                <form @submit.prevent="save(index)" :id="'formElem' + index" class="w-full d-block">
                    <div class="form-row">
                        <input type="hidden" :value="aiAgent.slug" name="ai_agent_type">

                        <div class="form-col-12 sm:form-col-6" v-for="aiAgentOption in aiAgent.options"
                            :key="aiAgentOption">
                            <label :for="aiAgentOption.option" class="db-field-title">
                                {{ $t("label." + aiAgentOption.option) }}
                            </label>
                            <input v-if="aiAgentOption.type === enums.inputTypeEnum.TEXT" type="text"
                                :value="aiAgentOption.value"
                                v-bind:class="errors[aiAgentOption.option] ? 'invalid' : ''"
                                :name="aiAgentOption.option" :id="aiAgentOption.option"
                                class="db-field-control" />

                            <select v-else class="db-field-control" :id="aiAgentOption.option"
                                :name="aiAgentOption.option"
                                v-bind:class="errors[aiAgentOption.option] ? 'invalid' : ''">
                                <option :value="index" :selected="index === aiAgentOption.value"
                                    v-for="(activity, index) in aiAgentOption.activities">
                                    {{ $t("label." + activity) }}
                                </option>
                            </select>

                            <small class="db-field-alert" v-if="errors[aiAgentOption.option]">{{
                                errors[aiAgentOption.option][0]
                            }}</small>
                        </div>
                        <div class="form-col-12">
                            <button type="submit" :id="'formButton' + index" class="text-white db-btn bg-primary">
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
import alertService from "../../../../services/alertService";
import inputTypeEnum from "../../../../enums/modules/inputTypeEnum";

export default {
    name: "AiAgentComponent",
    components: { LoadingComponent },
    data() {
         return {
            loading: {
                isActive: false,
            },
            search: {
                paginate    : 0,
                order_column: "id",
                order_type  : "asc",
            },
            selectIndex: 0,
            enums: {
                inputTypeEnum: inputTypeEnum
            },
            errors: {},
        };
    },
    computed: {
        aiAgents: function () {
            return this.$store.getters['aiAgent/lists'];
        }
    },
    mounted() {
        this.list();
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
                this.$store.dispatch("aiAgent/save", { form: formDataObj, search: this.search }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(res.config.method === "put" ?? 0, this.$t("menu.ai_agent"));
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
        list: function () {
            this.loading.isActive = true;
            this.$store.dispatch('aiAgent/lists', this.search).then(res => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        handlePaper(event) {
            const currGroup  = event.currentTarget.parentElement
            const isActivate = currGroup.className.includes('active')
            if (!isActivate) currGroup.classList.add('active')
            else currGroup.classList.remove('active')
        },
         selectActive: function (index) {
            this.selectIndex = index;
        },
    }
};
</script>
