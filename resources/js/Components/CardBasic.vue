<script setup>
import { computed } from "vue";
const props = defineProps({
    width: String,
});
const classes = computed(() => {
    switch (props.width) {
        case "none":
            return "sm:max-w-none";
        case "xs":
            return "sm:max-w-xs";
        case "sm":
            return "sm:max-w-sm";
        case "md":
            return "sm:max-w-md";
        case "lg":
            return "sm:max-w-lg";
        case "xl":
            return "sm:max-w-xl";
        case "2xl":
            return "sm:max-w-2xl";
        case "3xl":
            return "sm:max-w-3xl";

        default:
            return "sm:max-w-full";
    }
});
</script>

<template>
    <div
        class="w-full bg-white border border-gray-200 flex flex-col gap-4 p-3 lg:p-16vw rounded-lg shadow-md"
        :class="classes"
    >
        <div
            v-if="$slots.header"
            class="text-xl lg:text-30vw font-bold tracking-tight text-gray-900"
        >
            <slot name="header"></slot>
        </div>
        <slot></slot>
        <div v-if="$slots.footer">
            <slot name="footer"></slot>
        </div>
    </div>
</template>
