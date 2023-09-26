<script setup>
import { faUserCircle } from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed } from "vue";

const props = defineProps({
    initials: String,
    size: {
        type: String,
        default: "small"
    },
    useIcon: {
        type: Boolean,
        default: false
    },
    imageUrl: {
        type: String,
        default: null
    },
});

const classes = computed(() => {
    let common =
        (props.useIcon || props.imageUrl) ? "text-gray-600 flex items-center justify-center "
        : "bg-primary-400 p-1 text-white rounded-full flex items-center justify-center text-center uppercase font-semibold ";

    switch (props.size) {
        case "xs":
            common += "w-5 h-5 text-sm";
        case "small":
            common += "w-7 h-7 text-sm";
            break;
        case "medium":
            common +=
                "w-10 h-10 text-md";
            break;
        case "large":
            common +=
                "w-14 h-14 text-lg";
            break;

        default:
            break;
    }

    return common;
});

</script>

<template>
    <div v-if="imageUrl" :class="classes">
        <img class="w-full rounded-full object-cover" :src="imageUrl" />
    </div>
    <FontAwesomeIcon v-else-if="useIcon" :icon="faUserCircle" :class="classes" />
    <div v-else :class="classes">
        {{ initials }}
    </div>
</template>
