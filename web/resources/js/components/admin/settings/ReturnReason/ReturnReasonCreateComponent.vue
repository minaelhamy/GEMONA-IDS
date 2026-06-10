<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.return_reasons") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-6">
                            <label for="title" class="db-field-title required">
                                {{ $t("label.title") }}
                            </label>
                            <input v-model="props.form.title" v-bind:class="errors.title ? 'invalid' : ''" type="text"
                                id="title" class="db-field-control" />
                            <small class="db-field-alert" v-if="errors.title">
                                {{ errors.title[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active"
                                            type="radio" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status" type="radio"
                                            id="inactive" class="custom-radio-field" />
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>
                            </div>
                            <small class="db-field-alert" v-if="errors.status">
                                {{ errors.status[0] }}
                            </small>
                        </div>

                        <div class="form-col-12 sm:form-col-12">
                            <label for="details" class="db-field-title ">
                                {{ $t("label.details") }}
                            </label>
                            <textarea v-model="props.form.details" v-bind:class="errors.details ? 'invalid' : ''" 
                                id="details" class="db-field-control" ></textarea>
                            <small class="db-field-alert" v-if="errors.details">
                                {{ errors.details[0] }}
                            </small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save"></i>
                                    <span>{{ $t("button.save") }}</span>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";

export default {
    name: "ReturnReasonCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent, statusEnum },
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
              return {title: this.$t("button.add_return_reason")}
        }
    },
    methods: {
        
        reset: function () {
            appService.modalHide();
            this.$store.dispatch("returnReason/reset").then().catch();
            this.errors = {};
            this.$props.props.form = {
                title: "",
                details: "",
                status: statusEnum.ACTIVE,
            };
        },

        save: function () {
            try {
                const tempId = this.$store.getters["returnReason/temp"].temp_id;
                this.loading.isActive = true;
                this.$store.dispatch("returnReason/save", this.props).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip(
                        tempId === null ? 0 : 1,
                        this.$t("menu.return_reasons")
                    );
                    this.props.form = {
                        title: "",
                        details: "",
                        status: statusEnum.ACTIVE,
                    };
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
    },
};
</script>
