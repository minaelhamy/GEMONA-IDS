<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" />

    <div id="sidebar" @click.self="reset"
        class="fixed inset-0 z-50 bg-black/50 duration-500 transition-all invisible opacity-0">
        <div class="w-full max-w-xl h-screen overflow-x-hidden thin-scrolling bg-white ms-auto ltr:translate-x-full rtl:-translate-x-full">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t('menu.promotions') }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-12">
                        <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control" />
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
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                        id="inactive" :value="enums.statusEnum.INACTIVE" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="inactive" class="db-field-label">{{
                                    $t("label.inactive")
                                    }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title required">{{ $t("label.type") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.type" id="small"
                                        :value="enums.promotionTypeEnum.SMALL" class="custom-radio-field" checked />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="small" class="db-field-label">{{
                                    $t("label.small")
                                    }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.type" id="big"
                                        :value="enums.promotionTypeEnum.BIG" />
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="big" class="db-field-label">{{
                                    $t("label.big")
                                    }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-12">
                        <label class="db-field-title required">{{ $t("label.image") }} (540px,336px)</label>
                        <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                            class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" />
                        <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
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
import promotionTypeEnum from "../../../enums/modules/promotionTypeEnum";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";
import { useCanvas } from "../../../composables/canvas";

export default {
    name: "PromotionCreateComponent",
    components: { SmSidebarModalCreateComponent, LoadingComponent, Datepicker },
    props: ["props"],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: statusEnum,
                promotionTypeEnum: promotionTypeEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive"),
                },
                promotionTypeEnumArray: {
                    [promotionTypeEnum.SMALL]: this.$t("label.small"),
                    [promotionTypeEnum.BIG]: this.$t("label.big"),
                },
            },
            image: "",
            errors: {},
        };
    },
    computed: {
        addButton: function () {
            return { title: this.$t("button.add_promotion") }
        }
    },
    methods: {
        floatNumber(e) {
            return appService.floatNumber(e);
        },
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            useCanvas().closeCanvas('sidebar');
            this.$store.dispatch("promotion/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                type: promotionTypeEnum.SMALL,
                status: statusEnum.ACTIVE,
            };
            if (this.image) {
                this.image = "";
                this.$refs.imageProperty.value = null;
            }
        },

        save: function () {
            try {
                const fd = new FormData();
                fd.append("name", this.props.form.name);
                fd.append("type", this.props.form.type);
                fd.append("status", this.props.form.status);
                if (this.image) {
                    fd.append("image", this.image);
                }
                const tempId = this.$store.getters["promotion/temp"].temp_id;
                this.loading.isActive = true;
                this.$store
                    .dispatch("promotion/save", {
                        form: fd,
                        search: this.props.search,
                    })
                    .then((res) => {
                        useCanvas().closeCanvas('sidebar');
                        this.loading.isActive = false;
                        alertService.successFlip(
                            tempId === null ? 0 : 1,
                            this.$t("menu.promotions")
                        );
                        this.props.form = {
                            name: "",
                            type: promotionTypeEnum.SMALL,
                            status: statusEnum.ACTIVE,
                        };
                        this.image = "";
                        this.errors = {};
                        this.$refs.imageProperty.value = null;
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