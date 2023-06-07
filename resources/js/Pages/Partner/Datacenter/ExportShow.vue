<script setup>
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    exporting: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <single-view title="Details" :isEven="true">
        <template #item>
            <single-view-row label="ID" :value="exporting.id" />

            <single-view-row
                label="File Name"
                :even="false"
                :value="exporting.file_name"
            />

            <single-view-row label="Type" :value="exporting.mime_type" />

            <single-view-row
                label="Filters"
                :even="false"
                :value="exporting.filters"
            />

            <single-view-row
                label="Storage Disk"
                :value="exporting.storage_disk"
            />

            <single-view-row :even="false">
                <template #label>
                    <span class="text-sm font-medium text-gray-500">
                        Created By
                    </span>
                </template>
                <template #value>
                    <Link
                        class="text-blue-600"
                        :href="
                            route('admin.partners.show', exporting.created_by)
                        "
                    >
                        {{ exporting.user.name }}
                    </Link>
                </template>
            </single-view-row>

            <single-view-row
                label="Created At"
                :value="DateTime.fromISO(exporting.created_at).toRelative()"
            />

            <single-view-row
                label="Created At"
                :even="false"
                :value="DateTime.fromISO(exporting.created_at).toRelative()"
            />

            <single-view-row
                label="Completed"
                :value="DateTime.fromISO(exporting.completed_at).toRelative()"
            />
        </template>
    </single-view>
</template>
