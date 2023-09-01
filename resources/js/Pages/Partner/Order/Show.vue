<script setup>
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import CardBasic from "@/Components/CardBasic.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

defineProps({
    order: {
        type: Object,
        required: true,
    },
    business_settings: Object,
});
</script>

<template>
    <SingleView title="Summary" description="">
        <template #list>
            <SingleViewRow label="Member" :value="order.user.name"/>
            <SingleViewRow label="Items count" :value="order.line_items"/>
            <SingleViewRow label="Discount" :value="order.amount_discount_formatted"/>
            <SingleViewRow label="Subtotal" :value="order.amount_subtotal_formatted"/>
            <SingleViewRow label="Order total">
                <template #value>
                    <b>{{order.amount_total_formatted}}</b>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Checkout mode" :value="order.checkout_mode.toUpperCase()"/>
            <SingleViewRow label="Payment status">
                <template #value>
                    <b>{{order.payment_status.toUpperCase()}}</b>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Created">
                <template #value>
                    <DateValue 
                        :date="
                            DateTime.fromISO(order.created_at)
                            .setZone(business_settings.timezone)
                            .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format?.format_js)
                        "
                        >
                         ({{ DateTime.now().setZone(business_settings.timezone).toFormat('ZZZZ')}})
                    </DateValue>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Updated" :value="DateTime.fromISO(order.updated_at).toRelative()"/>
        </template>
    </SingleView>

    <CardBasic class="mt-4">
        <template #header> Order items </template>

        <DataTableLayout :disableButton="true">
            <template #tableHead>
                <TableHead>Q-ty</TableHead>
                <TableHead>Discount</TableHead>
                <TableHead>Subtotal</TableHead>
                <TableHead>Total</TableHead>
                <TableHead title="Actions" />
            </template>

            <template #tableData>
                <tr v-for="item in order.items" :key="item.id">
                    <TableData>
                        {{item.quantity}}
                    </TableData>
                    <TableData>
                        {{item.amount_discount_formatted}}
                    </TableData>
                    <TableData>
                        {{item.amount_subtotal_formatted}}
                    </TableData>
                    <TableData>
                        {{item.amount_total_formatted}}
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
        </DataTableLayout>

        <template #footer>
        </template>
    </CardBasic>
</template>
