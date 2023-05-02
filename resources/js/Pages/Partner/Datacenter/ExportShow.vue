<script setup>

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
        <single-view title="Details"
                     :isEven="true">

        <template #item>
            <single-view-row label="ID"
                             :isEven="true"
                             :value="exporting.id"/>

            <single-view-row label="File Name"
                             :value="exporting.file_name"/>

            <single-view-row label="Type"
                             :value="exporting.file_type"/>

            <single-view-row label="Filters"
                             :value="exporting.filters"/>


            <single-view-row>
                <template #label>
                    <span class="text-sm font-medium text-gray-500">
                        Created By
                    </span>
                </template>
                <template #value>
                    <span class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ exporting.created_by }}
                    </span>
                </template>
            </single-view-row>

            <single-view-row label="Created At"
                             :isEven="true"
                             :value="dayjs(exporting.created_at).fromNow()"/>

            <single-view-row label="Updated At"
                             :value="dayjs(exporting.completed_at).fromNow()"/>

        </template>
    </single-view>
</template>
