<script setup>
import SingleView from "@/Components/DataTable/SingleView.vue";
import SingleViewRow from "@/Components/DataTable/SingleViewRow.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import BookingList from "./BookingList.vue";
import { watch } from "vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import EmailClass from "./EmailClass.vue";
import { ref } from "vue";
import Avatar from '@/Components/Avatar.vue';

const props = defineProps({
    class_lesson: {
        type: Object,
        required: true,
    },
    bookings: {
        type: Object,
        required: true,
    },
    waitlists: {
        type: Object,
        required: true,
    },
    cancellations: {
        type: Object,
        required: true,
    },
    business_settings: Object,
    per_page: Number,
    type: String,
});

const bookingForm = useForm({
    per_page: props.per_page,
    type: 'bookings',
});

const waitlistForm = useForm({
    per_page: props.per_page,
    type: 'waitlists',
});

const cancellationForm = useForm({
    per_page: props.per_page,
    type: 'cancellations',
});

const setPerPage = (n, type) => {
    if(type == 'bookings')
        bookingForm.per_page = n;
    else if(type == 'cancellations')
        cancellationForm.per_page = n;
    else
        waitlistForm.per_page = n;

    runSearch(type == 'bookings' ? bookingForm : (type == 'cancellations' ? cancellationForm : waitlistForm));
};

const runSearch = (form) => {
    form.get(route("partner.classes.show", props.class_lesson?.id), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const showEmailClass = ref(false);
const emailClassData = ref({});

const emailClass = (classLesson) => {
    emailClassData.value = { ...classLesson };
    showEmailClass.value = true;
};

</script>
<template>
    <single-view
        title="Class Details"
        description="Here is the full details of the class"
    >
        <template #head>
            <div class="flex flex-row shrink-0 items-center mr-10">
                <ButtonLink
                    size="default"
                    styling="secondary"
                    @click="emailClass(class_lesson)"
                    class="mr-3"
                >
                    Email class
                </ButtonLink>

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
                :value="class_lesson.id"
            />

            <single-view-row
                label="Name"
                :value="class_lesson.title"
            />

            <single-view-row
                label="Location"
                :value="class_lesson.studio?.location?.title"
            />

            <single-view-row
                label="Studio"
                :value="class_lesson.studio?.title"
            />

            <single-view-row
                label="Class Type"
                :value="class_lesson.class_type?.title"
            />

            <single-view-row
                label="Instructors"
            >
                <template #value>
                <div v-if="class_lesson.instructors" class="flex items-center gap-1 mb-1" v-for="instructor in class_lesson.instructors" :key="instructor.id">
                    <Avatar
                        :initials="instructor.initials"
                        :imageUrl="instructor.profile_photo_url"
                        :useIcon="true"
                        size="medium"
                    />
                    {{instructor.full_name}}
                </div>
                </template>
            </single-view-row>

            <single-view-row
                label="Status"
            >
                <template #value>
                    <StatusLabel :status="class_lesson.status_label" />
                </template>
            </single-view-row>

            <single-view-row
                label="Capacity Status"
            >
                <template #value>
                    <div
                        class="inline-flex text-sm font-normal rounded-full text-white p-2 px-3 justify-center"
                        :class="{'bg-danger-600': !class_lesson.spaces_left, 'bg-gray-500': class_lesson.spaces_left}"
                    >
                        {{ !class_lesson.spaces_left ? 'Full' : 'Not Full'}}
                    </div>
                </template>
            </single-view-row>

            <single-view-row
                label="Start At"
                :value="
                    DateTime.fromISO(class_lesson.start_date)
                        .setZone(business_settings.timezone)
                        .toFormat(
                            business_settings.date_format.format_js +
                                ' ' +
                                business_settings.time_format.format_js
                        )
                "
            />

            <single-view-row
                label="End At"
                :value="
                    DateTime.fromISO(class_lesson.end_date)
                        .setZone(business_settings.timezone)
                        .toFormat(
                            business_settings.date_format.format_js +
                                ' ' +
                                business_settings.time_format.format_js
                        )
                "
            />

            <single-view-row
                label="Class Capacity"
                :value="class_lesson.spaces ? class_lesson.spaces : 'Default: '+class_lesson.default_spaces"
            />

            <single-view-row
                label="Created At"
                :value="
                    DateTime.fromISO(class_lesson.created_at)
                        .setZone(business_settings.timezone)
                        .toFormat(business_settings.date_format.format_js)
                "
            />

            <single-view-row
                label="Updated At"
                :value="DateTime.fromISO(class_lesson.updated_at).toRelative()"
            />
        </template>
        <template #list>
            <div class="px-4 py-5 sm:px-6">
                <div class="text-lg font-medium">
                    <a id="bookings">Bookings</a>
                </div>
                <BookingList
                    :form="bookingForm"
                    :data="bookings"
                    :business_settings="business_settings"
                    @setPerPage="(n) => setPerPage(n, 'bookings')"
                />
            </div>

            <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                <div class="text-lg font-medium">
                    <a id="waitlists">Waitlists</a>
                </div>
                <BookingList
                    type="waitlist"
                    :form="waitlistForm"
                    :data="waitlists"
                    :business_settings="business_settings"
                    @setPerPage="(n) => setPerPage(n, 'waitlists')"
                />
            </div>

            <div class="px-4 py-5 sm:px-6 border-t border-gray-200">
                <div class="text-lg font-medium">
                    <a id="cancellations">Cancellations</a>
                </div>
                <BookingList
                    type="cancellation"
                    :form="cancellationForm"
                    :data="cancellations"
                    :business_settings="business_settings"
                    @setPerPage="(n) => setPerPage(n, 'cancellations')"
                />
            </div>
        </template>
    </single-view>
    <EmailClass
        :show="showEmailClass"
        :classDetails="emailClassData"
        :business_settings="business_settings"
        @close="showEmailClass = false"
    />
</template>
