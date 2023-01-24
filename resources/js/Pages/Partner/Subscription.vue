<script setup>
import Package from '@/Components/Package.vue';
import Section from '@/Components/Section.vue';
import Subscription from '@/Components/Subscription.vue';
const props = defineProps({
    packages: Array,
    active_subscription: Object,
    has_subscription: Boolean,
});
</script>

<template>
    <Section>
        <div v-if="active_subscription">
            <div class="text-lg font-medium text-gray-900 mb-4">
                Your current package
            </div>
            <Subscription :subscription="active_subscription" />
        </div>

    </Section>
    <Section>
        <div class="text-lg font-medium text-gray-900 my-4">Available packages</div>
        <div class="flex flex-wrap gap-8 my-4">
            <Package v-for="pack in packages" :pack="pack" :has_subscription="active_subscription" :key="pack.id"/>
            <div v-if="!packages.length > 0">No packages found.</div>
        </div>
    </Section>
</template>
