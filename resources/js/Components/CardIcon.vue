<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    cardLink: String,
    iconClass: String,
    height: {
        type:String,
        default:"h-11" 
    }
});
</script>

<template>
    <div
        class="bg-white bg-white-50 flex hover:bg-white-100 items-center p-3 gap-4 relative transition"
    >
        <Link
            v-if="cardLink"
            :href="cardLink"
            class="absolute bottom-0 top-0 w-full cursor-pointer"
        ></Link>
        <div v-if="$slots.icon" class="flex-shrink-0 bg-mainBg text-gray-700 dark:text-gray-500 border inline-flex items-center transition text-green rounded-md border border-500 px-4 text-sm" :class="iconClass+' '+height">
            <slot name="icon"></slot>
        </div>
        <div class="flex-1 min-w-0">
            <p
                class="text-sm lg:text-md 2xl:text-lg font-bold text-gray-900 truncate dark:text-dark"
            >
                <slot name="title"></slot>
            </p>
            <p
                class="text-sm lg:text-md 2xl:text-lg text-gray-500 truncate dark:text-gray-400"
            >
                <slot></slot>
            </p>
        </div>
    </div>
</template>
