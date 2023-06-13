<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    let common =
        "inline-flex w-full items-center px-3 py-2 rounded-lg text-md lg:text-md gap-4";
    return (
        common +
        " " +
        (props.active
            ? "bg-[rgba(255,255,255,0.8)] text-dark transition font-semibold"
            : "hover:bg-[rgba(255,255,255,0.8)] text-white hover:text-dark font-medium transition")
    );
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
