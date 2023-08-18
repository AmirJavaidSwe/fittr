<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    se: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <SectionTitle>
        <template #title>
            Event details
        </template>
    </SectionTitle>
    <Section>
        <dl class="divide-y space-y-2">
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Connected account</dt>
                <dd class="text-lg font-semibold">{{se.connected_account}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Event ID</dt>
                <dd class="text-lg font-semibold flex-grow">{{se.stripe_id}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Event type</dt>
                <dd class="text-lg font-semibold">{{se.type}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">On behalf of</dt>
                <dd class="text-lg font-semibold">{{se.event_for}}</dd>
            </div>
            <div v-if="se.business" class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Business</dt>
                <dd class="text-lg font-semibold">{{se.business?.name.val}} (ID: {{se.business.id}})</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Processed</dt>
                <dd class="text-lg font-semibold">{{se.is_processed ? 'Yes' : 'No'}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">API version</dt>
                <dd class="text-lg font-semibold">{{se.api_version}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Livemode</dt>
                <dd class="text-lg font-semibold">{{se.livemode ? 'Yes' : 'No'}}</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-gray-500 w-full md:w-48">Received</dt>
                <dd class="text-lg font-semibold">{{ DateTime.fromISO(se.created_at).toLocaleString(DateTime.DATETIME_MED) }} ({{ DateTime.fromISO(se.created_at).toRelative() }})</dd>
            </div>
            <div class="flex flex-wrap gap-2">
                <dt class="text-blue-600 w-full md:w-48 mt-4"> Event JSON data</dt>
                <dd class="text-lg">
                    <div class="bg-white p-4">
                        <pre>{{se.data}}</pre>
                    </div>
                </dd>
            </div>
        </dl>
    </Section>
</template>
