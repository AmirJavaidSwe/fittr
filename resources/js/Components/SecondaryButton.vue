<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-3 lg:px-16vw py-3 lg:py-16vw bg-white rounded-md font-semibold text-sm lg:text-18vw text-dark capitalize border border-grey hover:border-secondary-500 hover:bg-secondary-100 hover:shadow-md hover:shadow-secondary-300 hover:text-secondary-500 focus:bg-secondary-100 focus:shadow-md focus:shadow-secondary-300 focus:text-secondary-500 active:bg-white active:text-secondary-500 active:border-secondary-500 active:shadow-none disabled:opacity-25 transition"
    >
        <slot />
    </button>
</template>
