<script setup>
import { computed, watch } from "vue";
import { DateTime } from "luxon";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Switcher from "@/Components/Switcher.vue";
import Avatar from "@/Components/Avatar.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const emit = defineEmits(["createNewInstructor", "createNewClassType", "createNewStudio", "onPreview"]);
const props = defineProps({
    statuses: Object,
    instructors: Object,
    classtypes: Object,
    studios: Object,
    business_settings: Object,
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
    isDuplicate: {
        type: Boolean,
        default: false,
        required: false,
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
        props.business_settings?.date_format?.format_js +
        " " +
        props.business_settings?.time_format?.format_js
    );
});
const instructorChanged = () => {
    if(props.form.instructor_id.includes('create_new_instructor')) {
        emit('createNewInstructor')
    }
}
const classTypeChanged = () => {
    if(props.form.class_type_id == 'create_new_class_type') {
        emit('createNewClassType')
    }
}
const studioChanged = () => {
    if(props.form.studio_id == 'create_new_studio') {
        emit('createNewStudio')
    }
}

const instructorsList = computed(() => {
    let newInstructorsList = { ...props.instructors }; // Create a shallow copy of the object
    newInstructorsList.create_new_instructor = "Add New"; // Add a new property
    return newInstructorsList;
});

const classTypeList = computed(() => {
    let newClassTypeList = { ...props.classtypes }; // Create a shallow copy of the object
    newClassTypeList.create_new_class_type = "Add New"; // Add a new property
    return newClassTypeList;
});

const studiosOptions = computed(() => {
    return props.studios.map(item => ({value: item.id, label: item.title}));
});

const defaultCapacity = computed(() => {
    const studio = props.studios.filter(item => props.form.studio_id == item.id)[0];
    if(studio) {
        const classTypeStudio = studio.class_type_studios.filter(item => props.form.class_type_id == item.class_type_id)[0];
        return classTypeStudio?.spaces ?? 0;
    }
    return 0;
});

const studioList = computed(() => {
    let newStudioList = studiosOptions.value;
    newStudioList.push({
        'label': "Add New",
        'value': "create_new_studio"
    })
    return newStudioList;
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div>
                <InputLabel for="title" value="Class Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div>
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
                        :timezone="business_settings?.timezone"
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

            <div>
                <InputLabel for="duration" value="Class Duration, minutes" />
                <div class="flex gap-2">
                    <TextInput
                        v-model="form.duration"
                        type="range"
                        min="10"
                        max="120"
                        :value="form.duration" step="5"
                        class="flex-grow cursor-pointer"
                         list="values"
                    />
                    <datalist id="values">
                        <option value="30" label="30"></option>
                        <option value="45" label="45"></option>
                        <option value="60" label="60"></option>
                        <option value="75" label="75"></option>
                        <option value="90" label="90"></option>
                    </datalist>
                    <TextInput
                        id="duration"
                        v-model="form.duration"
                        type="number"
                        min="1"
                        max="600"
                        class="w-20"
                    />
                </div>
                <InputError :message="form.errors.duration" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="">
                <InputLabel for="status" value="Status" />
                <Multiselect
                    id="status"
                    v-model="form.status"
                    :options="statuses"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Status"
                >
                    <template v-slot:singlelabel="{ value  }">
                        <div class="multiselect-single-label flex items-center">
                            <ColoredValue :color="value.color" :title="value.label" />
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <ColoredValue :color="option.color" :title="option.label" />
                    </template>

                </Multiselect>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Instructors -->
            <div class="">
                <InputLabel for="instructors" value="Instructors" />
                <Multiselect
                    id="instructors"
                    :mode="'tags'"
                    v-model="form.instructor_id"
                    :options="instructorsList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Instructors"
                    @select="instructorChanged"
                >
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <Avatar size="small" :title="value.label" v-if="value.label != 'Add New'"/>
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <Avatar size="small" :title="option.label" v-if="option.label != 'Add New'" />
                        <span class="ml-5">{{ option.label }}</span>
                    </template>
                </Multiselect>
                <InputError :message="form.errors.instructor_id" class="mt-2" />
            </div>

            <!-- Class Type -->
            <div class="">
                <InputLabel for="classtype" value="Class Type" />
                <Multiselect
                    id="classtype"
                    v-model="form.class_type_id"
                    :options="classTypeList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Class Type"
                    @select="classTypeChanged"
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
            <div class="">
                <InputLabel for="studios" value="Studio" />
                <Multiselect
                    id="studios"
                    v-model="form.studio_id"
                    :options="studioList"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                    @select="studioChanged"
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

            <!-- Off-peak -->
            <div class="">
                <Switcher
                    v-model="form.is_off_peak"
                    title="Off Peak"
                    description="Would you like to tag the class as off peak?"
                />
            </div>

            <!-- Free class -->
            <div>
                <Switcher
                    v-model="form.is_free"
                    title="Free class"
                    :description="form.is_free ?
                    'This class can be booked by anyone. No membership required to book.' :
                    'Active membership required to book.'"
                />
            </div>

            <!-- repeat -->
            <div>
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
                        v-for="(weekDay, index) in weekDays" :key="index"
                        :for="'weekday' + index"
                        class="bg-primary-500/10 flex font-medium gap-2 items-center p-2 rounded-xl text-dark text-sm lg:text-md cursor-pointer"
                    >
                        <input
                            :id="'weekday' + index"
                            type="checkbox"
                            :value="index"
                            @change="updateWeekDays(index)"
                            class="w-4 h-4 lg:w-4 lg:h-4 text-primary-600 !bg-transparent border-dark rounded-full focus:ring-0 focus:outline-none focus:shadow-none focus:border-0 checked:border-primary-600 checked:!bg-primary-600"
                        />
                        {{ weekDay }}
                    </label>
                </div>
                <InputError :message="form.errors.week_days" class="mt-2" />
            </div>
            <div class="">
                <InputLabel
                    value="Class Capacity"
                    class="mb-2"
                />

                <div class="flex mt-1 items-center">
                    <div v-if="form.use_defaults" class="mr-2">Default:</div>
                    <div v-if="form.use_defaults" class="flex flex-grow mr-2">{{ defaultCapacity }}</div>
                    <TextInput
                        v-else
                        id="title"
                        v-model="form.spaces"
                        type="number"
                        min="1"
                        max="1000"
                        class="mr-2 block w-full"
                    />
                    <ButtonLink v-if="form.use_defaults" type="button" size="small" styling="secondary" @click="form.use_defaults = false">Change</ButtonLink>
                </div>


            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                v-if="isNew"
                styling="default"
                size="default"
                type="button"
                class="mr-3"
                @click="$emit('onPreview')"
            >
                Preview
            </ButtonLink>

            <ButtonLink
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
            >
                <span v-if="isNew">Create</span>
                <span v-else-if="isDuplicate">Duplicate</span>
                <span v-else>Save changes</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>
