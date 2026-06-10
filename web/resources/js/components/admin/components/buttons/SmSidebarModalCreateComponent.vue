<template>
    <button
        class="h-8 px-3 flex items-center gap-2 text-xs tracking-wide capitalize rounded-md shadow text-white bg-primary"
        @click.prevent="openCanvas('sidebar')">
        <i class="lab lab-line-add-circle"></i>
        <span>{{ props.title }}</span>
    </button>
</template>

<script>

import { useCanvas } from "../../../../composables/canvas";

export default {
    name: "SmSidebarModalCreateComponent",
    props: ['props'],
    data() {
        return {
            openCanvas: useCanvas().openCanvas
        }
    }
}
</script>