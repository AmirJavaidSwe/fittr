<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <slot name="search"></slot>
            <div class="sm:flex sm:items-center">
                <div class="mt-4 sm:mt-0 sm:ml-8 sm:flex-none">
                    <slot name="button"></slot>
                    <Link v-if="!disableButton" :href="buttonLink"
                       class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent
                                    bg-indigo-600
                                    px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none
                                    focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                        {{ buttonTitle }}
                    </Link>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <slot name="tableHead"></slot>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
<!--                                <tr>-->
                                    <slot name="tableData"></slot>
<!--                                </tr>-->
                            </tbody>
                        </table>
                        <slot name="pagination"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    disableButton: {
        type: Boolean,
        default: false
    },
    disableSearch: {
        type: Boolean,
        default: false
    },
    buttonTitle: {
        default: 'Add data',
        type: String,
        required: false
    },
    buttonLink: {
        default: '#',
        type: String,
        required: false
    }
})

</script>
