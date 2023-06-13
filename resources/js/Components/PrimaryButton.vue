<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: String,
    type: {
        type: String,
        default: "submit",
    },
});

const classes = `inline-flex items-center justify-center px-3 lg:px-4 py-3 lg:py-4 bg-primary-500 rounded-md font-semibold text-sm lg:text-md text-white capitalize border border-primary-500 hover:bg-primary-100 hover:shadow-md hover:shadow-primary-100 hover:text-primary-500 focus:bg-primary-100 focus:shadow-md focus:shadow-primary-100 focus:text-primary-500 active:bg-primary-600 active:text-white active:shadow-none disabled:opacity-25 transition `;
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
