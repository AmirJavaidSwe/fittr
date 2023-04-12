<script setup>

import {Link} from "@inertiajs/vue3";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";

dayjs.extend(relativeTime);

defineProps({
    classtype: {
        type: Object,
        required: true,
    },
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
                    :href="route('partner.classtypes.edit', classtype)">
                    Edit
                </Link>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="classtype.id"/>

            <single-view-row :even="false" label="Title" :value="classtype.title"/>

            <single-view-row :even="true" label="Description" :value="classtype.description"/>

            <single-view-row :even="false" label="Created At" :value="dayjs(classtype.created_at).fromNow()"/>

            <single-view-row :even="true" label="Updated At" :value="dayjs(classtype.updated_at).fromNow()"/>

        </template>
    </single-view>
</template>
