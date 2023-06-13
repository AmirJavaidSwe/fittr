<script setup>
const props = defineProps({
    color: {
        type: String,
        default: "grey",
    },
    title: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div class="flex items-center">
        <span
            class="w-3 h-3 rounded-full mr-2"
            :style="'background-color:' + color + ';'"
        ></span>
        {{ title }}
    </div>
</template>
