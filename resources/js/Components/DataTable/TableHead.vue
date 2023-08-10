<script setup>
import { faSortDown, faSortUp } from "@fortawesome/free-solid-svg-icons";

defineProps({
    title: {
        type: String,
        required: false,
        default: '',
    },
    click: {
        type: Function,
    },
    arrowSide: {
        type: String,
    },
    currentSort: {
        type: Boolean,
    },
});
</script>
<template>
    <th
        scope="col"
        class="text-center whitespace-nowrap p-2 text-md font-semibold text-gray-900 cursor-pointer bg-gray-200 ">
        <!-- :class="[ title ? 'px-3 2xl:px-5' : 'pl-3.5 pr-5']" -->
        <div class="flex items-center justify-between">
            <template v-if="title">
                {{ title }}
            </template>
            <slot></slot>
            <template v-if="currentSort">
                <div v-if="arrowSide === 'desc'" class="ml-2 text-sm">
                    <font-awesome-icon :icon="faSortDown" class="text-dark" />
                </div>
                <div v-else class="ml-2 text-sm">
                    <font-awesome-icon :icon="faSortUp" class="text-dark" />
                </div>
            </template>
            <template v-else-if="arrowSide">
                <div class="flex flex-col items-center justify-center ml-2 text-sm opacity-25">
                    <font-awesome-icon
                        :icon="faSortUp"
                        class="-mb-1.5 text-dark"
                    />
                    <font-awesome-icon
                        :icon="faSortDown"
                        class="-mt-1.5 text-dark"
                    />
                </div>
            </template>
            <template v-else>
                <slot name="checkbox"></slot>
            </template>
        </div>
    </th>
</template>
