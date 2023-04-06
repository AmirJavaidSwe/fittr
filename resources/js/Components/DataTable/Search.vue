<script setup>
import { ref } from 'vue';
import { faMagnifyingGlass} from '@fortawesome/free-solid-svg-icons';

import debounce from 'lodash/debounce';
const emit = defineEmits([
    'update:modelValue',
    'reset',
    'pp_changed'
]);
const onInput = debounce((event) => {
            emit('update:modelValue', event.target.value);
        }, 500);

const props = defineProps({
    modelValue: String,
    per_page:  {
        type: Number,
        required: false,
        default: 10
    },
    disableSearch: {
        type: Boolean,
        required: false,
        default: false
    }
});
const pp = ref(props.per_page);

const optionsPP = [
    {value: 5, text: '5'},
    {value: 10, text: '10'},
    {value: 25, text: '25'},
    {value: 50, text: '50'},
];

</script>
<template v-if="!disableSearch">
    <div class="flex flex-grow">
        <select v-model="pp" @change="$emit('pp_changed', pp)" class="rounded mr-1">
          <option v-for="option in optionsPP"
                  v-bind:selected="option.value === props.per_page"
                  :value="option.value">
              {{ option.text }}
          </option>
        </select>
        <label for="search" class="sr-only outline-none">
            Search
        </label>
        <div class="relative rounded-md shadow-sm flex-grow">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <font-awesome-icon :icon="faMagnifyingGlass" class="text-gray-400" />
            </div>
            <input
                type="text"
                name="search"
                id="search"
                :value="modelValue"
                @input="onInput"
                class="block w-full h-full rounded-md border-gray-300 pl-10 focus:border-pink-500 sm:text-sm outline-none"
                placeholder="Search">

            <button
                @click="$emit('reset')"
                type="button"
                class="absolute top-0 right-0 h-full px-4 text-sm text-gray-700 bg-gray-100 rounded-r-md hover:bg-gray-200
                       focus:outline-none focus:ring-2 focus:ring-offset-2 border">
                Reset
            </button>

        </div>
    </div>
</template>


