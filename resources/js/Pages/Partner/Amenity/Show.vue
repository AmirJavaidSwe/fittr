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
                :even="false"
                label="Name"
                :value="amenity.title"
            />

            <single-view-row :even="false" label="Icon">
                <template #value>
                    <img
                        :src="amenity.image_url"
                        alt="icon"
                        class="w-10 h-10"
                    />
                </template>
            </single-view-row>

            <single-view-row
                :even="true"
                label="Contents"
                :value="amenity.contents"
            />

            <single-view-row
                :even="false"
                label="Ordering"
                :value="amenity.ordering"
            />

            <single-view-row
                :even="true"
                label="Status"
                :value="amenity.status"
            />

            <single-view-row
                :even="false"
                label="Created At"
                :value="DateTime.fromISO(amenity.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
