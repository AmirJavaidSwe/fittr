<script setup>
import { watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    stripe_events: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const setOrdering = (col) => {
    if (form.order_by == col) {
        form.order_dir = form.order_dir == "asc" ? "desc" : "asc";
    }
    form.order_by = col;
    runSearch();
};

const setPerPage = (n) => {
    form.per_page = n;
    runSearch();
};

const runSearch = () => {
    form.get(route("admin.se.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);
</script>

<template>
    <DataTableLayout :disable-search="false" :disableButton="true">
        <template #search>
            <Search :noFilter="true" v-model="form.search" @reset="form.search = null" />
        </template>

        <template #tableHead>
            <TableHead
                title="Id"
                @click="setOrdering('id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'id'"
            />
            <TableHead
                title="Event ID"
                @click="setOrdering('stripe_id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'stripe_id'"
            />
            <TableHead
                title="Account"
                @click="setOrdering('connected_account')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'connected_account'"
            />
            <TableHead title="Business" />
            <TableHead
                title="Type"
                @click="setOrdering('type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'type'"
            />
            <TableHead
                title="For"
                @click="setOrdering('event_for')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'event_for'"
            />
            <TableHead
                title="Processed"
                @click="setOrdering('is_processed')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'is_processed'"
            />
            <TableHead
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            >
                <div>
                    Created
                    <span v-tooltip="DateTime.now().toFormat('z')">
                        ({{ DateTime.now().toFormat('ZZZZ')}})
                    </span>
                </div>
            </TableHead>
        </template>

        <template #tableData>
            <tr v-for="(se, index) in props.stripe_events.data" :key="se.id">
                <TableData>{{ se.id }}</TableData>
                <TableData>
                    <ButtonLink :href="route('admin.se.show', se.id)">
                        {{ se.stripe_id }}
                    </ButtonLink>
                </TableData>
                <TableData>{{ se.connected_account }}</TableData>
                <TableData>{{ se?.business?.name.val }}</TableData>
                <TableData>{{ se.type }}</TableData>
                <TableData>{{ se.event_for }}</TableData>
                <TableData>{{ se.is_processed ? 'Yes' : 'No' }}</TableData>
                <TableData>
                    <DateValue :date="DateTime.fromISO(se.created_at).toLocaleString(DateTime.DATETIME_MED)" />
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="stripe_events.links"
                :to="stripe_events.to"
                :from="stripe_events.from"
                :total="stripe_events.total"
                @pp_changed="runSearch"
            />
        </template>
    </DataTableLayout>
</template>
