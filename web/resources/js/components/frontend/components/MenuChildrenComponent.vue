<template>
    <div class="p-0 m-0">
        <p v-if="categories.length > 0" v-for="category in categories" :key="category">
            <router-link :to="{ name: 'frontend.product', query: {category : category.slug} }"
                         class="text-sm capitalize py-1 transition-all duration-300 hover:text-primary hover:underline" :class="!icon ? 'font-medium' : ''">
                <i v-if="icon" class="lab lab-line-chevron-right"></i>
                {{ category.name }}
            </router-link>
            <MenuChildrenComponent :icon="true" class="ml-3" v-if="category.children" :key="category" :categories="category.children"/>
        </p>
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