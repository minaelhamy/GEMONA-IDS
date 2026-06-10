<template>
    <div>
        <p class="text-sm text-gray-700">
            {{
                $t("message.pagination_label", {
                    from: props.page.from ?? 0, to: props.page.to ?? 0,
                    total: props.page.total
                })
            }}
        </p>
    </div>
</template>

<script>
export default {
    name: "PaginationTextComponent",
    props: ['props'],
}
</script>
