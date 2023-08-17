<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: String,
    active: Boolean,
});
</script>

<template>
    <Link 
        :href="href"
        :class="{'text-white': props.active, 'text-white/50 ': !props.active}"
        class="flex gap-4 w-full items-center text-md font-semibold transition-colors hover:text-white  ">
        <slot />
    </Link>
</template>
