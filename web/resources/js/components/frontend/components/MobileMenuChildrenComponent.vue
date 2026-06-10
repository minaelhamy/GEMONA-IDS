<template>
    <div v-if="categories.length > 0" :id="'mobile_category_' + parentCategory.slug"
        class="w-full h-dvh max-w-xs fixed top-0 ltr:left-0 rtl:right-0 z-40 ltr:-translate-x-full rtl:translate-x-full overflow-y-auto bg-white transition-all duration-500">
        <div class="flex items-center gap-4 py-5 px-4 border-b border-slate-100">
            <button @click.prevent="hideTarget('mobile_category_' + parentCategory.slug, '!translate-x-0')"
                type="button">
                <i class="lab-line-chevron-left text-xl font-bold !leading-8"></i>
            </button>
            <router-link v-on:click="hideTarget('mobile-category-canvas', 'canvas-active')"
                :to="{ name: 'frontend.product', query: { category: parentCategory.slug } }"
                class="text-xl font-bold capitalize">
                {{ parentCategory.name }}
            </router-link>
            <button type="button" class="ltr:ml-auto">
                <i @click.prevent="hideTarget('mobile-category-canvas', 'canvas-active')"
                    class="lab-line-circle-cross text-xl text-danger"></i>
            </button>
        </div>

        <ul v-if="categories.length > 0" class="px-4">
            <li v-for="category in categories" class="border-b border-slate-100">
                <div class="flex items-center justify-between py-3">
                    <router-link v-on:click="hideTarget('mobile-category-canvas', 'canvas-active')"
                        :to="{ name: 'frontend.product', query: { category: category.slug } }"
                        class="text-lg font-semibold capitalize">{{ category.name }}</router-link>
                    <button v-if="category.children.length > 0"
                        @click.prevent="showTarget('mobile_category_' + category.slug, '!translate-x-0')" type="button">
                        <i
                            class="lab-line-chevron-right w-8 h-8 !leading-8 text-center rounded bg-primary/10 text-primary"></i>
                    </button>
                </div>
                <MobileMenuChildrenComponent :key="category" v-if="category.children" :parentCategory="category"
                    :categories="category.children" />
            </li>
        </ul>
    </div>
</template>

<script>
import targetService from "../../../services/targetService";

export default {
    name: "MobileMenuChildrenComponent",
    props: {
        categories: {
            type: Array,
            required: true
        },
        parentCategory: {
            type: Object,
            required: true
        }
    },
    computed: {
        setting: function () {
            return this.$store.getters['frontendSetting/lists'];
        }
    },
    methods: {
        showTarget: function (id, cClass) {
            targetService.showTarget(id, cClass);
        },
        hideTarget: function (id, cClass) {
            targetService.hideTarget(id, cClass);
        }
    }
}
</script>