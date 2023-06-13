<script setup>
const props = defineProps({
    status: String,
    default: "Active",
});
</script>

<template>
    <span
        :class="
            status === 'Active'
                ? 'text-green-800 bg-green-100'
                : 'text-red-800 bg-red-100'
        "
        class="px-3 py-2 inline-flex text-xs lg:text-sm 2xl:text-md font-semibold rounded-full"
    >
        {{ status }}
    </span>
</template>
