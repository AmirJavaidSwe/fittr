<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <single-view title="Instructor Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    :href="route('partner.instructors.edit', instructor)"
                    styling="primary"
                    size="default"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="instructor.id" />

            <single-view-row
                label="Name"
                :value="instructor.name"
            />

            <single-view-row
                label="Contents"
                :value="instructor.email"
            />

            <single-view-row
                label="Created At"
                :value="DateTime.fromISO(instructor.created_at)"
            />

            <single-view-row
                label="Updated At"
                :value="DateTime.fromISO(instructor.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
