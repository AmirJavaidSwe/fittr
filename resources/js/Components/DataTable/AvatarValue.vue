<script setup>
import Avatar from "../Avatar.vue";

const props = defineProps({
    title: String,
});
</script>

<template>
    <span class="flex items-center">
        <Avatar :title="title" size="small" />
        <span class="ml-2">{{ title }}</span>
    </span>
</template>
