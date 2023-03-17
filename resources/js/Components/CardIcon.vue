<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    cardLink: String,
    iconClass: String,
});
</script>

<template>
    <div class="bg-gray-50 flex hover:bg-gray-100 items-center p-2 gap-4 relative transition">
        <Link v-if="cardLink" :href="cardLink" class="absolute bottom-0 top-0 w-full cursor-pointer"></Link>
        <div v-if="$slots.icon" class="flex-shrink-0" :class="iconClass">
            <slot name="icon"></slot>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate dark:text-white">
                <slot name="title"></slot>
            </p>
            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                <slot></slot>
            </p>
        </div>
    </div>
</template>
