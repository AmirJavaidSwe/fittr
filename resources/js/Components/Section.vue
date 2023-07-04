<script setup>
const props = defineProps({
    bg: {
        type: String,
        default: 'shadow-xl'
    }
});
</script>

<template>
    <div class="sm:rounded-lg bg-mainBg">
        <slot />
    </div>
</template>
