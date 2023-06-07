<script setup>
import { faTimes } from "@fortawesome/free-solid-svg-icons";
import { useSlots } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const slots = useSlots();
const emit = defineEmits(["close"]);

const close = () => {
    emit("close");
};
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
                v-show="show"
                class="fixed inset-0 transform transition-all z-[1000]"
                @click="close"
            >
                <div class="absolute inset-0 bg-gray-500 opacity-75" />
            </div>
        </transition>
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-x-full"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 translate-x-full"
        >
            <nav
                v-if="show"
                class="fixed right-0 top-0 z-[1035] overflow-y-auto h-full w-full md:w-1/2 xl:w-1/3 overflow-hidden bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] p-5 flex flex-col"
            >
                <div class="text-xl font-bold" v-if="slots.title">
                    <button class="md:hidden mr-5" @click="close">
                        <font-awesome-icon :icon="faTimes" />
                    </button>
                    <slot name="title" />
                </div>

                <div class="mt-4 mb-3 flex-1" v-if="slots.content">
                    <slot name="content" />
                </div>
            </nav>
        </transition>
    </teleport>
</template>
