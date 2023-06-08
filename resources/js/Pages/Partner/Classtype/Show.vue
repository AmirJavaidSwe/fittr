<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    classtype: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <PrimaryButton
                    :href="route('partner.classtypes.edit', classtype)"
                >
                    Edit
                </PrimaryButton>
            </div>
        </template>
        <template #item>
            <single-view-row label="ID" :value="classtype.id" />

            <single-view-row
                :even="false"
                label="Title"
                :value="classtype.title"
            />

            <single-view-row
                :even="true"
                label="Description"
                :value="classtype.description"
            />

            <single-view-row
                :even="false"
                label="Created At"
                :value="DateTime.fromISO(classtype.created_at)"
            />

            <single-view-row
                :even="true"
                label="Updated At"
                :value="DateTime.fromISO(classtype.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
