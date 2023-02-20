<script setup>
import { computed, ref, watch } from 'vue';
const props = defineProps({
    flash: {
        type: Object,
        default(rawProps) {
            return { type: 'info', message: 'OK', timestamp: null }
        }
    },
});
const has_msg = computed(() => props.flash.timestamp);
const msg_type = computed(() => props.flash.type);
const show = ref(false);
const title = ref(null);
const timer = ref(null);
const classes = ref('');
watch(has_msg, (new_msg) => {
    switch (msg_type.value) {
        case 'success':
            classes.value = 'border-green-500';
            title.value = 'Success';
            break;
        case 'warning':
            classes.value = 'border-yellow-500';
            title.value = 'Warning';
            break;
    
        default:
            classes.value = 'border-blue-500';
    }
    show.value = true;
    clearTimeout(timer.value);
    timer.value = setTimeout(() => {
        show.value = false;
    }, 3000);
});
</script>
<template>
<teleport to="body">
    <transition  
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 translate-x-full"
          enter-to-class="opacity-100"

          leave-active-class="ease-in duration-300"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0 translate-x-full"
          >
        <div v-if="show" @click="show = false" class="cursor-pointer fixed right-8 top-24 transform transition-all">
            <div class="bg-gray-900 border-l-4 overflow-hidden px-4 py-2 rounded-lg shadow-md text-white w-60" :class="classes">
                <div v-if="title" class="font-bold">
                    {{ title }}
                </div>
                <div class="text-sm">
                    {{ flash.message }}
                </div>
            </div>
        </div>
    </transition>
</teleport>
</template>
