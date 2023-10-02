<script setup>
import { computed, ref } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vuepic/vue-datepicker/dist/main.css";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { useForm } from "@inertiajs/vue3";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import FormSection from "@/Components/FormSection.vue";
import { useSwal } from "@/Composables/swal";
import { DateTime } from "luxon";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import Avatar from "@/Components/Avatar.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    classtypes: Object,
    studios: Object,
    business_settings: Object,
    classes: Array,
});

const swal = useSwal();

const form = useForm({
    start_date: DateTime.now().toISODate(),
    end_date: DateTime.now().plus({ days: 7}).toISODate(),
    shift_period: 7,
    shift_repeat: 1,
    class_type_id: null,
    studio_id: null,
    ids: []
});

const studiosOptions = computed(() => {
    return props.studios.map(item => ({value: item.id, label: item.title}));
});

const steps = ref(['search-form', 'bulk-copy']);
const currentStep = ref(steps.value[0]);

const handleNext = () => {
    const nextStepIndex = steps.value.indexOf(currentStep.value) + 1;
    if(nextStepIndex >= steps.value.length) return;

    if(steps.value[nextStepIndex] == 'bulk-copy') {
        // if (!form.isDirty && (
        //     !form.start_date || !form.end_date || !form.class_type_id || !form.studio_id
        // )) {
        //     swal.toast({
        //         icon: 'error',
        //         title: 'Error!',
        //         text: 'Please fill the complete form to proceed.',
        //     });
        //     return;
        // }

        form.get(route('partner.classes.bulk-copy'), {
            preserveState: true,
            replace: true,
            only: ['classes'],
            onSuccess: (res) => {

                if (!res.props?.classes?.length) {
                    swal.toast({
                        icon: 'error',
                        title: 'Error',
                        text: 'No classes found in this criteria!',
                    });
                    return;
                }
                currentStep.value = steps.value[nextStepIndex];
            },
            onError: () => {
                swal.toast({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong!',
                });
            }
        });
    } else {
        currentStep.value = steps.value[nextStepIndex];
    }


};

const handleBack = () => {
    const previousStepIndex = steps.value.indexOf(currentStep.value) - 1;
    if(previousStepIndex < 0) return;
    currentStep.value = steps.value[previousStepIndex];
};

// const bulkCopyForm = useForm({
//     shift_period: 7,
//     shift_clone: 0,
// });

const handleFinish = () => {
    if (!form.ids?.length) {
        swal.toast({
            icon: 'error',
            title: 'Error!',
            text: 'Please select classes to proceed.',
        });
        return;
    }

    form.post(route('partner.classes.store-bulk-copy'));
}

const selectAllCheckboxes = () => {
    const ids = props.classes.map(item => item.id);
    form.ids = form.ids.length ? [] : ids;
};

</script>

