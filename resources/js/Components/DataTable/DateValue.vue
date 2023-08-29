<script setup>
import Calendar from "@/Icons/Calendar.vue";
import TimeIcon from "@/Icons/Time.vue";

const props = defineProps({
    date: String,
    isTime: Boolean,
    showCalenderIcon: {
        type: Boolean,
        required: false,
        default: true,
    },
});
</script>

<template>
    <div class="font-bold flex items-center">
        <span v-if="!isTime" class="text-grey mr-2">
            <template v-if="showCalenderIcon">
                <Calendar />
            </template>
        </span>
        <span v-else class="text-grey mr-2"><TimeIcon /></span>
        {{ date }}
        <slot></slot>
    </div>
</template>
