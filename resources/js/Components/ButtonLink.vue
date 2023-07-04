<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        default: null,
    },
    styling: {
        type: String,
        default: "link",
    },
    size: {
        type: String,
        default: "none",
    },
});

const classes = computed(() => {
    let common =
        "inline-flex items-center disabled:opacity-25 transition font-semibold";

    switch (props.styling) {
        case "primary":
            common +=
                "  bg-primary-500 hover:bg-primary-100 focus:bg-primary-100 active:bg-primary-600 text-white hover:text-primary-500 focus:text-primary-500 active:text-white rounded-md border border-primary-500 hover:bg-primary-100";
            break;

        case "secondary":
            common +=
                " bg-secondary-500 hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-secondary-500 hover:bg-secondary-100 hover:text-secondary-500 focus:text-secondary-500";
            break;

        case "danger":
            common +=
                " bg-danger-500 hover:bg-danger-100 focus:bg-danger-100 active:bg-danger-600 text-white active:text-white rounded-md border border-danger-500 hover:text-danger-500 hover:bg-danger-100 focus:text-danger-500";
            break;

        case "default":
            common +=
                " bg-white rounded-md border border-grey hover:border-secondary-500 hover:bg-secondary-100 hover:text-secondary-500 focus:bg-secondary-100 focus:text-secondary-500 active:bg-white active:text-secondary-500 active:border-secondary-500";
            break;

        case "blank":
            common += " text-primary-500 hover:text-primary-900";
            break;

        default: //link
            common += " text-primary-500 hover:text-primary-900";
            break;
    }
    switch (props.size) {
        case "small":
            common += " px-3 h-9 text-sm";
            break;
        case "default":
            common += " px-4 h-11 text-sm";
            break;
        case "medium":
            common += " px-4 h-14 text-md";
            break;
        case "large":
            common += " px-5 h-16 text-md";
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
