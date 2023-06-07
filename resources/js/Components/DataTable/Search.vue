<script setup>
import { ref } from "vue";
import {
    faFilter,
    faMagnifyingGlass,
    faTimes,
} from "@fortawesome/free-solid-svg-icons";
import SearchIcon from "@/Icons/SearchIcon.vue";
import FilterIcon from "@/Icons/Filter.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

import debounce from "lodash/debounce";

const emit = defineEmits([
    "update:modelValue",
    "reset",
    "onFilter",
    "pp_changed",
]);
const onInput = debounce((event) => {
    emit("update:modelValue", event.target.value);
}, 500);

const props = defineProps({
    modelValue: String,
    disableSearch: {
        type: Boolean,
        required: false,
        default: false,
    },
    noFilter: Boolean,
});
</script>
<template v-if="!disableSearch">
    <div class="flex flex-grow justify-end w-full sm:w-auto mb-5 lg:mb-0">
        <div
            class="relative rounded-md shadow-sm flex-grow sm:min-w-[300px] lg:min-w-min lg:max-w-sm"
        >
            <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
            >
                <!-- <font-awesome-icon :icon="faMagnifyingGlass" class="text-gray-400" /> -->
                <SearchIcon />
            </div>
            <input
                type="text"
                name="search"
                id="search"
                :value="modelValue"
                @input="onInput"
                class="block w-full rounded-md border-grey pl-10 pr-5 lg:pl-60vw py-3 lg:py-16vw focus:!border-primary-500 focus:!shadow-none sm:text-sm focus:!outline-none"
                placeholder="Search"
            />

            <button
                v-if="modelValue && modelValue !== ''"
                @click="$emit('reset')"
                type="button"
                class="absolute top-0 right-0 h-full px-4 text-sm text-gray-700 focus:outline-none"
            >
                <font-awesome-icon :icon="faTimes" />
            </button>
        </div>
        <SecondaryButton
            v-if="!noFilter"
            @click="$emit('onFilter')"
            class="ml-3"
        >
            <FilterIcon class="w-4 lg:w-24vw h-4 lg:h-24vw mr-0 md:mr-2" />
            <span class="hidden md:block">Filter</span>
        </SecondaryButton>
    </div>
</template>
