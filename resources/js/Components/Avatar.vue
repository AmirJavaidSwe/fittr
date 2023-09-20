<script setup>
import { faUserCircle } from "@fortawesome/free-regular-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { computed } from "vue";

const props = defineProps({
    title: String,
    size: {
        type: String,
        default: "small",
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
