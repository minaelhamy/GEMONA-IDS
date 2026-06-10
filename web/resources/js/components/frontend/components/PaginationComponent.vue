<template>
    <RenderLessPagination :data="data" :limit="limit" :keep-length="keepLength" @pagination-change-page="onPaginationChangePage" v-slot="slotProps">
        <nav v-bind="$attrs" aria-label="Pagination" v-if="slotProps.computed.total > slotProps.computed.perPage" class="flex items-center justify-center gap-4 mobile:mb-12">
            <button :disabled="!slotProps.computed.prevPageUrl" :class="slotProps.computed.prevPageUrl ? 'hover:text-white hover:bg-primary' : ''" v-on="slotProps.prevButtonEvents" class="h-10 leading-10 px-4 rounded-full font-medium capitalize bg-gray-100 transition-all duration-500">
                <slot name="prev-nav">
                    {{ $t('label.previous') }}
                </slot>
            </button>

            <button :class="slotProps.computed.currentPage === page ? 'bg-primary text-white' : ''" :aria-current="slotProps.computed.currentPage ? 'page' : null" v-for="(page, key) in slotProps.computed.pageRange" :key="key" v-on="slotProps.pageButtonEvents(page)" class="w-10 h-10 leading-10 rounded-full font-medium capitalize text-center transition-all duration-500 hover:text-white hover:bg-primary hidden sm:block bg-gray-100">
                {{ page }}
            </button>

            <button :disabled="!slotProps.computed.nextPageUrl" :class="slotProps.computed.nextPageUrl ? 'hover:text-white hover:bg-primary' : ''" v-on="slotProps.nextButtonEvents" class="h-10 leading-10 px-4 rounded-full font-medium capitalize bg-gray-100 transition-all duration-500">
                <slot name="next-nav">
                    {{ $t('label.next') }}
                </slot>
            </button>
        </nav>
    </RenderLessPagination>
</template>

<script>
import RenderLessPagination from 'laravel-vue-pagination/src/RenderlessPagination.vue';

export default {
    name: "PaginationComponent",
    inheritAttrs: false,
    emits: ['pagination-change-page'],
    components: {
        RenderLessPagination
    },
    props: {
        data: {
            type: Object,
            default: () => {}
        },
        limit: {
            type: Number,
            default: 0
        },
        keepLength: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            activeClass : [
                "bg-primary",
                "text-white"
            ]
        }
    },
    methods: {
        onPaginationChangePage(page) {
            this.$emit('pagination-change-page', page);
        }
    }
}
</script>
