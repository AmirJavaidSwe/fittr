<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    api_results: Object,
});
const metric = ref(props.api_results.data.metricData[0]);
</script>

<template>
    <SectionTitle>
        <template #title>
            Instance Metrics
        </template>
        <template #description>
            {{props.api_results.data?.metricName}}
        </template>
    </SectionTitle>
    <Section>
        <dl v-if="!api_results.data.error" class="divide-y space-y-2">
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Minimum</dt>
                <dd class="text-lg font-semibold flex-grow">{{metric.minimum}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Maximum</dt>
                <dd class="text-lg font-semibold">{{metric.maximum}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Average</dt>
                <dd class="text-lg font-semibold">{{metric.average}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Unit</dt>
                <dd class="text-lg font-semibold">{{metric.unit}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Period</dt>
                <dd class="text-lg font-semibold">{{ DateTime.fromISO(metric.timestamp) }}</dd>
            </div>
        </dl>
        <div v-else>
            Error: {{api_results.error_message}}
        </div>
    </Section>
</template>
