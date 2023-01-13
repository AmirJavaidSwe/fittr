<script setup>
const props = defineProps({
    index: {
        type: Number,
    },
    on: {
        type: Number,
    }
});
</script>

<template>
<div v-if="index == on">
    <slot></slot>
</div>
</template>