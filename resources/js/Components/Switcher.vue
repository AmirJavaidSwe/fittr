<script setup>
import {onMounted, ref} from 'vue'
import { Switch, SwitchDescription, SwitchGroup, SwitchLabel } from '@headlessui/vue';

defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Title',
    },
    description: {
        type: String,
        default: null,
    },
});

const enabled = ref(props.modelValue);

</script>
<template>
    <SwitchGroup as="div" class="flex items-center justify-between">
        <span class="flex flex-grow flex-col">
            <SwitchLabel as="span" class="cursor-pointer text-sm font-bold leading-6 text-gray-900">
                {{ title }}
            </SwitchLabel>
            <SwitchDescription as="span" class="text-sm text-gray-500">
                {{ description }}
            </SwitchDescription>
        </span>
        <Switch v-slot="enabled" @update:modelValue="$emit('update:modelValue', $event)"
                :class="[modelValue ? 'bg-green-600' :
                    'bg-gray-200', 'relative inline-flex h-6 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 ' +
                    'border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-1 ' +
                    'focus:ring-green-600 focus:ring-opacity-25']">
            <span aria-hidden="true"
                :class="[modelValue ? 'translate-x-6' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform ' +
                'rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
        </Switch>
    </SwitchGroup>
</template>