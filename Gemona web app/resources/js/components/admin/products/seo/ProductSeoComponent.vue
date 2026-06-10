<template>
    <LoadingComponent :props="loading" />
    <div class="db-card-header">
        <h3 class="db-card-title">{{ $t('label.seo') }}</h3>
    </div>
    <div class="db-card-body">
        <form @submit.prevent="save" class="d-block w-full">
            <div class="form-row">
                <div class="form-col-12 sm:form-col-12">
                    <label for="title" class="db-field-title required">
                        {{ $t("label.title") }}
                    </label>
                    <input v-model="form.title" v-bind:class="errors.title ? 'invalid' : ''" type="text" id="title"
                        class="db-field-control" />
                    <small class="db-field-alert" v-if="errors.title">{{ errors.title[0] }}</small>
                </div>

                <div class="form-col-12">
                    <label for="description" class="db-field-title required">
                        {{ $t("label.description") }}
                    </label>
                    <div :class="errors.description ? 'invalid textarea-error-box-style' : ''">
                        <quill-editor id="description" v-model:value="form.description"
                            class="!h-40 textarea-border-radius" />
                    </div>
                    <small class="db-field-alert" v-if="errors.description">{{
                        errors.description[0]
                    }}</small>
                </div>

                <div class="form-col-12 sm:form-col-12">
                    <label for="meta_keyword" class="db-field-title required">{{ $t("label.meta_keyword") }}</label>
                    <div>
                        <vue-tags-input id="meta_keyword" v-bind:class="errors.meta_keyword ? 'invalid-tag' : ''"
                            placeholder="" v-model="tag" :tags="tags" @tags-changed="newTags => tags = newTags" />
                    </div>
                    <small class="db-field-alert" v-if="errors.meta_keyword">{{ errors.meta_keyword[0] }}</small>
                </div>

                <div class="form-col-12 sm:form-col-12">
                    <label for="image" class="db-field-title">
                        {{ $t("label.image") }}
                    </label>
                    <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                        class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg" />
                    <small class="db-field-alert" v-if="errors.image">{{
                        errors.image[0]
                    }}</small>

                    <img class="w-[120px] h-[120px] object-fill rounded-lg mt-2" alt="logo" v-if="preview" :src="preview" />
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
</template>

<script>
import alertService from "../../../../services/alertService";
import LoadingComponent from "../../components/LoadingComponent";
import VueTagsInput from "@sipec/vue3-tags-input";
import { quillEditor } from 'vue3-quill'
import _ from 'lodash';

export default {
    name: "ProductSeoComponent",
    components: {
        VueTagsInput,
        LoadingComponent,
        quillEditor
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            tag: "",
            tags: [],
            form: {
                title: "",
                description: "",

            },
            productId: null,
            image: "",
            errors: {},
            preview: "",
        }
    },
    mounted() {
        this.loading.isActive = true;
        this.productId = this.$route.params.id;
        this.list();

    },
    methods: {
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        list: function () {
            this.$store.dispatch('productSeo/lists', this.productId).then(res => {
                this.preview = res.data.data.thumb;
                this.form = {
                    title: res.data.data.title,
                    description: res.data.data.description,
                };
                this.tags = res.data.data.meta_keyword.length !== 0 ? this.seoTag(res.data.data.meta_keyword) : [];
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                alertService.error(err.response.data.message);
            });
        },
        seoTag: function (objects) {
            objects = JSON.parse(objects);
            let seoTags = [];
            _.forEach(objects, (object) => {
                seoTags.push({ "text": object, "tiClasses": ["ti-valid"] });
            });
            return seoTags;
        },
        save: function () {
            try {
                const fd = new FormData();
                fd.append('product_id', this.productId);
                fd.append('title', this.form.title);
                fd.append('description', this.form.description);
                fd.append('meta_keyword', this.tags.length === 0 ? "" : JSON.stringify(this.tags.map(item => item.text)));
                if (this.image) {
                    fd.append('image', this.image);
                }
                this.loading.isActive = true;
                this.$store.dispatch('productSeo/save', {
                    form: fd,
                    productId: this.productId
                }).then((res) => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        res.config.method === "post" ?? 0,
                        this.$t("menu.seo")
                    );
                    this.errors = {};
                    this.$refs.imageProperty.value = null;
                    this.list();
                }).catch((err) => {
                    this.loading.isActive = false;
                    this.errors = err.response.data.errors;
                })
            } catch (err) {
                this.loading.isActive = false;
            }
        }

    }
}
</script>
