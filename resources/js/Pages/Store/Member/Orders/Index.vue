<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
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
    orders: Object,
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

const runSearch = () => {    
    form.get(route("ss.member.orders.index", {subdomain: props.business_settings.subdomain}), {
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
            <TableHead>
                Items
            </TableHead>
            <TableHead
                @click="setOrdering('payment_status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'payment_status'"
            >
                Status
            </TableHead>
            <TableHead
                @click="setOrdering('amount_total')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'amount_total'"
            >
                Order total
            </TableHead>
            
            <TableHead title="Actions" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="order in props.orders.data" :key="order.id">
                <TableData>
                    <DateValue 
                        :date="
                            DateTime.fromISO(order.created_at)
                            .setZone(business_settings.timezone)
                            .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format?.format_js)
                        "
                        />
                </TableData>
                <TableData>
                    <div v-for="item in order.items" :key="item.id">
                        <div v-tooltip="item.pack_price.priceable.description">
                            {{item.pack_price.priceable.title}}
                        </div>
                        <div>
                            {{item.pack_price.priceable.sub_title}}
                        </div>
                        <div class="font-bold">
                            {{item.amount_total_formatted}}
                            <template v-if="item.pack_price.type == 'recurring'">
                                {{item.pack_price.interval_human}}
                            </template>
                        </div>
                    </div>
                </TableData>
                <TableData>
                    {{order.payment_status}}
                </TableData>
                <TableData>
                    {{order.amount_total_formatted}}
                </TableData>
                <TableData class="text-right">
                    X
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="orders.links"
                :to="orders.to"
                :from="orders.from"
                :total="orders.total"
                @pp_changed="runSearch"
            />
        </template>
    </DataTableLayout>
</template>
