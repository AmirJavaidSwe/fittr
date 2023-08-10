<script setup>
defineProps(["title", "subtitle"]);
</script>
<template>
    <td
        class="whitespace-nowrap border-y border-mainBg px-2 text-sm text-gray-500 bg-white"
    >
        <div class="font-medium text-gray-900">
            <slot></slot>
            {{ title }}
        </div>
        <div v-if="subtitle" class="text-gray-500">
            {{ subtitle }}
        </div>
    </td>
</template>
