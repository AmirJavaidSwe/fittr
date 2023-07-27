<script setup>
import Package from '@/Components/Package.vue';
const props = defineProps({
    package: Object,
});
</script>
<template>
    <div class="flex flex-wrap gap-8 my-4">
        <Package :pack="package" />
    </div>
</template>
