<script setup>
    defineProps({
        modelValue: [String, Number],
        options: Array,
        option_value: String,
        option_text: String,
    });
    defineEmits(['update:modelValue'])
</script>

<template>
    <select
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        >
        <option v-for="option in options"
                :key="option[option_value]"
                :value="option[option_value]">
            {{ option[option_text] }}
        </option>
    </select>
</template>