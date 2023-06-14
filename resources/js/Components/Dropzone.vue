<script setup>
import { computed } from "@vue/reactivity";
import { ref } from "vue";
import { useDropzone } from "vue3-dropzone";

const props = defineProps({
    modelValue: [Object, Array],
    accept: Array,
    max_width: [Number, String],
    max_height: [Number, String],
    multiple: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue']);

const files = ref('');

const onDrop = (acceptFiles, rejectReasons) => {
  files.value = props.multiple ? files.value.concat(acceptFiles) : acceptFiles;
  emit('update:modelValue', props.multiple ? files.value : files.value[0]);
}

const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop, accept: props.accept, multiple: props.multiple });

const allowedTypes = computed(() => {
  return props.accept.map((item, index) => {
    item  = item.replace('.', '').toUpperCase();
    return index == props.accept.length-2 && props.accept.length > 1 ? item+' OR' : item;
  }).join(', ');
});

</script>
<template>
  <div>
    <div v-bind="getRootProps()" class="bg-blue-100 color-black px-3 py-6 rounded-xl mt-1 border-2 border-blue-300 border-dashed">
      <input v-bind="getInputProps()" />
      <div v-if="isDragActive">Drop the files here...</div>
      <div v-else-if="files.length">{{ multiple ? files.map(item => item.name).join(', ') : files[0].name }}</div>
      <div v-else>Drop your {{ allowedTypes }} file here. {{ max_width && max_height ? `(Max. ${max_width}x${max_height}px).` : '' }}</div>
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