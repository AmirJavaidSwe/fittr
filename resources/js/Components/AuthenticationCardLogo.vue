<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import LogoLetter from '@/Components/LogoLetter.vue';
// import LogoSign from '@/Components/LogoSign.vue';
</script>

<template>
    <Link :href="'/'">
        <LogoLetter fill="#000000" class="w-24 h-24" />
        <!-- <LogoSign fill="#EE8934" class="w-24 h-24" /> -->
    </Link>
</template>
