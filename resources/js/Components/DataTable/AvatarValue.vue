<script setup>
import Avatar from "../Avatar.vue";

const props = defineProps({
    title: String,
    onlyTooltip : {
        type: Boolean,
        required: false,
        default: false
    },
    useIcon: {
        type: Boolean,
        default: false
    },
    imageUrl: {
        type: String,
        default: ''
    },
});
</script>

<template>
    <span class="flex items-center">
        <Avatar :title="title" size="small" v-tooltip="title" :useIcon="useIcon" :imageUrl="imageUrl" />
        <span v-if="!onlyTooltip" class="ml-2">{{ title }}</span>
    </span>
</template>
