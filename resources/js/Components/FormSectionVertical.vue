<script setup>
import { computed, useSlots } from "vue";

defineEmits(["submitted"]);
const props = defineProps({
    modal: Boolean,
});

const hasActions = computed(() => !!useSlots().actions);

</script>

<template>
    <div class="bg-white shadow rounded mt-5">
        <div class="md:grid md:grid-rows-1 md:grid-flow-col">
            <slot name="tabsList" />
            <div class="col-span-11 border-l-2 border-gray-200 p-5">
                <slot name="heading" />
                <form @submit.prevent="$emit('submitted')" class="h-full w-full">
                    <slot name="form" />
                    <div
                        v-if="hasActions"
                        class="flex items-center justify-end mt-5"
                    >
                        <slot name="actions" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
