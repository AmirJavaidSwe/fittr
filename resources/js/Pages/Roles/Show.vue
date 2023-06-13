<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    role: Object,
});
</script>

<template>
    <single-view title="Role details" :description="'Role ID:' + role.id">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    size="default"
                    styling="primary"
                    v-can="{
                        module: 'roles',
                        roles: $page.props.user.user_roles,
                        permission: 'update',
                        user: $page.props.user,
                    }"
                    :href="
                        route(`${$page.props.user.source}.roles.edit`, {
                            id: role.slug,
                        })
                    "
                    type="primary"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>

        <template #item>
            <single-view-row label="Title" :even="false" :value="role.title" />
            <single-view-row label="Source" :even="true" :value="role.source" />
            <single-view-row
                v-if="role.business_id"
                label="Business ID"
                :even="false"
                :value="role.business_id"
            />
            <single-view-row label="Created At" :even="true">
                <template #value>
                    {{
                        DateTime.fromISO(role.created_at).toLocaleString(
                            DateTime.DATETIME_FULL
                        )
                    }}
                </template>
            </single-view-row>
        </template>
    </single-view>
</template>
