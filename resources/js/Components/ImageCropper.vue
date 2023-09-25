<script setup>
import { ref } from 'vue';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import Dropzone from './Dropzone.vue';
import { watch } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faClose } from '@fortawesome/free-solid-svg-icons';
import { onUpdated } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Object],
    }
});

const emit = defineEmits(['update:modelValue']);

const image = ref(props.modelValue ? { ...props.modelValue } : null);
const imageBase64 = ref('');

onUpdated(() => {
    if (!image.value && props.modelValue)
        image.value = { ...props.modelValue };
});

const onCropChange = async (data) => {
    const imageBlob = await (new Promise((resolve, reject) => {
        data.canvas.toBlob(blob => resolve(blob), image.value.type);
    }));

    let imageFile = new File([imageBlob], image.value.name);
    emit('update:modelValue', imageFile);

};

const readFile = (file) => {
    return new Promise((resolve, reject) => {
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            resolve(reader.result);
        };
        reader.onerror = function (error) {
            reject(error);
        };
    });
};

watch(image, async () => {
    if(image.value) {
        let base64Img = await readFile(image.value).catch(console.error);
        imageBase64.value = base64Img;
    } else {
        imageBase64.value = '';
        emit('update:modelValue', null);
    }
});
</script>
<template>
    <div class="flex relative mt-2 items-center" :class="{'bg-black': imageBase64, 'aspect-square': imageBase64}">
        <img v-if="image?.url" :src="image.url" class="w-full" />
        <Dropzone v-else-if="!image" v-model="image" :accept="['.jpg', '.png', '.bmp']" buttonText="Select Photo" />
        <Cropper
            v-else
            class="align-middle w-full"
            :stencil-props="{
                aspectRatio: 1,
            }"
            imageRestriction="fit-area"
            :src="imageBase64"
            @change="onCropChange"
        />
        <span v-if="image || image?.url" class="absolute right-0 top-0 px-1 bg-gray-700/70 cursor-pointer" @click="image = null">
            <FontAwesomeIcon :icon="faClose" class="text-white" />
        </span>
    </div>
</template>