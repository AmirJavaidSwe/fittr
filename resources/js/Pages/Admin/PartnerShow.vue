<script setup>
import { Link } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    partner: Object,
});
</script>

<template>
    <Section>
        <SectionTitle>
            <template #title>
                Partner details
            </template>
            <template #description>
                User ID: {{partner.id}}
            </template>
            <template #aside>
                <ButtonLink :href="route('admin.partners.edit', {id: partner.id})" type="primary">Edit</ButtonLink>
            </template>
        </SectionTitle>

        <SectionBorder />

        <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700">
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Name</dt>
                <dd class="text-lg font-semibold">{{partner.name}}</dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Email address</dt>
                <dd class="text-lg font-semibold">{{partner.email}}</dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Email verified</dt>
                <dd class="text-lg font-semibold">
                    <span v-if="partner.email_verified_at">YES, {{ DateTime.fromISO(partner.email_verified_at).toRelative() }}</span>
                    <span v-else>NO</span>
                </dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Date created</dt>
                <dd class="text-lg font-semibold">{{ DateTime.fromISO(partner.created_at) }} {{ DateTime.fromISO(partner.created_at).toRelative() }}</dd>
            </div>
        </dl>
    </Section>
</template>
