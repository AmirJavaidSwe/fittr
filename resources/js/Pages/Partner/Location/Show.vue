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

            <single-view-row label="Title" :value="location.title"/>

            <single-view-row label="Description" :value="location.brief"/>


            <single-view-row label="Url" :value="location.url"/>

            <single-view-row label="Checkin Url" :value="location.checkin_url"/>

            <single-view-row label="General Manager" :value="location.manager?.name"/>

            <single-view-row label="Email (General Manager)" :value="location.manager?.email"/>

            <single-view-row label="Address 1" :value="location.address_line_1"/>

            <single-view-row label="Address 2" :value="location.address_line_2"/>

            <single-view-row label="Country" :value="location.country?.name"/>

            <single-view-row label="City" :value="location.city"/>

            <single-view-row label="Post Code" :value="location.postcode"/>

            <single-view-row label="Latitude" :value="location.map_latitude"/>

            <single-view-row label="Longitude" :value="location.map_longitude"/>

            <single-view-row label="Phone" :value="location.tel"/>

            <single-view-row label="Email" :value="location.email"/>

            <single-view-row label="Ordering" :value="location.ordering"/>

            <single-view-row label="Status" :value="location.status ? 'Active' : 'Inactive'"/>

            <single-view-row label="Created At" :value="DateTime.fromISO(location.created_at).toString()"/>

            <single-view-row label="Updated At" :value="DateTime.fromISO(location.updated_at).toRelative()"/>

             <single-view-row label="Image">
                <template #value>
                    <div v-for="image in location.images" class="inline-block w-1/5 rounded-xl border-gray-200 mr-2">
                        <img :src="image.url" :alt="image.original_filename">
                    </div>
                </template>
             </single-view-row>

             <single-view-row label="Amenities">
                <template #value>
                    <div v-for="amenity in location.amenities" class="inline-block bg-gray-200 rounded-lg p-2 mr-2">
                        {{ amenity.title }}
                    </div>
                </template>
             </single-view-row>

             <single-view-row label="Studios">
                <template #value>
                    <div v-for="studio in location.studios" class="inline-block bg-gray-200 rounded-lg p-2 mr-2">
                        {{ studio.title }}
                    </div>
                </template>
             </single-view-row>

        </template>
    </single-view>
</template>
