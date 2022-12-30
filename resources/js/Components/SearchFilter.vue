<template>
  <div class="flex items-center">
    <div class="flex w-full shadow">
        <slot />
        <input class="relative px-6 py-3 w-full rounded-r focus:shadow-outline" autocomplete="off" type="text" name="search" placeholder="Search…" 
                :value="modelValue"
                @input="onInput"
                />
    </div>
    <button class="ml-3 text-gray-500 hover:text-gray-700 focus:text-indigo-500 text-sm" type="button" @click="$emit('reset')">Reset</button>
  </div>
</template>

<script setup>
    import { computed } from 'vue';
    import debounce from 'lodash/debounce';

    const props = defineProps(['modelValue']);
    const emit = defineEmits(['update:modelValue', 'reset']);
    const onInput = debounce((event) => {
                emit('update:modelValue', event.target.value);
            }, 500);
</script>
