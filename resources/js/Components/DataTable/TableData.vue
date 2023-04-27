<template>
    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
        <div class="font-medium text-gray-900">
            <slot></slot>
            {{ title }}
        </div>
        <div v-if="subtitle" class="text-gray-500">
            {{ subtitle }}
        </div>
    </td>
</template>

<script setup>
    defineProps(['title', 'subtitle']);
</script>
