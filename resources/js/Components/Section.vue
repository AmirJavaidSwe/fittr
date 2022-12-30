<script setup>
const props = defineProps({
    bg: {
        type: String,
        default: 'bg-white shadow-xl'
    }
});
</script>

<template>
    <div class="sm:rounded-lg p-4 mb-4" :class="bg">
        <slot />
    </div>
</template>
