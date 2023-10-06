<script setup>
import { Link } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-wrap">
        <single-view title="Instructor Details" description="">
            <template #head>
                <div class="flex flex-row items-center mr-10">
                    <ButtonLink
                        :href="route('partner.instructors.edit', instructor)"
                        styling="primary"
                        size="default"
                    >
                        Edit
                    </ButtonLink>
                </div>
            </template>
            <template #item>
                <single-view-row label="First Name" :value="instructor.first_name" />
                <single-view-row label="Last Name" :value="instructor.last_name" />
                <single-view-row label="Phone" :value="instructor.phone" />
                <single-view-row label="Email" :value="instructor.email" />

                <single-view-row
                    label="Created At"
                    :value="DateTime.fromISO(instructor.created_at).toLocaleString(DateTime.DATETIME_HUGE)"
                />

                <single-view-row
                    label="Updated At"
                    :value="DateTime.fromISO(instructor.updated_at).toRelative()"
                />
            </template>
        </single-view>
        <div v-if="instructor.profile.images.length" class="flex-grow">
            <div class="w-80">
                <img v-for="image in instructor.profile.images" :src="image.url" :alt="image.filename">
            </div>
        </div>
    </div>
</template>
