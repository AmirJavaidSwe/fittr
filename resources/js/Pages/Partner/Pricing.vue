<script setup>
import Section from '@/Components/Section.vue';
import Package from '@/Components/Package.vue';
const props = defineProps({
    packages: Array,
    has_subscription: Boolean,
});
</script>

<template>
    <Section bg="bg-transparent">
        <div>Available packages</div>

        <div class="flex flex-wrap gap-8 my-4">
            <Package v-for="pack in packages" :pack="pack" :has_subscription="has_subscription" :key="pack.id"/>
            <div v-if="!packages.length > 0">No packages found.</div>
        </div>
    </Section>
</template>
