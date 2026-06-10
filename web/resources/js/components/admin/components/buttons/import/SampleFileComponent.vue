<template>
    <a href="#" @click.prevent="sampleFileDownload" class="db-card-filter-dropdown-menu">
        <i class="lab lab-line-sample-file lab-font-size-15"></i>
        {{ $t('button.sample_file') }}
    </a>
</template>

<script>
export default {
    name: "SampleFileComponent",
    props: {
        method: { type: Function }
    },
    methods: {
        sampleFileDownload: function () {
            this.method();
        }
    }

}
</script>

<style scoped></style>