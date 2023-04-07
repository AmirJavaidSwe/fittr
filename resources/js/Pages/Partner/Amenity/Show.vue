<script setup>
import {Link} from "@inertiajs/vue3";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

dayjs.extend(relativeTime);

defineProps({
    amenity: {
        type: Object,
        required: true,
    },
})
</script>
<template>
    <single-view title="Amenity Details" description="Here is the full details of the class">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <Link
                    class="cursor-pointer h-10 inline-flex items-center justify-center rounded-md border border-transparent
                            bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none
                            focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                    :href="route('partner.amenity.edit', amenity)">
                    Edit
                </Link>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="amenity.id"/>

            <single-view-row :even="false" label="Name" :value="amenity.title"/>

            <single-view-row :even="false" label="Icon">
                <template #value>
                    <img :src="amenity.image_url" alt="icon" class="w-10 h-10">
                </template>
            </single-view-row>

            <single-view-row :even="true" label="Contents" :value="amenity.contents"/>

            <single-view-row :even="false" label="Ordering" :value="amenity.ordering"/>

            <single-view-row :even="true" label="Status" :value="amenity.status"/>

            <single-view-row :even="false" label="Created At" :value="dayjs(amenity.created_at).fromNow()"/>

        </template>
    </single-view>
</template>
