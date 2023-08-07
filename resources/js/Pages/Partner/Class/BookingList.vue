<script setup>

import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import { DateTime } from "luxon";
import DateValue from "@/Components/DataTable/DateValue.vue";
import Pagination from "@/Components/Pagination.vue";

defineProps({
    type: {
        type: String,
        default: 'booking',
    },
    data: {
        required: true,
        type: Object,
    },
    business_seetings: {
        required: true,
        type: Object
    },
    form: {
        required: true,
        type: Object
    }
});

defineEmits(['setPerPage']);

</script>
<template>
    <data-table-layout
        :disableButton="true"
    >
        <template #tableHead>
            <table-head
                title="Name"
            />
            <table-head
                title="Email"
            />
            <!-- <table-head
                v-if="type === 'booking'"
                title="Status"
            /> -->
            <table-head
                title="Created At"
            />
        </template>
        <template #tableData>
            <tr
                v-for="(dt, index) in data.data"
                :key="dt.id"
            >
                <table-data>
                    {{ dt.user?.name }}
                </table-data>
                <table-data>
                    {{ dt.user?.email }}
                </table-data>
                <!-- <table-data v-if="type === 'booking'">
                    {{ dt.status_text }}
                </table-data> -->
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(dt.created_at)
                                .setZone(business_seetings.timezone)
                                .toFormat(business_seetings.date_format?.format_js)
                        "
                    />
                </table-data>
            </tr>
        </template>
        <template #pagination>
            <Pagination
                :per_page="form.per_page"
                :links="data.links"
                :to="data.to"
                :from="data.from"
                :total="data.total"
                @pp_changed="(id) => $emit('setPerPage', id)"
            />
        </template>
    </data-table-layout>
</template>