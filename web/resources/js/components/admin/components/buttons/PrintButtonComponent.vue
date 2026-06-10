<template>
    <button v-print="props" :class="buttonClass">
        <i class="lab-fill-printer"></i>
        {{ $t('button.print') }}
    </button>
</template>

<script>
import print from "vue3-print-nb";

export default {
    name: "PrintButtonComponent",
    props: ["props", "buttonClass"],
    directives: {
        print
    }
}
</script>
