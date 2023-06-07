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
        type: Boolean,
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
                {{ description }}
            </SwitchDescription>
        </span>
        <Switch
            v-slot="enabled"
            @update:modelValue="$emit('update:modelValue', $event)"
            class="switcher-button"
        >
            <span
                aria-hidden="true"
                :class="[
                    modelValue ? 'translate-x-5' : '-translate-x-1',
                    'switcher-nob',
                ]"
            />
        </Switch>
    </SwitchGroup>
</template>
