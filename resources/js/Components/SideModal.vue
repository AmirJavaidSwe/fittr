<script setup>
import { ref, computed, useSlots } from "vue";
import { faTimes } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    closeable: {
        type: Boolean,
        default: true,
    }
});

const slots = useSlots();
const emit = defineEmits(["close"]);
const lengthOfClassElements = ref(0);
const sideModalZIndex = computed(() => {
    lengthOfClassElements.value = document.querySelectorAll(".sideModalOpened").length;
    return {'z-index': 1035 + lengthOfClassElements.value};
});
const sideModalOverLayZIndex = computed(() => {
    return {'z-index': 1000 + lengthOfClassElements.value};
})
const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

//closes all modals
// const closeOnEscape = (e) => {
//     if (e.key === "Escape") {
//         close();
//     }
// };
// onMounted(() => document.addEventListener('keydown', closeOnEscape));
// onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));
</script>

<template>
    <teleport to="body">
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                @click="close"
                class="fixed inset-0 transform transition-all" :style="sideModalOverLayZIndex"
            >
                <div class="absolute inset-0 bg-gray-500 opacity-75" />
            </div>
        </transition>
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="ease-in duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <nav
                v-if="show"
                class="sideModalOpened fixed right-0 top-0 overflow-y-auto h-full w-full md:w-1/2 xl:w-1/3 bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] p-5 flex flex-col"
                :style="sideModalZIndex"
            >
                <div class="text-xl font-bold flex justify-between">
                    <slot name="title" />
                    <button 
                        v-if="closeable"
                        class="h-8 flex-shrink-0 hover:bg-secondary-600 hover:text-gray-900 flex items-center justify-center rounded-lg text-gray-400 text-sm transition w-8"
                        @click="close">
                        <font-awesome-icon :icon="faTimes" class="fa-xl" />
                    </button>
                </div>

                <div class="mt-4 mb-3 flex-1" v-if="slots.content">
                    <slot name="content" />
                </div>

                <div
                    class="flex mt-4 mb-3 flex-1 justify-end"
                    v-if="slots.footer"
                >
                    <slot name="footer" />
                </div>
            </nav>
        </transition>
    </teleport>
</template>
