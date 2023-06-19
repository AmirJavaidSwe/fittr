<script setup>
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    studio: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    styling="primary"
                    size="default"
                    :href="route('partner.studios.edit', studio)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row :even="false" label="ID" :value="studio.id" />

            <single-view-row
                :even="true"
                label="Title"
                :value="studio.title"
            />

            <single-view-row
                :even="false"
                label="Location"
                :value="studio.location?.title"
            />

            <single-view-row
                :even="true"
                label="Ordering"
                :value="studio.ordering"
            />

            <single-view-row
                :even="false"
                label="Created At"
                :value="DateTime.fromISO(studio.created_at).toString()"
            />

            <single-view-row
                :even="true"
                label="Updated At"
                :value="DateTime.fromISO(studio.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
