<script setup>
import { computed } from "vue";
const props = defineProps({
    percent: {
        type: Number,
        required: true,
    },
    title: {
        type: String,
        required: false,
    },
    subtitle: {
        type: String,
        required: false,
    },
    animate: {
        type: Boolean,
        default: true,
    },
});

const strokeDasharray = computed(() => {
    return props.percent + ' ' + Math.round(100 - props.percent);
});

</script>
<template>
  <svg width="100%" height="100%" viewBox="0 0 40 40">
    <circle class="stroke-gray-100" cx="20" cy="20" r="17" fill="transparent" stroke-width="2" stroke=""></circle>
    <circle class="stroke-primary-300" :style="animate ? {'animation': 'donut 1s'} : ''" cx="20" cy="20" r="17" fill="transparent" stroke-width="2" :stroke-dasharray="strokeDasharray" stroke-dashoffset="25"></circle>
    <g dominant-baseline="middle" text-anchor="middle" class="fill-primary-300">
      <text v-if="title" y="50%" x="50%" transform="translate(0, 1)" class="font-bold text-xs tracking-tight">
        {{title}}
      </text>
      <text v-if="subtitle" y="70%" x="50%" class="font-medium text-[0.25rem]">
        {{subtitle}}
      </text>
    </g>
  </svg>
</template>

<style>
@keyframes donut {
    0% {
        stroke-dasharray: 0, 100;
    }
    100% {
        stroke-dasharray: strokeDasharray;
    }
}
</style>