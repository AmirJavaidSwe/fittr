<template>
    <th
        scope="col"
        class="text-center whitespace-nowrap px-3 lg:px-16vw py-3.5 lg:py-16vw text-left text-sm lg:text-20vw font-semibold text-gray-900 cursor-pointer"
        @click="click"
    >
        <div class="flex items-center">
            {{ title }}
            <template v-if="currentSort">
                <div v-if="arrowSide === 'desc'" class="ml-2">
                    <font-awesome-icon :icon="faSortDown" class="text-dark" />
                </div>
                <div v-else class="ml-2">
                    <font-awesome-icon :icon="faSortUp" class="text-dark" />
                </div>
            </template>
            <template v-else>
                <div
                    v-if="title && arrowSide"
                    class="flex flex-col items-center justify-center ml-2 opacity-50"
                >
                    <font-awesome-icon
                        :icon="faSortUp"
                        class="-mb-1.5 lg:-mb-10vw text-dark"
                    />
                    <font-awesome-icon
                        :icon="faSortDown"
                        class="-mt-1.5 lg:-mt-10vw text-dark"
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
