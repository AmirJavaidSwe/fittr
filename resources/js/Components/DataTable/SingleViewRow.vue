<script setup>

import {computed} from "vue";

const css = {
    even: 'bg-white',
    odd: 'bg-gray-50',
}

let props = defineProps({
    even: {
        type: Boolean,
        required: false,
        default: true,
    },
    label: {
        type: String,
        required: false,
    },
    value: {
        type: [String, Number],
        required: false,
    },
})

let isEven = computed(() => props.even)

</script>
<template>
    <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
         :class="isEven ? css.even : css.odd">
        <dt class="text-sm font-medium text-gray-500">
            <slot name="label"></slot>
            {{ label }}
        </dt>
        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
            <slot name="value"></slot>
            {{ value }}
        </dd>
    </div>
</template>
