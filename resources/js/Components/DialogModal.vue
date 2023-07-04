<script setup>
import { computed, useSlots } from 'vue';
import Modal from './Modal.vue';

const emit = defineEmits(['close']);
const hasFooter = computed(() => !! useSlots().footer);

defineProps({
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
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="px-6 py-4 bg-gray-50">
            <div class="flex text-lg">
                <slot name="title" />
                <slot name="close" />
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="mt-2">
                <slot name="content" />
            </div>
        </div>
        <div v-if="hasFooter" class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right">
            <slot name="footer" />
        </div>
    </Modal>
</template>
