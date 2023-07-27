<script setup>
import Avatar from "../Avatar.vue";

const props = defineProps({
    title: String,
    onlyTooltip : {
        type: Boolean,
        required: false,
        default: false
    }
});
</script>

<template>
    <span class="flex items-center">
        <Avatar :title="title" size="small" v-tooltip="title" />
        <span v-if="!onlyTooltip" class="ml-2">{{ title }}</span>
    </span>
</template>
