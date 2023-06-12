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
            common += "w-6 h-6 2xl:w-8 2xl:h-8 text-sm lg:text-md 2xl:text-lg";
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
