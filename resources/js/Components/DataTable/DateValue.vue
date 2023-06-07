<script setup>
import Calendar from "@/Icons/Calendar.vue";
import TimeIcon from "@/Icons/Time.vue";

const props = defineProps({
    date: String,
    isTime: Boolean,
});
</script>

<template>
    <div class="font-bold text-primary-500 flex items-center">
        <span v-if="!isTime" class="text-grey mr-2"><Calendar /></span>
        <span v-else class="text-grey mr-2"><TimeIcon /></span>
        {{ date }}
    </div>
</template>
