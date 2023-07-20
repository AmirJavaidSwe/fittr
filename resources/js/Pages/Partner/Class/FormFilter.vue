<script setup>
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    statuses: Object,
    instructors: Object,
    classtypes: Object,
    studios: Object,
    form: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["reset", "submitted"]);
</script>

<template>
    <FormSection modal @submitted="emit('submitted')">
        <template #form>
            <div class="flex gap-2">
                <div class="w-full">
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
                    <InputError
                        :message="form.errors.start_date"
                        class="mt-2"
                    />
                </div>
                <div class="w-full">
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
            </div>

            <!-- Status -->
            <div class="">
                <InputLabel for="status" value="Status" />
                <Multiselect mode="tags"
                    v-model="form.status"
                    :groups="true"
                    :options="[{
                        label: (form.status.length == Object.values(statuses).length) ? 'Deselect All' : 'Select All',
                        options: statuses,
                    }]"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Status"
                >
                </Multiselect>
            </div>

            <!-- Instructors -->
            <div class="">
                <InputLabel for="instructors" value="Instructor" />
                <Multiselect mode="tags"
                    :groups="true"
                    :options="[{
                        label: (form.instructor_id.length == Object.values(instructors).length) ? 'Deselect All' : 'Select All',
                        options: instructors,
                    }]"
                    v-model="form.instructor_id"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Instructor"
                >
                </Multiselect>
            </div>

            <!-- Studios -->
            <div class="">
                <InputLabel for="studios" value="Studio" />
                <Multiselect mode="tags"
                    v-model="form.studio_id"
                    :groups="true"
                    :options="[{
                        label: (form.studio_id.length == Object.values(studios).length) ? 'Deselect All' : 'Select All',
                        options: studios,
                    }]"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                >
                </Multiselect>
            </div>

            <!-- Class Type -->
            <div class="">
                <InputLabel for="classtype" value="Class Type" />
                <Multiselect mode="tags"
                    v-model="form.class_type_id"
                    :groups="true"
                    :options="[{
                        label: (form.studio_id.length == Object.values(classtypes).length) ? 'Deselect All' : 'Select All',
                        options: classtypes,
                    }]"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                >
                </Multiselect>
                <InputError :message="form.errors.class_type_id" class="mt-2" />
            </div>


            <!-- Off-peak -->
            <div class="">
                <InputLabel for="classtype" value="Peak/Off-peak" />
                <Multiselect
                    v-model="form.is_off_peak"
                    :options="[{value: '0', label: 'Peak'}, {value: '1', label: 'Off-peak'}]"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Peak/Off-peak"
                >
                </Multiselect>
                <InputError :message="form.errors.is_off_peak" class="mt-2" />
                <!-- <Switcher
                    v-model="form.is_off_peak"
                    title="Off Peak"
                    description="Would you like to tag the class as off peak?"
                /> -->
            </div>
        </template>

        <template #actions>
            <div class="flex-grow">
                <ButtonLink
                    type="button"
                    @click="emit('reset')"
                    styling="default"
                    size="default"
                >
                    <span>Reset filters</span>
                </ButtonLink>
            </div>

            <ButtonLink
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
            >
                <span>Apply Filters</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>