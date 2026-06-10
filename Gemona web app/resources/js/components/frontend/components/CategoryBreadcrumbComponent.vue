<template>
    <div v-if="categories.length > 0">
        <ul class="flex flex-wrap items-center">
            <li v-for="(category, categoryKey) in categories"
                class="after:content-['\e037'] after:font-icon rtl:after:rotate-180 after:text-sm after:mx-2 last:after:hidden">
                <router-link v-if="(categoryKey + 1) <= Object.keys(categories).length - 1"
                    :to="{ name: 'frontend.product', query: { category: category.slug } }"
                    class="text-base font-medium capitalize transition-all duration-500 hover:text-primary">
                    {{ category.name }}
                </router-link>

                <span v-else class="text-base font-medium capitalize text-text">
                    {{ category.name }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "CategoryBreadcrumbComponent",
    props: {
        "categories": "object"
    }
}

</script>