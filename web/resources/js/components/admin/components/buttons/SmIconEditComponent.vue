<template>
    <router-link  class="db-table-action edit" :to="{ name: this.$props.link, params: { id: this.$props.id } }">
        <i class="lab lab-line-edit"></i>
        <span class="db-tooltip">{{ $t('button.edit') }}</span>
    </router-link>
</template>

<script>
export default {
    name: "SmIconEditComponent",
    props: {
        link: String,
        id: Number
    }
}
</script>

<style scoped>

</style>
