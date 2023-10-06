<script setup>
import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';
import CKEditor from '@ckeditor/ckeditor5-vue';
import { onUpdated, ref } from 'vue';

const editor = DecoupledEditor;

const CKEditorComponent = CKEditor.component;

const props = defineProps({
    modelValue: String,
    readonly: {
        type: Boolean,
        default: false,
    },
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
        :disabled="readonly"
        :config="{
            minHeight: 400,
            toolbar: {
                items: [
                    'bold', 'italic', 'underline',
                    '|', 'heading', 'alignment',
                    '|', 'link', 'bulletedList', 'numberedList', 'outdent', 'indent',
                    '|', 'undo', 'redo',
                ]
            },
        }"
        @input="setEditorData"
        @ready="(editor) => {
            // Insert the toolbar before the editable area.
            const classNames  = editor.ui.view.toolbar.element.getAttribute('class');
            const toolbar = editor.ui.getEditableElement().parentElement.getElementsByClassName(classNames);

            if (toolbar.length) {
                toolbar[0].remove();
            }

            editor.ui.getEditableElement().parentElement.insertBefore(
                editor.ui.view.toolbar.element,
                editor.ui.getEditableElement()
            );
        }"
    />
</template>