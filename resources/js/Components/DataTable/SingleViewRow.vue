<script setup>
let props = defineProps({
    label: {
        type: String,
        required: false,
    },
    value: {
        type: [String, Number],
        required: false,
    },
});

</script>
<template>
    <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 odd:bg-white even:bg-gray-50">
        <dt class="text-sm font-medium text-gray-500">
            <slot name="label"></slot>
            {{ label }}
        </dt>
        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
            <slot name="value"></slot>
            {{ value }}
        </dd>
    </div>
</template>
