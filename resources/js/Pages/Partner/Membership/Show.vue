<script setup>
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

defineProps({
    membership: {
        type: Object,
        required: true,
    },
    business_settings: Object,
});
</script>

<template>
    <SingleView title="Summary" description="">
        <template #list>
            <SingleViewRow label="Member" :value="membership.user.name"/>
            <SingleViewRow label="Type" :value="membership.type"/>
            <SingleViewRow label="Title" :value="membership.title"/>
            <SingleViewRow label="Subtitle" :value="membership.sub_title" v-if="membership.sub_title" />
            <SingleViewRow label="Description" :value="membership.description" v-if="membership.description" />
            <SingleViewRow label="Has restrictions?" :value="membership.is_restricted ? 'YES' : 'No'"/>
            <SingleViewRow label="Restrictions" v-if="membership.is_restricted">
                <template #value>
                    <b>{{membership.restrictions}}</b>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Billing" :value="membership.billing_type"/>
            <SingleViewRow label="Order Discount" :value="membership.order.amount_discount_formatted"/>
            <SingleViewRow label="Order Subtotal" :value="membership.order.amount_subtotal_formatted"/>
            <SingleViewRow label="Order total">
                <template #value>
                    <b>{{membership.order.amount_total_formatted}}</b>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Created">
                <template #value>
                    <DateValue 
                        :date="
                            DateTime.fromISO(membership.created_at)
                            .setZone(business_settings.timezone)
                            .toFormat(business_settings.date_format.format_js +' ' +business_settings.time_format?.format_js)
                        "
                        >
                         ({{ DateTime.now().setZone(business_settings.timezone).toFormat('ZZZZ')}})
                    </DateValue>
                </template>
            </SingleViewRow>
            <SingleViewRow label="Updated" :value="DateTime.fromISO(membership.updated_at).toRelative()"/>
        </template>
    </SingleView>
</template>
