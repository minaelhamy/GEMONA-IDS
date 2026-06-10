<template>
    <button type="button" @click="add" :data-modal="'#' + props" class="db-card-filter-dropdown-menu">
        <i class="lab lab-line-upload-file"></i>
        <span>{{ $t('button.upload_file') }}</span>
    </button>
</template>

<script>
import appService from "../../../../../services/appService";

export default {
    name: "UploadFileComponent",
    props: ['props'],
    methods: {
        add: function () {
            appService.modalShow('#' + this.props);
        }
    }
}
</script>

<style scoped></style>