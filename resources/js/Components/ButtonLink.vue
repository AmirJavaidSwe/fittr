<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    type: {
        type: String,
        default: 'default'
    }
});

const classes = computed(() => {
    let common = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring disabled:opacity-25 transition';

    switch (props.type) {
        case 'secondary':
            common += ' bg-white border-gray-300 text-gray-700 hover:text-gray-500 focus:border-blue-300 focus:ring-blue-200 active:text-gray-800 active:bg-gray-50';
            break;
        case 'primary':
            common += ' bg-gray-800 border-transparent text-white hover:bg-gray-700 active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300';
            break;
    
        default:
            break;
    }

    return common;
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
