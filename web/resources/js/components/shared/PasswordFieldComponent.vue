<template>
    <div class="relative">
        <input
            :id="id"
            :value="modelValue"
            :type="visible ? 'text' : 'password'"
            :autocomplete="autocomplete"
            :class="[inputClass, invalid ? 'invalid' : '', 'w-full pr-11']"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <button
            type="button"
            class="absolute top-1/2 right-3 -translate-y-1/2 text-[#6E7191] hover:text-primary transition-colors"
            :aria-label="visible ? 'Hide password' : 'Show password'"
            @click="visible = !visible"
        >
            <i :class="visible ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye'"></i>
        </button>
    </div>
</template>

<script>
export default {
    name: "PasswordFieldComponent",
    props: {
        modelValue: {
            type: String,
            default: "",
        },
        id: {
            type: String,
            default: "",
        },
        invalid: {
            type: Boolean,
            default: false,
        },
        inputClass: {
            type: String,
            default: "db-field-control",
        },
        autocomplete: {
            type: String,
            default: "off",
        },
    },
    emits: ["update:modelValue"],
    data() {
        return {
            visible: false,
        };
    },
};
</script>
