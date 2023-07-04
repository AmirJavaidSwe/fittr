<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: String,
    type: {
        type: String,
        default: "submit",
    },
});

//const classes = `inline-flex items-center justify-center px-3 lg:px-4 py-3 lg:py-4 bg-primary-500 rounded-md font-semibold text-sm lg:text-md text-white capitalize border border-primary-500 hover:bg-primary-100 hover:shadow-md hover:shadow-primary-100 hover:text-primary-500 focus:bg-primary-100 focus:shadow-md focus:shadow-primary-100 focus:text-primary-500 active:bg-primary-600 active:text-white active:shadow-none disabled:opacity-25 transition `;
const classes = `items-center disabled:opacity-25 transition font-semibold bg-secondary-500 hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-secondary-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm`;
</script>

<template>
    <template v-if="href">
        <Link :href="href" :class="classes"><slot /></Link>
    </template>
    <template v-else>
        <button :type="type" :class="classes">
            <slot />
        </button>
    </template>
</template>
