<script setup>
const props = defineProps({
    img: {
        type: String,
        default: '/images/auth-background.png',
    },
});
</script>
<template>
    <div class="relative w-full lg:w-1/2 min-h-screen hidden lg:block bg-top" :style="{ backgroundImage: 'url('+img+')' }">
        <slot />
    </div>
</template>