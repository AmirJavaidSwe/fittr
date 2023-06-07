<script setup>
import { computed } from "vue";

const props = defineProps({
    title: String,
    size: {
        type: String,
        default: "medium",
    },
});

const classes = computed(() => {
    let common =
        "bg-blue p-1 text-white rounded-full flex items-center justify-center text-center uppercase font-semibold ";

    switch (props.size) {
        case "small":
            common += "w-6 h-6 lg:w-33vw lg:h-33vw text-sm lg:text-16vw";
            break;
        case "medium":
            common += "w-8 h-8 lg:w-48vw lg:h-48vw text-md lg:text-20vw";
            break;
        case "large":
            common += "w-12 h-12 lg:w-60vw lg:h-60vw text-lg lg:text-30vw";
            break;

        default:
            break;
    }

    return common;
});

const handleUsersInitials = (name) => {
    var parts = name.split(" ");
    var initials = "";
    for (var i = 0; i < parts.length; i++) {
        if (parts[i].length > 0 && parts[i] !== "") {
            initials += parts[i][0];
        }
    }
    return initials;
};
</script>

<template>
    <div :class="classes">
        {{ handleUsersInitials(title) }}
    </div>
</template>
