<script setup>

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
                             :value="DateTime.fromISO(exporting.created_at).toRelative()"/>

            <single-view-row label="Created At" :value="DateTime.fromISO(exporting.created_at).toRelative()"/>

            <single-view-row label="Completed" :value="DateTime.fromISO(exporting.completed_at)"/>

        </template>
    </single-view>
</template>
