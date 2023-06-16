<script setup>
import { faEye, faRemove, faTrash } from "@fortawesome/free-solid-svg-icons";
import { Link } from "@inertiajs/vue3";
import { computed } from "@vue/reactivity";
import { ref } from "vue";
import { useDropzone } from "vue3-dropzone";

const props = defineProps({
    modelValue: [Object, Array],
    accept: Array,
    max_width: [Number, String],
    max_height: [Number, String],
    multiple: { type: Boolean, default: false },
    uploaded_files: Array,
});

const emit = defineEmits(['update:modelValue', 'remove_uploaded_file']);

const files = ref('');

const onDrop = (acceptFiles, rejectReasons) => {
  files.value = props.multiple ? files.value.concat(acceptFiles) : acceptFiles;
  if(!props.multiple) props.uploaded_files.splice(0, 1);
  emit('update:modelValue', props.multiple ? files.value : files.value[0]);
}

const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop, accept: props.accept, multiple: props.multiple });

const allowedTypes = computed(() => {
  return props.accept.map((item, index) => {
    item  = item.replace('.', '').toUpperCase();
    return index == props.accept.length-2 && props.accept.length > 1 ? item+' OR' : item;
  }).join(', ');
});

const removeUploadedFile = (id) => {
  emit('remove_uploaded_file', id);
  // props.remove_uploaded_file(id);
}

</script>
<template>
  <div class="m-3">
    <div v-bind="getRootProps()" class="bg-blue-100 color-black px-3 py-6 rounded-xl mt-1 border-2 border-blue-300 border-dashed">
      <input v-bind="getInputProps()" />
      <div v-if="isDragActive">Drop the files here...</div>
      <div v-else-if="files.length">{{ multiple ? files.map(item => item.name).join(', ') : files[0].name }}</div>
      <div v-else>Drop your {{ allowedTypes }} file here. {{ max_width && max_height ? `(Max. ${max_width}x${max_height}px).` : '' }}</div>
    </div>
    <div v-if="uploaded_files.length" class="mt-2">
      <div v-for="(image, index) in uploaded_files" class="inline-block relative">
        <a :href="image.url" target="_blank" title="Click to view">
          <img :src="image.url" :alt="image.original_filename" class="inline-block w-20 rounded-lg cursor-pointer border-gray-300 border">
        </a>
        <div class="absolute top-[0px] right-[0px] px-2 cursor-pointer" @click="removeUploadedFile(image.id)">
          <font-awesome-icon class="text-red-900" size="xs" :icon="faTrash" />
        </div>
      </div>
    </div>
  </div>
</template>

<!-- <script>

export default {
  name: "Dropzone",
  setup() {

    return {
      getRootProps,
      getInputProps,
      ...rest,
    };
  },
};
</script> -->