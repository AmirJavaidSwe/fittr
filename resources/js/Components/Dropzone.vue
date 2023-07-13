<script setup>
import { ref, watchEffect } from "vue";
import { faEye, faRemove, faTrash } from "@fortawesome/free-solid-svg-icons";
import { Link } from "@inertiajs/vue3";
import { computed } from "@vue/reactivity";
import { useDropzone } from "vue3-dropzone";
import Upload from "@/Icons/UploadArrowIcon.vue";
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
  modal: Boolean,
  modelValue: [Object, Array],
  accept: Array,
  max_width: [Number, String],
  max_height: [Number, String],
  multiple: { type: Boolean, default: false },
  uploaded_files: {
    type: Array,
    default: [],
    required: false
  },
  uploaded_file: {
    type: String,
    default: '',
    required: false
  },
  buttonText: {
    type: String, 
    default: 'Select New Logo',
    required: false
  },
  instance_name: {
    type: String, 
    default: '',
    required: false
  }
});
const emit = defineEmits(['update:modelValue', 'remove_uploaded_file', 'remove_file']);

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

const imgPreview = ref(null)
const imgPreviewDelete = ref(null)

watchEffect(async () => {
  if (files.value.length) {
    const img = files.value[0];
    const reader = new FileReader();
    reader.onload = (e) => {
      imgPreview.value = e.target.result;
      imgPreviewDelete.value = false;
    };
    reader.readAsDataURL(img);
  }
})


const deletePreview = (instance_name) => {
  imgPreviewDelete.value = true;
  imgPreview.value = null;
  emit('remove_file', {
    'instance_name': instance_name
  })
  return false;
};

const show_logo = computed(() => {
  return (props.uploaded_file != '' || props.uploaded_files.length || imgPreview.value) && !imgPreviewDelete.value;
});
</script>
<template>
  <!-- <div class="m-3">
    <div v-bind="getRootProps()"
    class="bg-blue-100 color-black px-3 py-6 rounded-xl mt-1 border-2 border-blue-300 border-dashed">
      <input v-bind="getInputProps()" />
      <div v-if="isDragActive">Drop the files here...</div>
      <div v-else-if="files.length">
        {{ multiple ? files.map(item => item.name).join(', ') : files[0].name }}
      </div>
      <div v-else>Drop your {{ allowedTypes }} file here. {{ max_width && max_height ? `(Max.
        ${max_width}x${max_height}px).` : '' }}</div>
      </div>
    <div v-if="uploaded_files.length" class="mt-2">
      <div v-for="(image, index) in uploaded_files" class="inline-block relative">
        <a :href="image.url" target="_blank" title="Click to view">
          <img :src="image.url" :alt="image.original_filename"
            class="inline-block h-20 w-20 rounded-lg cursor-pointer border-gray-300 border">
        </a>
        <div class="absolute top-[0px] right-[0px] px-2 cursor-pointer" @click="removeUploadedFile(image.id)">
          <font-awesome-icon class="text-red-900" size="xs" :icon="faTrash" />
        </div>
      </div>
    </div>
  </div> -->
  <label for="dropzone-file" class="flex flex-col w-full border-2 border-blue border-dashed rounded-lg cursor-pointer bg-fileInputBg" :class="[(show_logo ? 'items-start justify-start' : 'items-center justify-center'), (props.modal ? 'h-24' : 'h-34')]">
    <div v-bind="getRootProps()" class="flex items-center justify-center w-full mt-3 mb-5" v-if="!show_logo">
      <input v-bind="getInputProps()" />
      <div class="flex flex-col items-center justify-center pt-5 pb-6">

        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
          <span class="font-semibold text-blue flex" :class="[(props.modal ? 'text-md pb-1 mt-2' : 'text-lg pb-3')]">
            <span class="mr-2">
              <Upload />
            </span>
            <span v-if="isDragActive">
              Drop the files here...
            </span>
            <span v-else>
              Upload or Drag a {{ allowedTypes }} file here.
            </span>
          </span>
        </p>
        <button type="button" class="bg-white hover:bg-gray-100 text-grey font-semibold border border-blue rounded-lg shadow" :class="[(props.modal ? 'py-1 px-2 text-sm h-9' : 'py-2 px-4')]">
          {{ buttonText }}
        </button>
      </div>
    </div>
    <div class="flex flex-col items-center justify-start z-[9999]" v-if="show_logo" :class="[(props.modal ? 'p-2' : 'p-6')]">
      <div class="inline-block relative z-50" v-if="imgPreview">
        <a :href="'javascript:;'">
          <img :src="imgPreview" :alt="files[0].name"
            class="inline-block w-20 h-20 rounded-lg cursor-pointer border-gray-300 border">
        </a>
        <div class="absolute z-[9999] top-[0px] right-[0px] px-2 cursor-pointer" @click="deletePreview">
          <font-awesome-icon class="text-red-900" size="xs" :icon="faTrash" />
        </div>
      </div>
      <div class="inline-block relative z-50" v-else-if="props.uploaded_file">
        <a :href="props.uploaded_file" target="_blank" title="Click to View">
          <img :src="props.uploaded_file" :alt="props.uploaded_file"
            class="inline-block w-20 h-20 rounded-lg cursor-pointer border-gray-300 border">
        </a>
        <div class="absolute z-[9999] top-[0px] right-[0px] px-2 cursor-pointer" @click="deletePreview(props.instance_name)">
          <font-awesome-icon class="text-red-900" size="xs" :icon="faTrash" />
        </div>
      </div>
      <div v-else-if="uploaded_files.length" v-for="(image, index) in uploaded_files" :key="index" class="inline-block relative">
        <a :href="image.url" target="_blank" title="Click to View">
          <img :src="image.url" :alt="image.original_filename"
            class="inline-block w-20 h-20 rounded-lg cursor-pointer border-gray-300 border">
        </a>
        <div class="absolute z-[99999] top-[0px] right-[0px] px-2 cursor-pointer" @click="removeUploadedFile(image.id)">
          <font-awesome-icon class="text-red-900" size="xs" :icon="faTrash" />
        </div>
      </div>
    </div>
  </label>
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