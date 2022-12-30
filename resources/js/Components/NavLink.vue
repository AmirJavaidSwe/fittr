<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    let common = 'inline-flex w-full items-center px-1 py-2 rounded text-sm gap-4';
    return common + ' ' + (props.active
        ? 'bg-white text-gray-900 transition font-semibold'
        : 'hover:bg-gray-200 text-gray-500 hover:text-gray-900 font-medium transition');
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
