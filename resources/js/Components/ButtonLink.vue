<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    styling: {
        type: String,
        default: 'link'
    },
    size: {
        type: String,
        default: 'none'
    }
});

const classes = computed(() => {
    let common = 'inline-flex items-center disabled:opacity-25 transition  ';

    switch (props.styling) {
        case 'primary':
            common += '  bg-primary-500 hover:bg-primary-100 focus:bg-primary-100 active:bg-primary-600 font-semibold text-white hover:text-primary-500 focus:text-primary-500 active:text-white rounded-md border border-primary-500 hover:bg-primary-100 hover:shadow-md hover:shadow-primary-100 focus:shadow-md focus:shadow-primary-200 active:shadow-none';
            break;

        case 'secondary':
            common += ' bg-secondary-500 hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 font-semibold text-dark active:text-white rounded-md border border-secondary-500 hover:bg-secondary-100 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:shadow-secondary-200 active:shadow-none';
            break;

        case 'default':
            common += ' bg-white hover:bg-primary-100 focus:bg-primary-100 active:bg-primary-600 font-semibold text-primary-500 hover:text-primary-500 focus:text-primary-500 active:text-white rounded-md border border-primary-500 hover:bg-primary-100 hover:shadow-md hover:shadow-primary-100 focus:shadow-md focus:shadow-primary-200 active:shadow-none';
            break;

        case 'blank':
            common += ' text-primary-500 hover:text-primary-900 font-semibold';
            break;

        default: //link
            common += ' font-bold text-primary-500 hover:text-primary-900';
            break;
    }
    switch (props.size) {
        case 'small':
            common += ' px-2 py-0.5';
            break;
        case 'default':
            common += ' px-3 py-1';
            break;
        case 'medium':
            common += ' px-4 py-2.5';
            break;
        case 'large':
            common += ' p-4';
            break;

        default: //none
            break;
    }

    return common;
});
</script>

<template>
    <template v-if="href">
        <Link :href="href" :class="classes">
            <slot />
        </Link>
    </template>
    <template v-else>
        <button :class="classes">
            <slot />
        </button>
    </template>
</template>
