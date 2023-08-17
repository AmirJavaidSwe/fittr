<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    amenity: {
        type: Object,
        required: true,
    },
});
</script>
<template>
    <single-view
        title="Amenity Details"
        description="Here is the full details of the class"
    >
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    styling="primary"
                    size="default"
                    :href="route('partner.amenity.edit', amenity)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="amenity.id" />

            <single-view-row
                label="Title"
                :value="amenity.title"
            />

            <single-view-row
                label="Status"
                :value="amenity.status"
            />

            <single-view-row
                label="Created At"
                :value="DateTime.fromISO(amenity.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
