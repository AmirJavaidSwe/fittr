<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: String,
    type: {
        type: String,
        default: "button",
    },
});

const classes =
    "inline-flex items-center justify-center px-3 lg:px-4 py-3 lg:py-4 bg-secondary-500 rounded-md font-semibold text-sm lg:text-md text-dark capitalize border border-secondary-500 hover:bg-secondary-100 hover:shadow-md hover:shadow-secondary-200 hover:text-secondary-500 focus:bg-secondary-100 focus:shadow-md focus:shadow-secondary-200 focus:text-secondary-500 active:bg-secondary-600 active:text-white active:shadow-none disabled:opacity-25 transition";
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
