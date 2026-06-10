<template>
    <div v-if="menuTemplateId === 1">
        <ContactUsComponent />
    </div>
</template>

<script>

import ContactUsComponent from "../page/ContactUsComponent";
export default {
    name: "TemplateManagerComponent",
    props: ["menuTemplateId"],
    components: { ContactUsComponent },
}
</script>
