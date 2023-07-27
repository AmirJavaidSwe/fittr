<script setup>
import Upload from "@/Icons/UploadArrowIcon.vue";
</script>
<template>
    
<div class="flex items-center justify-center w-full mt-6 mb-8">
    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-34 border-2 border-blue border-dashed rounded-lg cursor-pointer bg-fileInputBg">
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
            
            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                <span class="font-semibold text-lg text-blue flex pb-3">
                    <span class="mr-2">
                        <Upload />
                    </span>Upload or Drag a PNG,JPG or SVG logo image</span></p>
                <button class="bg-white hover:bg-gray-100 text-grey font-semibold py-2 px-4 border border-blue rounded-lg shadow">
                    Select New logo
                </button>
        </div>
        <input id="dropzone-file" type="file" class="hidden" />
    </label>
</div> 
 
</template>
