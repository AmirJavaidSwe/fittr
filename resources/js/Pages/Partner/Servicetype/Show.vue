<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";

defineProps({
    servicetype: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-wrap">
        <single-view title="Details" description="service type" class="w-full lg:w-1/2">
            <template #head>
                <div class="flex flex-row items-center mr-10">
                    <ButtonLink
                        styling="primary"
                        size="default"
                        :href="route('partner.servicetypes.edit', servicetype)"
                    >
                        Edit
                    </ButtonLink>
                </div>
            </template>
            <template #item>
                <single-view-row 
                    label="ID"
                    :value="servicetype.id" />

                <single-view-row
                    label="Status"
                >
                    <template #value>
                        <ColoredValue :color="statuses.find(el => el.value == servicetype.status).color" :title="statuses.find(el => el.value == servicetype.status).label " />
                    </template>
                </single-view-row>

                <single-view-row
                    label="Title"
                    :value="servicetype.title"
                />

                <single-view-row
                    label="Description"
                    :value="servicetype.description"
                />

                <single-view-row
                    label="Created At"
                    :value="DateTime.fromISO(servicetype.created_at).toLocaleString(DateTime.DATETIME_HUGE)"
                />

                <single-view-row
                    label="Updated At"
                    :value="DateTime.fromISO(servicetype.updated_at).toRelative()"
                />
            </template>
        </single-view>
         <div v-if="servicetype.images.length" class="flex-grow">
            <div class="w-80">
                <img v-for="image in servicetype.images" :src="image.url" :alt="image.filename">
            </div>
        </div>
    </div>
</template>
