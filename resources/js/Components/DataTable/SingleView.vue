<template>
    <div class="overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="flex">
            <div class="px-4 py-5 sm:px-6 w-full">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500"> {{ description }} </p>
            </div>
            <slot name="head"></slot>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <slot name="item"></slot>
            </dl>
        </div>
    </div>
</template>

<script setup>

defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    }
})

</script>
