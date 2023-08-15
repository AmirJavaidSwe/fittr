<script setup>
defineProps({
    value: String,
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block font-bold text-sm text-gray-700">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required"
            class="text-base text-danger-600 px-1"
            v-tooltip="'This field is required.'"
        >*</span>
    </label>
</template>
