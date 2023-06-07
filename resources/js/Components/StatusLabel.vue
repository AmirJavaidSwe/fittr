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
        class="px-3 py-2 inline-flex text-xs lg:text-16vw font-semibold rounded-full"
    >
        {{ status }}
    </span>
</template>
