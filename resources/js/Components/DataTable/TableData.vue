<template>
    <td
        class="whitespace-nowrap border-y-[12px] border-mainBg px-3 py-3 text-sm lg:text-md 2xl:text-lg text-gray-500 bg-white first:border-l-4 first:rounded-s-2xl last:border-r-4 last:rounded-e-2xl"
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

<script setup>
defineProps(["title", "subtitle"]);
</script>
