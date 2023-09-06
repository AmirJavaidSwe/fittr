<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import Search from "@/Components/DataTable/Search.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import Pagination from "@/Components/Pagination.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    orders: Object,
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

const runSearch = () => {
    form.get(route("partner.orders.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

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

// form.search getter only;
watch(() => form.search, runSearch);
</script>
<template>
    <DataTableLayout :disableButton="true">
        <template #button>
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :noFilter="true"
                :disable-search="disableSearch"
                @reset="form.search = null"
            />
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
                Member
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
            <TableHead title="Actions" />
        </template>

        <template #tableData>
            <tr v-for="order in orders.data" :key="order.id">
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
                    <ButtonLink :href="route('partner.members.show', order.user)" v-tooltip.right="'View member'">
                        {{order.user.name}}
                    </ButtonLink>
                    ({{order.user.email}})
                </TableData>
                <TableData v-for="item in order.items" :key="item.id">
                    <div class="flex flex-wrap justify-between gap-2">
                        <ButtonLink 
                            :href="route('partner.packs.show', item.pack_price.priceable_id)"
                            v-tooltip.right="'View membership pack'
                            ">
                            {{item.pack_price.priceable.title}}
                        </ButtonLink>
                        <ButtonLink 
                            :href="route('partner.memberships.show', item.membership)"
                            v-tooltip.right="'View membership'"
                            v-if="item.membership"
                            >
                            membership
                        </ButtonLink>
                    </div>
                    <div class="font-bold">
                        {{item.amount_total_formatted}}
                        <template v-if="item.pack_price.type == 'recurring'">
                            {{item.pack_price.interval_human}}
                        </template>
                    </div>
                </TableData>
                <TableData>
                    {{order.payment_status}}
                </TableData>
                <TableData>
                    {{order.amount_total_formatted}}
                </TableData>
                <TableData>
                    <div class="flex gap-4">
                        <ButtonLink :href="route('partner.orders.show', order.id)">
                            Details
                        </ButtonLink>
                    </div>
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <Pagination
                :links="orders.links"
                :to="orders.to"
                :from="orders.from"
                :total="orders.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>
</template>
