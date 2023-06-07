<script setup>
import { computed, useSlots } from "vue";
import SectionTitle from "./SectionTitle.vue";

defineEmits(["submitted"]);
const props = defineProps({
    modal: Boolean,
});

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
    <div class="flex flex-col h-full gap-4">
        <SectionTitle class="w-full">
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="flex flex-1 w-full">
            <form @submit.prevent="$emit('submitted')" class="h-full w-full">
                <div v-if="modal" class="flex flex-col h-full">
                    <div class="flex-1">
                        <div class="space-y-4">
                            <slot name="form" />
                        </div>
                    </div>
                    <div
                        v-if="hasActions"
                        class="flex items-center justify-end mt-5"
                    >
                        <slot name="actions" />
                    </div>
                </div>
                <template v-else>
                    <div
                        class="px-4 py-5 bg-white sm:p-6 shadow"
                        :class="
                            hasActions
                                ? 'sm:rounded-tl-md sm:rounded-tr-md'
                                : 'sm:rounded-md'
                        "
                    >
                        <div class="space-y-4">
                            <slot name="form" />
                        </div>
                    </div>

                    <div
                        v-if="hasActions"
                        class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md"
                    >
                        <slot name="actions" />
                    </div>
                </template>
            </form>
        </div>
    </div>
</template>
