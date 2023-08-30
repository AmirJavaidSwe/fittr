<script setup>
import { computed, defineProps, ref } from "vue";

const props = defineProps({
    minWidth: {
        type: Number,
        default: 0.5,
    },
    maxWidth: {
        type: Number,
        default: 2.5,
    },
    penColor: {
        type: String,
        default: "black",
    },
    onEnd: {
        type: Function,
        default: () => {},
    },
});

const signature = ref('');
const signaturePadRef = ref(null);

const undo = () => {
    signaturePadRef.value.undoSignature();
};

const clear = () => {
    signaturePadRef.value.clearSignature();
};

const save = () => {
    signature.value = signaturePadRef.value.saveSignature();
};

const signaturePadOptions = computed(() => {
    return {
        minWidth: props.minWidth,
        maxWidth: props.maxWidth,
        penColor: props.penColor,
        onEnd: save,
    };
});
</script>

<template>
    <VueSignaturePad
        ref="signaturePadRef"
        :options="signaturePadOptions"
        class="border border-gray-300 rounded-md"
    />
    <div class="flex justify-between">
        <button
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
            @click="undo"
        >
            <svg
                class="w-4 h-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 2a8 8 0 100 16 8 8 0 000-16zM7.293 9.707a1 1 0 010-1.414L9.586 5.95a1 1 0 011.414 1.414L8.414 9l2.586 2.586a1 1 0 01-1.414 1.414L7.293 9.707z"
                    clip-rule="evenodd"
                />
            </svg>
            Undo
        </button>
        <button
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center"
            @click="clear"
        >
            <svg
                class="w-4 h-4 mr-2"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 9a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1zm4.293 1.707a1 1 0 010-1.414L12.414 9l1.879-1.879a1 1 0 10-1.414-1.414L11 7.586l-1.879-1.88a1 1 0 00-1.414 1.414L9.586 9l-1.88 1.879a1 1 0 001.414 1.414L11 10.414l1.879 1.879a1 1 0 001.414-1.414L12.414 9z"
                    clip-rule="evenodd"
                />
            </svg>
            Clear
        </button>
    </div>

</template>