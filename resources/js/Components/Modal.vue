<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    sideModalOpened: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

watch(() => props.show, () => {
    if (props.show) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = null;
    }
});

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
});

const maxWidthClass = computed(() => {
    return {
        'sm': 'sm:max-w-sm', //24rem; /* 384px */
        'md': 'sm:max-w-md', //28rem; /* 448px */
        'lg': 'sm:max-w-lg', //32rem; /* 512px */
        'xl': 'sm:max-w-xl', //36rem; /* 576px */
        '2xl': 'sm:max-w-2xl', //42rem; /* 672px */
        '3xl': 'sm:max-w-3xl', //48rem; /* 768px */
        '4xl': 'sm:max-w-4xl', //56rem; /* 896px */
        '5xl': 'sm:max-w-5xl', //64rem; /* 1024px */
        'full': 'sm:max-w-full', // 100%;
        'min': 'sm:max-w-min', // min-content;
        'max': 'sm:max-w-max', // max-content;
        'fit': 'sm:max-w-fit', // fit-content;
    }[props.maxWidth];
});
const zIndexing = computed(() => {
    return  props.sideModalOpened ? 'z-index: 9999;' : 'z-index: 100;';
});
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div v-if="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0" scroll-region :style="zIndexing">
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-show="show" class="fixed inset-0 transform transition-all" @click="close">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div v-show="show" class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all w-full sm:mx-auto" :class="maxWidthClass">
                        <slot v-if="show" />
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
