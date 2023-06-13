<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Section from "@/Components/Section.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    user: Object,
});
</script>

<template>
    <Section>
        <SectionTitle>
            <template #title> Usr details </template>
            <template #description>
                <!-- Role ID: {{role.id}} -->
            </template>
            <template #aside>
                <ButtonLink
                    size="default"
                    styling="primary"
                    :href="route(`partner.users.edit`, { id: user.id })"
                    type="primary"
                    >Edit</ButtonLink
                >
            </template>
        </SectionTitle>

        <SectionBorder />

        <dl
            class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700"
        >
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">
                    Name
                </dt>
                <dd
                    class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900"
                >
                    {{ props.user.name }}
                </dd>
            </div>
            <div class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">
                    Email
                </dt>
                <dd
                    class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900"
                >
                    {{ props.user.email }}
                </dd>
            </div>
            <div v-if="role.business_id" class="flex flex-col pb-3">
                <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">
                    Roles
                </dt>
                <dd
                    class="text-lg font-semibold text-gray-900 md:text-lg dark:text-gray-900"
                >
                    <template
                        v-for="(role, role_i) in props.user.roles"
                        :key="role_i"
                    >
                        <div
                            class="block my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300"
                        >
                            {{ role.title }}
                        </div>
                    </template>
                </dd>
            </div>
        </dl>
    </Section>
</template>
