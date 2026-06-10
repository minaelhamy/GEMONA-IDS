
<template>
    <button
        class="db-table-action edit"
        @click.prevent="openCanvas('sidebar')">
        <i class="lab lab-line-edit"></i><span class="db-tooltip">{{ $t("button.edit") }}</span>
    </button>
</template>

<script>

import { useCanvas } from "../../../../composables/canvas";

export default {
    name: "SmIconSidebarModalEditComponent",
    data() {
        return {
            openCanvas: useCanvas().openCanvas
        }
    }
}
</script>