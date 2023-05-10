<script setup>
import { Link } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import ButtonLink from '@/Components/ButtonLink.vue';


const props = defineProps({
    role: Object,
});
</script>

<template>
    <Section>
        <SectionTitle>
            <template #title>
                Role details
            </template>
            <template #description>
                <!-- Role ID: {{role.id}} -->
            </template>
            <template #aside>
                <ButtonLink :href="route('admin.roles.edit', {id: role.slug})" type="primary">Edit</ButtonLink>
            </template>
        </SectionTitle>

        <SectionBorder />

        <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Title</dt>
                <dd class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900">{{ role.title }}</dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Source</dt>
                <dd class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900">{{ role.source }}</dd>
            </div>
            <div v-if="role.business_id" class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Business ID</dt>
                <dd class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900">{{ role.business_id }}</dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Created At</dt>
                <dd class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900">{{ DateTime.fromISO(role.created_at).toLocaleString(DateTime.DATETIME_FULL) }}</dd>
            </div>
        </dl>
    </Section>
</template>
