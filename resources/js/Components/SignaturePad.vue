<script setup>
import { onMounted, ref, watch, watchEffect } from "vue";
import { faUndo, faTimes } from "@fortawesome/free-solid-svg-icons";
import SignaturePad from "signature_pad";
import debounce from 'lodash/debounce';


const props = defineProps(['sign'])

const emit = defineEmits(['onSignatureUpdate'])

const canvas = ref(null)

const signaturePad = ref(null)


const updateSignature = debounce(() => {
    let data = null;
    if(!signaturePad.value.isEmpty()) {
        data = signaturePad.value.toDataURL('image/png');
    }
    emit('onSignatureUpdate', data);
}, 300)

onMounted(() => {
    canvas.value = document.getElementById('signature-pad');
    const parentElement = canvas.value.parentElement;
    const parentWidth = parentElement.offsetWidth;
    canvas.value.setAttribute("width", parentWidth-5);

    signaturePad.value = new SignaturePad(canvas.value, {
      backgroundColor: 'rgb(255, 255, 255)' // necessary for saving image as JPEG; can be removed is only saving as PNG or SVG
    });
    if(props.sign) {
        signaturePad.value.fromDataURL(props.sign, { ratio: 1 });
    }


    signaturePad.value.addEventListener("endStroke", () => {
        updateSignature();
    });
});

const undoSignature = () => {
    var data = signaturePad.value.toData();
    if (data) {
        data.pop(); // remove the last dot or line
        signaturePad.value.fromData(data);
        updateSignature();
    }
};
const clearSignature = () => {
    signaturePad.value.clear();
    updateSignature();
};
</script>

<template>
    <div class="h-40 mb-12 border rounded-md border-gray-500">
        <canvas id="signature-pad"></canvas>
        <div class="flex justify-between">
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 mt-3 font-bold py-2 px-4 rounded inline-flex items-center"
                @click="undoSignature"
            >
                <font-awesome-icon :icon="faUndo" style="color: #333" /> &nbsp;
                Undo
            </button>
            <button type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 mt-3 font-bold py-2 px-4 rounded inline-flex items-center"
                @click="clearSignature"
            >
                <font-awesome-icon :icon="faTimes" style="color: #333" /> &nbsp;
                Clear
            </button>
        </div>
    </div>
</template>
