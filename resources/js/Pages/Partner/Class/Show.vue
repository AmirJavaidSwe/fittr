<script setup>
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";

defineProps({
    class_lesson: {
        type: Object,
        required: true,
    },
    business_seetings: Object,
});
</script>
<template>
    <single-view
        title="Class Details"
        description="Here is the full details of the class"
    >
        <template #head>
            <div class="flex flex-row items-center mr-10">
                <ButtonLink
                    size="default"
                    styling="primary"
                    :href="route('partner.classes.edit', class_lesson)"
                >
                    Edit
                </ButtonLink>
            </div>
        </template>
        <template #item>
            <single-view-row
                label="ID"
                :even="false"
                :value="class_lesson.id"
            />

            <single-view-row
                label="Name"
                :even="true"
                :value="class_lesson.title"
            />

            <single-view-row
                label="Studio"
                :even="false"
                :value="class_lesson.studio?.title"
            />

            <single-view-row
                label="Instructor"
                :even="true"
                :value="class_lesson.instructor?.name"
            />

            <single-view-row
                label="Class Type"
                :even="true"
                :value="class_lesson.class_type?.title"
            />

            <single-view-row
                label="Start At"
                :even="false"
                :value="
                    DateTime.fromISO(class_lesson.start_date)
                        .setZone(business_seetings.timezone)
                        .toFormat(
                            business_seetings.date_format.format_js +
                                ' ' +
                                business_seetings.time_format.format_js
                        )
                "
            />

            <single-view-row
                label="End At"
                :even="true"
                :value="
                    DateTime.fromISO(class_lesson.end_date)
                        .setZone(business_seetings.timezone)
                        .toFormat(
                            business_seetings.date_format.format_js +
                                ' ' +
                                business_seetings.time_format.format_js
                        )
                "
            />

            <single-view-row
                label="Spaces"
                :even="false"
                :value="class_lesson.spaces"
            />

            <single-view-row
                label="Created At"
                :even="true"
                :value="
                    DateTime.fromISO(class_lesson.created_at)
                        .setZone(business_seetings.timezone)
                        .toFormat(business_seetings.date_format.format_js)
                "
            />

            <single-view-row
                label="Updated At"
                :even="false"
                :value="DateTime.fromISO(class_lesson.updated_at).toRelative()"
            />
        </template>
    </single-view>
</template>
