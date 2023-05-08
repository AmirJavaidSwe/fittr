<script setup>

import {Link} from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

defineProps({
    classpack: {
        type: Object,
        required: true,
    },
    business_seetings: Object,
})

</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <Link
                    class="cursor-pointer h-10 inline-flex items-center justify-center rounded-md border border-transparent
                            bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none
                            focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
                    :href="route('partner.classpacks.edit', classpack)">
                    Edit
                </Link>
            </div>
        </template>
        <template #item>
            <single-view-row :even="false" label="ID" :value="classpack.id"/>
            <single-view-row :even="true" label="Title" :value="classpack.title"/>
            <single-view-row :even="false" label="Number of sessions" :value="classpack.sessions"/>
            <single-view-row :even="true" label="Pack is active" :value="classpack.is_active ? 'Yes':'no'"/>
            <single-view-row :even="false" label="Price" :value="classpack.price"/>
            <single-view-row :even="true" label="Type" :value="classpack.type"/>
            <single-view-row :even="false" label="Intro pack" :value="classpack.is_intro ? 'Yes':'no'"/>
            <single-view-row :even="true" label="Has restrictions" :value="classpack.is_restricted ? 'Yes':'no'"/>
            <single-view-row :even="false" label="Private pack" :value="classpack.is_private ? 'Yes':'no'"/>
            <single-view-row :even="true" label="Private URL" :value="classpack.private_url"/>
            <single-view-row :even="false" label="Renewable pack" :value="classpack.is_renewable ? 'Yes':'no'"/>
            <single-view-row :even="true" label="Sessions expire" :value="classpack.is_expiring ? 'Yes':'no'"/>
            <single-view-row :even="false" label="Expiration period multiplier" :value="classpack.expiration"/>
            <single-view-row :even="true" label="Expiration period" :value="classpack.expiration_period"/>
            <single-view-row :even="false" label="Pack active from" :value="classpack.active_from ? DateTime.fromISO(classpack.active_from).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js) : null"/>
            <single-view-row :even="true" label="Pack active to" :value="classpack.active_to ? DateTime.fromISO(classpack.active_to).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js) : null"/>
            <single-view-row :even="false" label="Created At" :value="DateTime.fromISO(classpack.created_at).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js)"/>
            <single-view-row :even="true" label="Updated At" :value="DateTime.fromISO(classpack.updated_at).toRelative()"/>
        </template>
    </single-view>
</template>