<template>
    <FormSection>
        <template #form>
            <!-- Search Form -->
            <div v-if="currentStep == 'search-form'">
                <div class="mb-8 text-xl font-bold">
                    Selection criteria
                </div>

                <div class="mb-4">
                    <InputLabel for="start_date" value="Start Date" required />
                    <Datepicker
                        class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                        v-model="form.start_date"
                        position="left"
                        :enable-time-picker="false"
                        placeholder="Start Date"
                        timezone="UTC"
                        auto-apply
                        week-numbers
                    />
                </div>

                <div class="mb-4">
                    <InputLabel for="end_date" value="End Date" required />
                    <Datepicker
                        class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                        v-model="form.end_date"
                        position="left"
                        :enable-time-picker="false"
                        placeholder="End Date"
                        timezone="UTC"
                        auto-apply
                        week-numbers
                    />
                </div>

                <div class="mb-4">
                    <InputLabel for="shift_period" value="Shift Period (In Days)" required />
                    <TextInput
                        id="shift_period"
                        v-model="form.shift_period"
                        type="number"
                        class="mt-1 block w-full"
                        placeholder="Shift Period (In Days)"
                        min="1"
                    />
                    <InputError :message="form.errors.shift_period" class="mt-2" />
                </div>

                <div class="mb-4">
                    <InputLabel for="shift_repeat" value="Shift Repeat" required />
                    <TextInput
                        id="shift_repeat"
                        v-model="form.shift_repeat"
                        type="number"
                        class="mt-1 block w-full"
                        placeholder="Shift Repeat"
                        min="1"
                    />
                    <InputError :message="form.errors.shift_repeat" class="mt-2" />
                </div>

                <!-- Class Type -->
                <div class="mb-4">
                    <InputLabel for="classtype" value="Class Type" />
                    <Multiselect
                        v-model="form.class_type_id"
                        :options="classtypes"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        placeholder="Select Class Type"
                    >
                        <template v-slot:singlelabel="{ value }">
                            <div class="multiselect-single-label flex items-center">
                                <span class="ml-2">{{ value.label }}</span>
                            </div>
                        </template>

                        <template v-slot:option="{ option }">
                            <span class="ml-5">{{ option.label }}</span>
                        </template>
                    </Multiselect>
                    <InputError :message="form.errors.class_type_id" class="mt-2" />
                </div>

                <!-- Studios -->
                <div class="mb-4">
                    <InputLabel for="studios" value="Studio" />
                    <Multiselect
                        v-model="form.studio_id"
                        :options="studiosOptions"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        placeholder="Select Studio"
                    >
                        <template v-slot:singlelabel="{ value }">
                            <div class="multiselect-single-label flex items-center">
                                <MapMarker v-if="value.label != 'Add New'" />
                                <span class="ml-2">{{ value.label }}</span>
                            </div>
                        </template>

                        <template v-slot:option="{ option }">
                            <MapMarker v-if="option.label != 'Add New'" />
                            <span class="ml-2">{{ option.label }}</span>
                        </template>
                    </Multiselect>
                    <InputError :message="form.errors.studio_id" class="mt-2" />
                </div>
            </div>

            <!-- Class List -->
            <div v-if="currentStep == 'bulk-copy'">
                <div class="mb-8 text-xl font-bold">
                    Select Classes
                </div>
                <data-table-layout
                    :disableButton="true"
                >
                    <template #tableHead>
                        <table-head>
                            <template #checkbox>
                                <Checkbox
                                    :checked="form.ids.length > 0"
                                    class="p-2.5"
                                    @click="selectAllCheckboxes"
                                />
                            </template>
                        </table-head>
                        <table-head
                            title="Title"
                        />
                        <table-head
                            title="Location"
                        />
                        <table-head
                            title="Studio"
                        />
                        <table-head
                            title="Class Type"
                        />
                        <table-head
                            title="Instructors"
                        />
                        <table-head
                            title="Status"
                        />
                        <table-head
                            title="Date"
                        />
                        <table-head
                            title="Time"
                        />
                        <table-head
                            title="Duration"
                        />
                        <table-head
                            title="Shift, Days"
                        />
                        <table-head
                            title="New Date & Time"
                        />
                    </template>
                    <template #tableData>
                        <tr
                            v-for="(data, index) in classes"
                            :key="data.id"
                            class="h-9"
                        >

                            <table-data>
                                <label class="flex items-center">
                                    <Checkbox
                                        v-model:checked="form.ids"
                                        :checked="form.ids.includes(data.id)"
                                        :value="data.id"
                                    />
                                </label>
                            </table-data>

                            <!-- Title -->
                            <table-data>
                                <span
                                    v-if="data.title.length > 25"
                                    v-tooltip="data.title"
                                >
                                    {{ data.title.substring(0, 25) }}...
                                </span>
                                <span v-else>
                                    {{ data.title }}
                                </span>
                            </table-data>

                            <!-- Location -->
                            <table-data :title="data.studio?.location?.title" />

                            <!-- Studio -->
                            <table-data :title="data.studio?.title" />

                            <!-- Class Type -->
                            <table-data>
                                <ColoredValue
                                    color="#F47560"
                                    :title="data?.class_type?.title ?? 'Test'"
                                />
                            </table-data>

                            <!-- Instructors -->
                            <table-data class="text-center">
                                <div v-if="data?.instructors.length" class="flex items-center gap-1" v-for="instructor in data.instructors" :key="instructor.id">
                                        <Avatar
                                            :initials="instructor.initials"
                                            :imageUrl="instructor.profile_photo_url"
                                            :useIcon="true"
                                            size="xs"
                                        />
                                        {{instructor.full_name}}
                                </div>
                            </table-data>

                            <!-- Status -->
                            <table-data class="text-center">
                                <StatusLabel :status="data.status_label" />
                            </table-data>

                            <!-- Date -->
                            <table-data>
                                <DateValue
                                    :date="
                                        DateTime.fromISO(data.start_date)
                                            .setZone(business_settings.timezone)
                                            .toFormat(business_settings.date_format?.format_js)
                                    "
                                />
                            </table-data>

                            <!-- Time -->
                            <table-data>
                                <DateValue isTime :date="DateTime
                                    .fromISO(data.start_date)
                                    .setZone(business_settings.timezone)
                                    .toFormat(business_settings.time_format?.format_js)"
                                />
                            </table-data>

                            <!-- Duration -->
                            <table-data :title="data.duration + ' minutes'" />

                            <!-- Shift, Days -->
                            <table-data :title="data.shift_period + ' days'" />

                            <!-- New Date & Time -->
                            <table-data>
                                <div v-for="new_date_time in data.new_date_time">{{
                                    DateTime
                                        .fromISO(new_date_time)
                                        .setZone(business_settings.timezone)
                                        .toFormat(business_settings.date_format?.format_js +' '+ business_settings.time_format?.format_js)
                                }}</div>
                            </table-data>
                        </tr>
                    </template>
                </data-table-layout>
            </div>
        </template>
        <template #actions>
            <!-- <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage> -->

            <ButtonLink
                v-if="currentStep != 'search-form'"
                class="mr-3"
                :disabled="form.processing"
                styling="default"
                size="default"
                type="button"
                @click="handleBack"
            >
                Back
            </ButtonLink>

            <ButtonLink
                v-if="currentStep == 'bulk-copy'"
                :disabled="form.processing || !classes.length"
                styling="secondary"
                size="default"
                type="button"
                @click="handleFinish"
            >
                Finish
            </ButtonLink>
            <ButtonLink
                v-else
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="button"
                @click="handleNext"
            >
                Next
            </ButtonLink>
        </template>
    </FormSection>
</template>
