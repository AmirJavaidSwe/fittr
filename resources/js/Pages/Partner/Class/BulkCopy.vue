<script setup>
import { computed, ref } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
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
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    classtypes: Object,
    studios: Object,
    business_settings: Object,
    classes: Array,
});

const swal = useSwal();

const form = useForm({
    start_date: null,
    end_date: null,
    class_type_id: null,
    studio_id: null,
});

const studiosOptions = computed(() => {
    return props.studios.map(item => ({value: item.id, label: item.title}));
});

const steps = ref(['search-form', 'class-list', 'bulk-copy']);
const currentStep = ref(steps.value[0]);

const handleNext = () => {
    const nextStepIndex = steps.value.indexOf(currentStep.value) + 1;
    if(nextStepIndex >= steps.value.length) return;

    if(steps.value[nextStepIndex] == 'class-list') {
        if (!form.isDirty && (
            !form.start_date || !form.end_date || !form.class_type_id || !form.studio_id
        )) {
            swal.toast({
                icon: 'error',
                title: 'Error!',
                text: 'Please fill the complete form to proceed.',
            });
            return;
        }
        form.get(route('partner.classes.bulk-copy'), {
            preserveState: true,
            replace: true,
            only: ['classes'],
            onSuccess: () => {
                currentStep.value = steps.value[nextStepIndex];
                bulkCopyForm.shift_clone = props.classes?.length;
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

const bulkCopyForm = useForm({
    shift_period: 7,
    shift_clone: 0,
});

const handleFinish = () => {
    if (!props.classes?.length) {
        swal.toast({
            icon: 'error',
            title: 'Error!',
            text: 'Invalid selection criteria',
        });
        return;
    }

    bulkCopyForm.transform((data) => ({
        ...data,
        ...form.data(),
    }))
    .post(route('partner.classes.store-bulk-copy'));
}

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
                    <InputLabel for="start_date" value="Start Date" />
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
                    <InputLabel for="end_date" value="End Date" />
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
                </div>
            </div>

            <!-- Class List -->
            <div v-if="currentStep == 'class-list'">
                <div class="mb-8 text-xl font-bold">
                    Selected Classes
                </div>
                <data-table-layout
                    :disableButton="true"
                >
                    <template #tableHead>
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
                    </template>
                    <template #tableData>
                        <tr
                            v-for="(data, index) in classes"
                            :key="data.id"
                            class="h-9"
                        >

                            <!-- Title -->
                            <table-data :title="data.title" />

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
                                <template v-if="data?.instructor.length">
                                    <template
                                        v-for="(
                                            instructor, ins
                                        ) in data?.instructor"
                                        :key="ins"
                                    >
                                        <AvatarValue
                                            class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                                            :onlyTooltip="true"
                                            :title="instructor?.name ?? 'Demo Ins'"
                                        />
                                    </template>
                                </template>
                                <template v-else>
                                    <AvatarValue :title="'Demo Ins'" />
                                </template>
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
                        </tr>
                    </template>
                </data-table-layout>
            </div>

            <!-- Alter Selected Classes -->
            <div v-if="currentStep == 'bulk-copy'">
                <div class="mb-8 text-xl font-bold">
                    Alter Selected Classes
                </div>

                <div class="mb-4">
                    <InputLabel for="shift_period" value="Shift Period (In Days)" />
                    <TextInput
                        id="shift_period"
                        v-model="bulkCopyForm.shift_period"
                        type="number"
                        class="mt-1 block w-full"
                        placeholder="Shift Period (In Days)"
                        min="1"
                    />
                    <InputError :message="bulkCopyForm.errors.shift_period" class="mt-2" />
                </div>

                <div class="mb-4">
                    <InputLabel for="shift_clone" value="Shift Clone (No. of classes)" />
                    <TextInput
                        id="shift_clone"
                        v-model="bulkCopyForm.shift_clone"
                        type="number"
                        class="mt-1 block w-full"
                        placeholder="Shift Clone (No. of classes)"
                        min="1"
                    />
                    <InputError :message="bulkCopyForm.errors.shift_clone" class="mt-2" />
                </div>
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
                :disabled="currentStep == 'class-list' && !classes.length"
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
