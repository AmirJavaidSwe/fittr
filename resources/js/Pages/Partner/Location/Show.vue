<script setup>

import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    location: {
        type: Object,
        required: true,
    },
})

</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    styling="primary"
                    size="default"
                    :href="route('partner.locations.edit', location)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="location.id"/>

            <single-view-row :even="false" label="Title" :value="location.title"/>

            <single-view-row :even="true" label="Description" :value="location.brief"/>


            <single-view-row :even="false" label="Url" :value="location.url"/>

            <single-view-row :even="true" label="Checkin Url" :value="location.checkin_url"/>

            <single-view-row :even="false" label="General Manager" :value="location.manager?.name"/>

            <single-view-row :even="true" label="Email (General Manager)" :value="location.manager?.email"/>

            <single-view-row :even="false" label="Address 1" :value="location.address_line_1"/>

            <single-view-row :even="true" label="Address 2" :value="location.address_line_2"/>

            <single-view-row :even="false" label="Country" :value="location.country?.name"/>

            <single-view-row :even="true" label="City" :value="location.city"/>

            <single-view-row :even="false" label="Post Code" :value="location.postcode"/>

            <single-view-row :even="true" label="Latitude" :value="location.map_latitude"/>

            <single-view-row :even="false" label="Longitude" :value="location.map_longitude"/>

            <single-view-row :even="true" label="Phone" :value="location.tel"/>

            <single-view-row :even="false" label="Email" :value="location.email"/>

            <single-view-row :even="true" label="Ordering" :value="location.ordering"/>

            <single-view-row :even="false" label="Status" :value="location.status ? 'Active' : 'Inactive'"/>

            <single-view-row :even="true" label="Created At" :value="DateTime.fromISO(location.created_at).toString()"/>

            <single-view-row :even="false" label="Updated At" :value="DateTime.fromISO(location.updated_at).toRelative()"/>

             <single-view-row :even="true" label="Image">
                <template #value>
                    <div v-for="image in location.images" class="inline-block w-1/5 rounded-xl border-gray-200 mr-2">
                        <img :src="image.url" :alt="image.original_filename">
                    </div>
                </template>
             </single-view-row>

             <single-view-row :even="false" label="Amenities">
                <template #value>
                    <div v-for="amenity in location.amenities" class="inline-block bg-gray-200 rounded-lg p-2 mr-2">
                        {{ amenity.title }}
                    </div>
                </template>
             </single-view-row>

             <single-view-row :even="true" label="Studios">
                <template #value>
                    <div v-for="studio in location.studios" class="inline-block bg-gray-200 rounded-lg p-2 mr-2">
                        {{ studio.title }}
                    </div>
                </template>
             </single-view-row>

        </template>
    </single-view>
</template>
