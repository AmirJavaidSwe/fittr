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
const instance = ref(props.api_results.data.instance);
</script>

<template>
    <SectionTitle>
        <template #title>
            Instance details
        </template>
        <template #description>
            Metrics
        </template>
        <template #aside>
            <div class="space-x-1">
                <ButtonLink :href="route('admin.instances.show_metric', {name: instance.name, metric: 'CPUUtilization'})" type="primary">CPU Utilization</ButtonLink>
                <ButtonLink :href="route('admin.instances.show_metric', {name: instance.name, metric: 'StatusCheckFailed'})" type="primary">Status Check Failed</ButtonLink>
                <ButtonLink :href="route('admin.instances.show_metric', {name: instance.name, metric: 'NetworkOut'})" type="primary">Network Out</ButtonLink>
            </div>
        </template>
    </SectionTitle>
    <Section>
        <dl v-if="!api_results.data.error" class="divide-y space-y-2">
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Name</dt>
                <dd class="text-lg font-semibold flex-grow">{{instance.name}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">State</dt>
                <dd class="text-lg font-semibold">{{instance.state.name}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Date created</dt>
                <dd class="text-lg font-semibold">{{ DateTime.fromISO(instance.createdAt) }} ({{ DateTime.fromISO(instance.createdAt).toRelative() }})</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-blue-600 w-full md:w-48 mt-4"> Object data</dt>
                <dd class="text-lg">
                    <div class="bg-white p-4">
                        <ul>
                            <li v-for="(value, key) in instance">
                                <b>{{ key }}</b>: 
                                <div v-if="typeof value === 'object'">
                                    <li v-for="(value, key) in value">
                                        <b class="ml-2">{{ key }}</b>: {{ value }}
                                    </li>
                                </div>
                                <div v-else>
                                    {{ value }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </dd>
            </div>
        </dl>
        <div v-else>
            Error: {{api_results.error_message}}
        </div>
    </Section>
</template>
