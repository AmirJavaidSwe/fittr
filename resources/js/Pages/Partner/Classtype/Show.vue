<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";

defineProps({
    classtype: {
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
        <single-view title="Details" description="class type" class="w-full lg:w-1/2">
            <template #head>
                <div class="flex flex-row items-center mr-10">
                    <ButtonLink
                        styling="primary"
                        size="default"
                        :href="route('partner.classtypes.edit', classtype)"
                    >
                        Edit
                    </ButtonLink>
                </div>
            </template>
            <template #item>
                <single-view-row
                    label="Status"
                >
                    <template #value>
                        <ColoredValue :color="statuses.find(el => el.value == classtype.status).color" :title="statuses.find(el => el.value == classtype.status).label " />
                    </template>
                </single-view-row>

                <single-view-row
                    label="Title"
                    :value="classtype.title"
                />

                <single-view-row
                    label="Description"
                    :value="classtype.description"
                />

                <single-view-row
                    label="Created At"
                    :value="DateTime.fromISO(classtype.created_at).toLocaleString(DateTime.DATETIME_HUGE)"
                />

                <single-view-row
                    label="Updated At"
                    :value="DateTime.fromISO(classtype.updated_at).toRelative()"
                />
            </template>
        </single-view>
        <div v-if="classtype.images.length" class="flex-grow">
            <div class="w-80">
                <img v-for="image in classtype.images" :src="image.url" :alt="image.filename">
            </div>
        </div>
    </div>
</template>
