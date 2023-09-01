<script setup>
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import CKEditor from '@ckeditor/ckeditor5-vue';
import { onUpdated, ref } from 'vue';

const editor = ClassicEditor;

const CKEditorComponent = CKEditor.component;

const props = defineProps({
    modelValue: String,
});

const htmlContent = ref(props.modelValue);

onUpdated(() => {
    htmlContent.value = props.modelValue;
});

const emit = defineEmits(["update:modelValue", "setPlainText"]);

const setEditorData = (data) => {
    let htmlContent = data.replace(/<\/div>/ig, '</div>\n')
        .replace(/<\/li>/ig, '</li>\n')
        .replace(/<li>/ig, '\t<li>')
        .replace(/<\/p>/ig, '</p>\n')
        .replace(/<\/h[1-6]>/ig, (data) => (data+'\n'))
        .replace(/<br\s*[\/]?>/gi, "<br />\n");

    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlContent, 'text/html');
    const plainText = doc.firstElementChild.textContent;

    emit('update:modelValue', data);
    emit('setPlainText', plainText);
};

</script>
<template>
    <CKEditorComponent
        v-model="htmlContent"
        :editor="editor"
        :config="{
            minHeight: 400,
            toolbar: {
                items: [
                    'bold', 'italic',
                    '|', 'heading',
                    '|', 'link', 'bulletedList', 'numberedList', 'outdent', 'indent',
                    '|', 'undo', 'redo',
                ]
            },
        }"
        @input="setEditorData"
    />
</template>