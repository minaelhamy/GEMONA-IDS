<template>
    <dl class="relative">
        <dt class="capitalize text-lg font-bold mb-1">{{ $t('message.payment_information') }}</dt>
        <dd class="text-sm font-medium capitalize text-text">
            {{ $t('message.select_your_payment_method' ) }}
        </dd>
    </dl>
</template>

<script>
export default {
    name: "HeaderComponent",
}
</script>