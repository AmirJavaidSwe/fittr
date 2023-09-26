<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    member: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <single-view title="Details" description="second line">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    styling="primary"
                    size="default"
                    :href="route('partner.members.edit', member)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>

        <template #item>
            <single-view-row label="ID" :value="member.id" />

            <single-view-row label="First Name" :value="member.first_name" />
            <single-view-row label="Last Name" :value="member.last_name" />
            <single-view-row label="Phone" :value="member.phone" />
            <single-view-row label="Email" :value="member.email" />

            <single-view-row
                label="Created At"
                :value="DateTime.fromISO(member.created_at)"
            />

            <single-view-row
                label="Updated At"
                :value="DateTime.fromISO(member.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
