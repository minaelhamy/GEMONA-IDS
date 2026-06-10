<template>
    <dl class="relative">
        <dt class="capitalize text-lg font-bold mb-1">{{ $t('label.your_shipping_cart') }}</dt>
        <dd class="text-sm font-medium capitalize text-text">
            ({{ carts.length }})
            {{ carts.length > 1 ? $t('label.products') : $t('label.product') }}
        </dd>
    </dl>
</template>

<script>

export default {
    name: "HeaderComponent",
    computed: {
        carts: function () {
            return this.$store.getters['frontendCart/lists'];
        }
    }
}
</script>