<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    rows: {
        type: [String, Number],
        default: 3,
    },
    cols: {
        type: [String, Number],
        default: 20,
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <textarea
        ref="input"
        :rows="rows"
        :cols="cols"
        class="rounded-md py-1 lg:text-base border border-gray-300 focus:border-[1.5px] focus:border-primary-500 focus:outline-none focus:shadow-none focus:ring-0"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>
</template>
