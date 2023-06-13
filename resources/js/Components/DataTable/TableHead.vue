<template>
    <th
        scope="col"
        class="text-center whitespace-nowrap px-3 lg:px-3 2xl:px-5 py-3.5 lg:py-3 2xl:py-5 text-left text-md 2xl:text-xl font-semibold text-gray-900 cursor-pointer"
        @click="click"
    >
        <div class="flex items-center">
            {{ title }}
            <template v-if="currentSort">
                <div v-if="arrowSide === 'desc'" class="ml-2 text-sm">
                    <font-awesome-icon :icon="faSortDown" class="text-dark" />
                </div>
                <div v-else class="ml-2 text-sm">
                    <font-awesome-icon :icon="faSortUp" class="text-dark" />
                </div>
            </template>
            <template v-else>
                <div
                    v-if="title && arrowSide"
                    class="flex flex-col items-center justify-center ml-2 text-sm opacity-25"
                >
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
        </div>
    </th>
</template>

<script setup>
import { faSortDown, faSortUp } from "@fortawesome/free-solid-svg-icons";

defineProps({
    title: {
        type: String,
        required: true,
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
