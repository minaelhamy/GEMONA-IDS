

<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="modal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("menu.product_attribute_options") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form class="d-block w-full" @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12 sm:form-col-12">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-fill-close-circle"></i>
                                    <span>{{ $t('button.close') }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-fill-save"></i>
                                    <span>{{ $t('button.save') }}</span>
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

import appService from "../../../../services/appService";
import alertService from "../../../../services/alertService";
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";

export default {
    name: "ProductAttributeCreateComponent",
    props: ['props'],
    components: { SmModalCreateComponent, LoadingComponent },
    data() {
        return {
            loading: {
                isActive: false
            },
            errors: {},
        }
    },
    computed: {
        addButton: function () {
              return {title: this.$t("button.add_product_attribute_option")}
        }
    },
    methods: {
        reset: function () {
            appService.modalHide();
            this.$store.dispatch('productAttributeOption/reset').then().catch();
            this.errors = {};
            this.$props.props.form = {
                name: "",
            }
        },
        save: function () {
            try {
                this.loading.isActive = true;
                this.$store.dispatch('productAttributeOption/save', this.props).then((res) => {
                    appService.modalHide();
                    this.loading.isActive = false;
                    alertService.successFlip((res.config.method === 'put' ?? 0), this.$t('label.product_attribute_option'));
                    this.props.form = {
                        name: "",
                    }
                    this.errors = {}
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            }
        }
    }
}
</script>

<style scoped></style>
