<script setup>
import { faUserCircle } from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed } from "vue";

const props = defineProps({
    title: String,
    size: {
        type: String,
        default: "medium",
    },
    useIcon: {
        type: Boolean,
        default: false
    },
    imageUrl: {
        type: String,
        default: ''
    },
});

const classes = computed(() => {
    let common =
        (props.useIcon || props.imageUrl) ? "text-gray-600 flex items-center justify-center "
        : "bg-blue p-1 text-white rounded-full flex items-center justify-center text-center uppercase font-semibold ";

    switch (props.size) {
        case "small":
            common += "w-7 h-7 2xl:w-9 2xl:h-9 text-sm 2xl:text-lg";
            break;
        case "medium":
            common +=
                "w-10 h-10 2xl:w-12 2xl:h-12 text-md lg:text-md 2xl:text-lg";
            break;
        case "large":
            common +=
                "w-14 h-14 2xl:w-16 2xl:h-16 text-lg lg:text-lg 2xl:text-xl";
            break;

        default:
            break;
    }

    return common;
});

const handleUsersInitials = (name) => {
    if (name !== undefined) {
        var parts = name.split(" ");
        var initials = "";
        for (var i = 0; i < parts.length; i++) {
            if (parts[i].length > 0 && parts[i] !== "") {
                initials += parts[i][0];
            }
        }
        return initials;
    }
};
</script>

<template>
    <div v-if="imageUrl" :class="classes">
        <img class="w-full" :src="imageUrl" />
    </div>
    <FontAwesomeIcon v-else-if="useIcon" :icon="faUserCircle" :class="classes" />
    <div v-else :class="classes">
        {{ handleUsersInitials(title) }}
    </div>
</template>
