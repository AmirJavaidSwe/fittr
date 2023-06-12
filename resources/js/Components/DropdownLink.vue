<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    href: String,
    as: String,
});

const classes = `block px-4 py-2 text-sm lg:text-md 2xl:text-lg w-full text-left leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition flex items-center`;
</script>

<template>
    <div>
        <button v-if="as == 'button'" type="submit" :class="classes">
            <slot />
        </button>

        <a v-else-if="as == 'a'" :href="href" :class="classes">
            <slot />
        </a>

        <Link v-else :href="href" :class="classes">
            <slot />
        </Link>
    </div>
</template>
