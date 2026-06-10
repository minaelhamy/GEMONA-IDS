<template>
    <dl class="relative">
        <dt class="capitalize text-lg font-bold mb-1">{{ $t('message.provide_your_shipping_information')}}</dt>
        <dd class="text-sm font-medium capitalize text-text">
            {{ $t('message.check_your_information_before_you_continue' )}}
        </dd>
    </dl>
</template>

<script>

export default {
    name: "HeaderComponent"
}
</script>