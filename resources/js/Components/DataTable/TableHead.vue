<template>
    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{ title }}</th>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    }
})
</script>
