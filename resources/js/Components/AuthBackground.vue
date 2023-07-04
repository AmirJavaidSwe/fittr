<script setup>
import { ref } from "vue";
const bg_img = window.location.origin + '/images/auth-background.png';
</script>
<template>
    <div class="relative w-full lg:w-1/2 flex flex-col items-center justify-center min-h-screen hidden lg:block">
        <img src="/images/auth-background.png" alt="auth-background.png" class="bg-no-repeat bg-cover bg-center w-full">
        <div class="absolute bottom-6 left-0 w-7/7 pl-10">
            <slot name="imagetext" />
        </div>
      </div>
</template>