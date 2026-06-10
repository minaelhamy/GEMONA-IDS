<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" @click.self="reset"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div class="w-full max-w-xl h-screen overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t('menu.product_sections') }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text" id="name"
                            class="db-field-control" />
                        <small class="db-field-alert" v-if="errors.name">{{
                            errors.name[0]
                        }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.status" id="active"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field" checked />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active" class="db-field-label">{{
                                    $t("label.active")
                                }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status" id="inactive"
                                        :value="enums.statusEnum.INACTIVE" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="inactive" class="db-field-label">{{
                                    $t("label.inactive")
                                }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="submit" class="db-btn py-2 text-white bg-primary">
                                <i class="lab lab-fill-save"></i>
                                <span>{{ $t("label.save") }}</span>
                            </button>

                            <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                <i class="lab lab-fill-close-circle"></i>
                                <span>{{ $t("button.close") }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</template>
<script>
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import LoadingComponent from "../components/LoadingComponent";
import statusEnum from "../../../enums/modules/statusEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import { useCanvas } from "../../../composables/canvas";

export default {
    name: "ProductSectionCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
            },
            errors: {},
        };
    },
    computed: {
        addButton: function () {
              return {title: this.$t("button.add_product_section")}
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.$store.dispatch("productSection/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                status: statusEnum.ACTIVE,
            };
        },

        save: function () {
            try {
                const tempId = this.$store.getters["productSection/temp"].temp_id;
                this.loading.isActive = true;
                this.$store
                    .dispatch("productSection/save", {
                        form: this.props.form,
                        search: this.props.search,
                    })
                    .then((res) => {
                        useCanvas().closeCanvas('sidebar');
                        this.loading.isActive = false;
                        alertService.successFlip(
                            tempId === null ? 0 : 1,
                            this.$t("menu.product_sections")
                        );
                        this.props.form = {
                            name: "",
                            status: statusEnum.ACTIVE,
                        };
                        this.errors = {};
                    })
                    .catch((err) => {
                        this.loading.isActive = false;
                        this.errors = err.response.data.errors;
                    });
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>
