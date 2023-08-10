<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    servicetype: {
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
                    :href="route('partner.servicetypes.edit', servicetype)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="servicetype.id" />

            <single-view-row
                :even="false"
                label="Title"
                :value="servicetype.title"
            />

            <single-view-row
                :even="true"
                label="Description"
                :value="servicetype.description"
            />

            <single-view-row
                :even="false"
                label="Created At"
                :value="DateTime.fromISO(servicetype.created_at)"
            />

            <single-view-row
                :even="true"
                label="Updated At"
                :value="DateTime.fromISO(servicetype.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
