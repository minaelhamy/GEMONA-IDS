<template>
    <div v-if="categories.length > 0" class="p-0 m-0 flex flex-col gap-1">
        <div v-for="category in categories" :key="category.id || category.slug" class="m-0">
            <router-link :to="{ name: 'frontend.product', query: {category : category.slug} }"
                         class="block text-sm capitalize py-1 transition-all duration-300 hover:text-primary hover:underline" :class="!icon ? 'font-medium' : ''">
                <i v-if="icon" class="lab lab-line-chevron-right"></i>
                {{ category.name }}
            </router-link>
            <MenuChildrenComponent :icon="true" class="ml-3" v-if="category.children && category.children.length > 0" :categories="category.children"/>
        </div>
    </div>
</template>
<script>
export default {
    name: "MenuChildrenComponent",
    props: {
        categories: {
            type: Array,
            required: true
        },
        icon: {
            type: Boolean,
            default: false
        },
    },
}
</script>
