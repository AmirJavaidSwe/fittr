<script setup>

import Multiselect from '@vueform/multiselect';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import '@vueform/multiselect/themes/tailwind.css';

import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    statuses: Object,
    instructors: Object,
    studios: Object,
    form: {
        type: Object,
        required: true
    },
    submitted: {
        type: Function,
        required: true
    },
    isNew: {
        type: Boolean,
        default: false
    }
});

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
const updateWeekDays = (index) => {
    if (props.form.week_days.includes(index)) {
        const indexToRemove = props.form.week_days.indexOf(index);
        props.form.week_days.splice(indexToRemove, 1);
    } else {
        props.form.week_days.push(index);
    }
}

</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="">
                <InputLabel for="name" value="Class Name"/>
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    />
                <InputError :message="form.errors.name" class="mt-2"/>
            </div>

            <div class="">
                <div class="mb-5">
                    <InputLabel for="start_date" value="Start Date and Time"/>
                    <Datepicker
                        class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300
                               focus:bg-white focus:ring-0"
                        v-model="form.start_date"

                        position="left"
                        :enable-time-picker="true"
                        placeholder="Start Date"
                        minutes-increment="1"
                        text-input
                        timezone="UTC"
                        hide-offset-dates
                        week-numbers
                        close-on-scroll
                        partial-flow
                        :flow="['calendar', 'time']"
                        />
                    <InputError :message="form.errors.start_date" class="mt-2"/>
                </div>

                <div class="mb-5">
                    <InputLabel for="end_date" value="End Date and Time"/>
                    <Datepicker
                        class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300
                               focus:bg-white focus:ring-0"
                        v-model="form.end_date"

                        position="left"
                        :enable-time-picker="true"
                        placeholder="End Date"
                        minutes-increment="1"
                        text-input
                        timezone="UTC"
                        close-on-scroll
                        partial-flow
                        :flow="['calendar', 'time']"
                        hide-offset-dates
                        :min-date="form.start_date"/>
                    <InputError :message="form.errors.end_date" class="mt-2"/>
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
                    placeholder="Status">
                </Multiselect>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Instructors -->
            <div class="">
                <InputLabel for="instructors" value="Instructor"/>
                <Multiselect
                    v-model="form.instructor_id"
                    :options="instructors"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Instructor"
                    >
                </Multiselect>
                <InputError :message="form.errors.instructor_id" class="mt-2"/>
            </div>

            <!-- Studios -->
            <div class="">
                <InputLabel for="studios" value="Studio"/>
                <Multiselect
                    v-model="form.studio_id"
                    :options="studios"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio">
                </Multiselect>
                <InputError :message="form.errors.studio_id" class="mt-2"/>
            </div>

            <!-- Off-peak -->
            <div class="">
                <Switcher
                    v-model="form.is_offpeak"
                    title="Off Peak"
                    description="Would you like to tag the class as off peak?"/>
            </div>

            <!-- repeat -->
            <div v-if="isNew">
                <Switcher
                    v-model="form.does_repeat"
                    title="Repeat"
                    description="Enable to create multiple classes."/>
            </div>

            
            <div v-if="form.does_repeat">
                <InputLabel for="repeat_end_date" value="Repeat until (cutout date)"/>
                <Datepicker
                    class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300
                            focus:bg-white focus:ring-0"
                    v-model="form.repeat_end_date"
                    :enable-time-picker="false"
                    placeholder="Repeat End Date"
                    text-input
                    auto-apply
                    timezone="UTC"
                    :min-date="form.end_date"
                    />
                <InputError :message="form.errors.repeat_end_date" class="mt-2"/>
            </div>
            <div v-if="form.does_repeat">
                <InputLabel for="studio_id"
                            value="Select which days to repeat"
                            class="mb-2"/>
                <div class="text-xs">Additional classes will be created with same settings but on the days you selected until cutout date inclusive</div>
                <div class="items-center w-full text-sm font-medium text-gray-900 bg-white flex flex-wrap text-white gap-2 cursor-pointer">
                    <label 
                        v-for="(weekDay, index) in weekDays"
                        :for="'weekday'+index" class="border border-gray-200 flex font-medium gap-4 items-center p-3 py-2 rounded-md text-gray-900 text-sm">
                        <input :id="'weekday'+index"
                            type="checkbox"
                            :value="index"
                            @change="updateWeekDays(index)"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-full focus:ring-blue-500 focus:ring-blue-600 ring-offset-gray-700 focus:ring-2"
                        >
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

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                <span v-if="isNew">Create</span>
                <span v-else>Save changes</span>                
            </PrimaryButton>
        </template>
    </FormSection>
</template>
