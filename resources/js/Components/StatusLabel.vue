<script setup>
import { computed } from "vue";
const props = defineProps({
    status: {
        type: [String, Boolean],
        required: false,
        default: "Active",
    },
    bgColor: {
        type: [String, Boolean],
        required: false,
        default: false,
    }
});

const computedClasses = computed(() => {
    if(props.status === true || props.status === 'Active' || props.status === 'Yes') {
        return 'text-green-800 bg-green-100';
    }
    else if(props.bgColor && !(props.status === true || props.status === 'Active' || props.status === 'Yes')) {
        return 'text-secondary-800 bg-secondary-100';
    } else {
        return 'text-red-800 bg-red-100'
    }
})
</script>

<template>
    <span
        :class="computedClasses"
        class="px-2 py-1 inline-flex text-xs lg:text-sm font-semibold rounded-full"
    >
        {{ status }}
    </span>
</template>
