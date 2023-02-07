<script setup>
const props = defineProps({
    bg: {
        type: String,
        default: 'shadow-xl'
    }
});
</script>

<template>
    <div class="sm:rounded-lg p-4" :class="bg">
        <slot />
    </div>
</template>
