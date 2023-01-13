<script setup>
import Package from '@/Components/Package.vue';
const props = defineProps({
    packages: Array,
});
</script>
<template>
    <div class="flex flex-wrap gap-8 my-4">
        <Package v-for="pack in packages" :pack="pack" :key="pack.id"/>
        <div v-if="!packages.length > 0">No packages found.</div>
    </div>
</template>