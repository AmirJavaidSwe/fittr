<script setup>
import { onMounted, ref } from "vue";
import {
    Switch,
    SwitchDescription,
    SwitchGroup,
    SwitchLabel,
} from "@headlessui/vue";

defineEmits(["update:modelValue"]);
const props = defineProps({
    modelValue: {
        type: [Boolean, Number],
        default: false,
    },
    title: {
        type: String,
        default: "Title",
    },
    description: {
        type: String,
        default: null,
    },
    descriptionLeftBorder: {
        type: Boolean,
        default: null,
        required: false,
    },
    showLabels: {
        type: Array,
        default: [],
        required: false,
    },
});

const enabled = ref(props.modelValue);
</script>
<template>
    <SwitchGroup as="div" class="flex items-center justify-between">
        <span class="flex flex-grow flex-col">
            <SwitchLabel
                as="span"
                class="cursor-pointer text-sm font-bold leading-6 text-gray-900"
            >
                {{ title }}
            </SwitchLabel>
            <SwitchDescription as="span" class="text-sm text-gray-500">
                <span v-if="props.descriptionLeftBorder" class="mt-1 mr-2 border-l-[3px] border-primary-500 rounded-md border-t-[4px]"></span>
                {{ description }}
            </SwitchDescription>
        </span>
        <span v-if="props.showLabels.length" class="text-sm font-bold text-gray-500 mr-2">{{ props.showLabels[0] }}</span>
        <Switch
            v-slot="enabled"
            @update:modelValue="$emit('update:modelValue', $event)"
            class="switcher-button"
        >
            <span
                aria-hidden="true"
                :class="[
                    modelValue ? 'translate-x-5 bg-primary-500' : '-translate-x-1 bg-gray-50 border-2 switcher-nob',
                    'switcher-nob',
                ]"
            />
        </Switch>
        <span v-if="props.showLabels.length" class="text-sm font-bold text-gray-500 ml-2">{{ props.showLabels[1] }}</span>
    </SwitchGroup>
</template>
