<script setup>
import MultiSelectRemoveIcon from '@/Icons/MultiSelectRemoveIcon.vue';
import Multiselect from '@vueform/multiselect';

defineProps({
    modelValue: Array,
    options: Array,
    mode: String
});
defineEmits(['update:modelValue'])
</script>

<template>
    <Multiselect :mode="mode" :modelValue="modelValue" :options="options" placeholder="Select" @change="$emit('update:modelValue', $event)" />
    <div class="mt-2">
        <span class="inline-block mr-1 px-2 py-1 rounded-2xl text-xs text-black" v-for="(val, index) in modelValue" style="background: rgba(66, 112, 95, 0.24);">
            <span class="inline-block mr-2 align-middle">{{ options.find(item => val == item.value).label }}</span>
            <span class="inline-block align-middle cursor-pointer">
                <MultiSelectRemoveIcon @click="modelValue.splice(index, 1)" />
            </span>
        </span>
    </div>
    <!-- <select
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        >
        <option v-for="option in options"
                :key="option[option_value]"
                :value="option[option_value]">
            {{ option[option_text] }}
        </option>
    </select> -->
</template>