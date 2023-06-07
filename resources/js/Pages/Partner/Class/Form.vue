<script setup>
import { computed } from "vue";
import { DateTime } from "luxon";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";

import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import WarningButton from "@/Components/WarningButton.vue";
import Switcher from "@/Components/Switcher.vue";
import Avatar from "@/Components/Avatar.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import { faMapMarkerAlt } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    statuses: Object,
    instructors: Object,
    classtypes: Object,
    studios: Object,
    business_seetings: Object,
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    isNew: {
        type: Boolean,
        default: false,
    },
});

const weekDays = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
const updateWeekDays = (index) => {
    if (props.form.week_days.includes(index)) {
        const indexToRemove = props.form.week_days.indexOf(index);
        props.form.week_days.splice(indexToRemove, 1);
    } else {
        props.form.week_days.push(index);
    }
};
const formatDate = computed(() => {
    return (
        props.business_seetings.date_format?.format_js +
        " " +
        props.business_seetings.time_format?.format_js
    );
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="">
                <InputLabel for="title" value="Class Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div class="">
                <div class="mb-5">
                    <InputLabel for="start_date" value="Start Date and Time" />
                    <div
                        class="DatepickerWrap mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                    >
                        <Datepicker
                            class="border-none bg-mainBg/40"
                            v-model="form.start_date"
                            :enable-time-picker="true"
                            :flow="['calendar', 'time']"
                            :format="formatDate"
                            :timezone="business_seetings.timezone"
                            position="left"
                            placeholder="Start Date"
                            minutes-increment="1"
                            text-input
                            week-numbers
                            close-on-scroll
                            partial-flow
                            hide-offset-dates
                        />
                    </div>
                    <InputError
                        :message="form.errors.start_date"
                        class="mt-2"
                    />
                </div>

                <div class="mb-5">
                    <InputLabel for="end_date" value="End Date and Time" />
                    <Datepicker
                        class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                        v-model="form.end_date"
                        :enable-time-picker="true"
                        :flow="['calendar', 'time']"
                        :format="formatDate"
                        :timezone="business_seetings.timezone"
                        position="left"
                        placeholder="End Date"
                        minutes-increment="1"
                        text-input
                        close-on-scroll
                        partial-flow
                        hide-offset-dates
                    />
                    <InputError :message="form.errors.end_date" class="mt-2" />
                </div>
            </div>

            <!-- Status -->
            <div class="">
                <InputLabel for="status" value="Status" />
                <Multiselect
                    v-model="form.status"
                    :options="statuses"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Status"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <ColoredValue color="#ddd" :title="value.label" />
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <ColoredValue color="#ddd" :title="option.label" />
                    </template>
                </Multiselect>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Instructors -->
            <div class="">
                <InputLabel for="instructors" value="Instructor" />
                <Multiselect
                    v-model="form.instructor_id"
                    :options="instructors"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Instructor"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <Avatar size="small" :title="value.label" />
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <Avatar size="small" :title="option.label" />
                        <span class="ml-2">{{ option.label }}</span>
                    </template>
                </Multiselect>
                <InputError :message="form.errors.instructor_id" class="mt-2" />
            </div>

            <!-- Class Type -->
            <div class="">
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
                            <ColoredValue color="#ddd" :title="value.label" />
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <ColoredValue color="#ddd" :title="option.label" />
                    </template>
                </Multiselect>
                <InputError :message="form.errors.class_type_id" class="mt-2" />
            </div>

            <!-- Studios -->
            <div class="">
                <InputLabel for="studios" value="Studio" />
                <Multiselect
                    v-model="form.studio_id"
                    :options="studios"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <MapMarker />
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <MapMarker />
                        <span class="ml-2">{{ option.label }}</span>
                    </template>
                </Multiselect>
                <InputError :message="form.errors.studio_id" class="mt-2" />
            </div>

            <!-- Off-peak -->
            <div class="">
                <Switcher
                    v-model="form.is_off_peak"
                    title="Off Peak"
                    description="Would you like to tag the class as off peak?"
                />
            </div>

            <!-- repeat -->
            <div v-if="isNew">
                <Switcher
                    v-model="form.does_repeat"
                    title="Repeat"
                    description="Enable to create multiple classes."
                />
            </div>

            <div v-if="form.does_repeat">
                <InputLabel
                    for="repeat_end_date"
                    value="Repeat until (cutout date)"
                />
                <Datepicker
                    class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                    v-model="form.repeat_end_date"
                    :enable-time-picker="false"
                    placeholder="Repeat End Date"
                    text-input
                    auto-apply
                    :min-date="form.end_date"
                />
                <InputError
                    :message="form.errors.repeat_end_date"
                    class="mt-2"
                />
            </div>
            <div v-if="form.does_repeat">
                <InputLabel
                    for="studio_id"
                    value="Select which days to repeat"
                    class="mb-2"
                />
                <div class="text-xs mb-2">
                    Additional classes will be created with same settings but on
                    the days you selected until cutout date inclusive
                </div>
                <div class="items-center w-full flex flex-wrap gap-3">
                    <label
                        v-for="(weekDay, index) in weekDays"
                        :for="'weekday' + index"
                        class="bg-primary-500/10 flex font-medium gap-2 items-center p-2 lg:p-12vw lg:py-10vw rounded-xl text-dark text-sm lg:text-18vw cursor-pointer"
                    >
                        <input
                            :id="'weekday' + index"
                            type="checkbox"
                            :value="index"
                            @change="updateWeekDays(index)"
                            class="w-4 h-4 lg:w-20vw lg:h-20vw text-primary-600 !bg-transparent border-dark rounded-full focus:ring-0 focus:outline-none focus:shadow-none focus:border-0 checked:border-primary-600 checked:!bg-primary-600"
                        />
                        {{ weekDay }}
                    </label>
                </div>
                <InputError :message="form.errors.week_days" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <WarningButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="isNew">Create</span>
                <span v-else>Save changes</span>
            </WarningButton>
        </template>
    </FormSection>
</template>
