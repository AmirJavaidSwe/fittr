<script setup>
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    partner: Object,
});
</script>

<template>
    <single-view title="Partner details" :description="'User ID:' + partner.id">
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    size="default"
                    styling="primary"
                    v-can="{
                        module: 'partner-management',
                        roles: $page.props.user.user_roles,
                        permission: 'update',
                        user: $page.props.user,
                    }"
                    :href="route('admin.partners.edit', { id: partner.id })"
                    type="primary"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>

        <template #item>
            <single-view-row label="Name" :value="partner.name" />
            <single-view-row
                label="Email address"
                :value="partner.email"
            />
            <single-view-row label="Email verified">
                <template #value>
                    <span v-if="partner.email_verified_at"
                        >YES,
                        {{
                            DateTime.fromISO(
                                partner.email_verified_at
                            ).toRelative()
                        }}
                    </span>
                </template>
            </single-view-row>
            <single-view-row label="Date created">
                <template #value>
                    {{ DateTime.fromISO(partner.created_at) }}
                    {{ DateTime.fromISO(partner.created_at).toRelative() }}
                </template>
            </single-view-row>
        </template>
    </single-view>
</template>
