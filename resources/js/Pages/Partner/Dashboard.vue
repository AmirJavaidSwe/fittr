<script setup>
import Section from '@/Components/Section.vue';
import Package from '@/Components/Package.vue';
import CardBasic from '@/Components/CardBasic.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    partner: Object,
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl">
            Hello, {{partner.name}}
        </div>
        <hr class="mb-4">
        <div></div>

        <CardBasic v-if="partner.active_subscription">
            <template #header>
                Your service package is active
            </template>

            <template #footer>
                <ButtonLink :href="route('partner.subscriptions.index')" type="primary">Manage</ButtonLink>
            </template>
        </CardBasic>
        <CardBasic v-else>
            <template #header>
                You are not subscribed to any package
            </template>

            <template #footer>
                <ButtonLink :href="route('partner.pricing.index')" type="primary">Select package</ButtonLink>
            </template>
        </CardBasic>

    </Section>
</template>
