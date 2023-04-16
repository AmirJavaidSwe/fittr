<script setup>

import {Link} from "@inertiajs/vue3";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

dayjs.extend(relativeTime);

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

            <single-view-row label="Created At" :value="dayjs(exporting.created_at).fromNow()"/>

            <single-view-row label="Updated At" :value="dayjs(exporting.completed_at).fromNow()"/>

        </template>
    </single-view>
</template>
