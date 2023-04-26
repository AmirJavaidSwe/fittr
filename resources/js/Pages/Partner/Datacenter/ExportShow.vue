<script setup>

import {Link} from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

defineProps({
    exporting: {
        type: Object,
        required: true,
    },
})

</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <!-- <Link>
                    Download
                </Link> -->
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="exporting.id"/>

            <single-view-row label="filters" :value="exporting.filters.start_date"/>

            <single-view-row label="Created At" :value="DateTime.fromISO(exporting.created_at)"/>

            <single-view-row label="Completed" :value="DateTime.fromISO(exporting.completed_at).toRelative()"/>

        </template>
    </single-view>
</template>
