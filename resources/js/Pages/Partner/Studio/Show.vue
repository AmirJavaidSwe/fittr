<script setup>

import {Link} from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

defineProps({
    studio: {
        type: Object,
        required: true,
    },
})

</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <Link
                    class="cursor-pointer h-10 inline-flex items-center justify-center rounded-md border border-transparent
                            bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none
                            focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                    :href="route('partner.studios.edit', studio)">
                    Edit
                </Link>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="studio.id"/>

            <single-view-row :even="false" label="Title" :value="studio.title"/>

            <single-view-row :even="true" label="Ordering" :value="studio.ordering"/>

            <single-view-row :even="false" label="Created At" :value="DateTime.fromISO(studio.created_at)"/>

            <single-view-row :even="true" label="Updated At" :value="DateTime.fromISO(studio.updated_at).toRelative()"/>

        </template>
    </single-view>
</template>
