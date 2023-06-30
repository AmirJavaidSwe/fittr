<script setup>
import { ref } from "vue";
const bg_img = window.location.origin + '/images/auth-background.png';
</script>
<template>
    <div class="relative w-full lg:w-1/2 flex flex-col items-center justify-center p-12 bg-no-repeat bg-cover bg-center min-h-screen hidden lg:block" :style="'background: url('+bg_img+');'">
        <div class="absolute bottom-6 left-0 w-7/7 pl-10">
            <slot name="imagetext" />
        </div>
      </div>
</template>