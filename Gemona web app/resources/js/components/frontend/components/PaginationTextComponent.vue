<template>
    <p class="first-letter:capitalize">
        {{ $t('label.showing') }}
        <b>{{ props.page.from ?? 0 }}</b>
        {{ $t('label.to') }}
        <b>{{ props.page.to ?? 0 }}</b>
        {{ $t('label.of') }}
        <b>{{ props.page.total }}</b>
        {{ $t('label.results') }}
    </p>
</template>

<script>
export default {
    name: "PaginationTextComponent",
    props: ['props'],
}
</script>
