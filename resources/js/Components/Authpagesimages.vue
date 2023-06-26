<style>
    /* .login_or_register_right {
        background: url("") no-repeat;
        background-size:cover;
    } */
</style>
<template>
    <div class="relative w-full lg:w-1/2 flex flex-col items-center justify-center p-12 bg-no-repeat bg-cover bg-center bg-[url('images/Register-Background.png')] min-h-screen">
        <div class="absolute bottom-6 left-0 w-7/7 pl-10">
            <slot name="imagetext" />
        </div>
      </div>
</template>