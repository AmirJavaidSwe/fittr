<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
// import ButtonLink from "@/Components/ButtonLink.vue";
import { DateTime } from "luxon";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import {
    faPencil,
    faChevronRight,
    faPlus,
    faEye,
    faCog,
} from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
const props = defineProps({
    memberships: Object,
    pack_types: Array,
    price_types: Array,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    business_settings: Object,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const setOrdering = (col) => {
    //reverse same col order
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
    form.get(route("ss.member.memberships.index", {subdomain: props.business_settings.subdomain}), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);
</script>
<template>
    <DataTableLayout :disableButton="true">
        <template #search>
            <Search
                :noFilter="true"
                v-model="form.search"
                @reset="form.search = null"
            />
        </template>

        <template #button>
        </template>

        <template #tableHead>
            <TableHead
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            >
                <div>
                    Created
                    <span v-tooltip="DateTime.now().setZone(business_settings.timezone).toFormat('z')">
                        ({{ DateTime.now().setZone(business_settings.timezone).toFormat('ZZZZ')}})
                    </span>
                </div>
            </TableHead>
            <TableHead
                @click="setOrdering('billing_type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'billing_type'">
                Billing
            </TableHead>
            <TableHead
                @click="setOrdering('type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'type'">
                Type
            </TableHead>
            <TableHead
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'">
                Title
            </TableHead>
        </template>

        <template #tableData>
            <tr v-for="membership in props.memberships.data" :key="membership.id">
                <TableData>
                    <DateValue 
                        :date="
                            DateTime.fromISO(membership.created_at)
                            .setZone(business_settings.timezone)
                            .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format?.format_js)
                        "
                        />
                </TableData>
                <TableData>
                    {{price_types.find(({value}) => value === membership.billing_type)?.label}}
                </TableData>
                <TableData>
                    {{pack_types.find(({value}) => value === membership.type)?.label}}
                </TableData>
                <TableData>
                    {{membership.title}}
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <Pagination
                :links="memberships.links"
                :to="memberships.to"
                :from="memberships.from"
                :total="memberships.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>
</template>
