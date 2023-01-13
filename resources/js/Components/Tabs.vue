<script setup>
import { reactive, ref, onMounted } from 'vue';
import Tab from '@/Components/Tab.vue';

const currentTab = ref('');
const props = defineProps({
    tabs: {
        type: Array,
    },
    active: {
        type: Number,
    },

});
const emit = defineEmits(['tabChanged']);
const setCurrentTab = (index) => {
    currentTab.value = index;
    emit('tabChanged', index);
};
currentTab.value = props.active;
</script>

<template>
  <div>
    <button
       v-for="(title, index) in tabs"
       :key="title"
       :class="[
        'inline-flex items-center px-4 py-2 font-semibold text-xs uppercase transition',
        index == currentTab ? ' bg-white rounded-t-md' : 'text-gray-500'
       ]"
       @click="setCurrentTab(index)"
     >
      {{ title }}
    </button>
    <div class="bg-white p-4">
      <slot></slot>
    </div>
  </div>
</template>
