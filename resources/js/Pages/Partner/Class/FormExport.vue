<script setup>
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import WarningButton from "@/Components/WarningButton.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    statuses: Object,
    instructors: Object,
    studios: Object,
    form: {
        type: Object,
        required: true,
    },
    initiated: Boolean,
    modal: Boolean,
    ready: String,
});

const emit = defineEmits(["reset", "submitted"]);
</script>

<template>
    <div v-if="initiated" class="space-y-6 text-center">
        <p>
            Your export has been successfully initiated.<br />
            You can find your requested file in the Exports page.
        </p>

        <p v-if="ready">Export is complete. Click here to download the file.</p>
        <div v-else>Checking the status...</div>

        <ButtonLink :href="route('partner.exports.index')" type="primary">
            Go to Exports
        </ButtonLink>
    </div>
    <FormSection v-else :modal="modal" @submitted="emit('submitted')">
        <template #title> Filters </template>

        <template #description>
            Apply filters to limit results or leave empty to export all records.
        </template>

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
                <Multiselect
                    v-model="form.status"
                    :options="statuses"
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
                <Multiselect
                    v-model="form.instructor_id"
                    :options="instructors"
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
                <Multiselect
                    v-model="form.studio_id"
                    :options="studios"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                >
                </Multiselect>
            </div>

            <!-- File Type -->
            <div class="w-full">
                <InputLabel for="file_type" value="File Type" />
                <select-input
                    class="w-full"
                    v-model="form.file_type"
                    option_value="id"
                    option_text="name"
                    :options="[
                        {
                            id: 'csv',
                            name: 'CSV',
                        },
                        {
                            id: 'xls',
                            name: 'Excel xls',
                        },
                        {
                            id: 'xlsx',
                            name: 'Excel xlsx',
                        },
                    ]"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select Studio"
                >
                </select-input>
            </div>

            <!-- Off-peak -->
            <div class="">
                <Switcher
                    v-model="form.is_off_peak"
                    title="Off Peak"
                    description="Would you like to tag the class as off peak?"
                />
            </div>
        </template>

        <template #actions>
            <div class="flex-grow text-left">
                <SecondaryButton type="button" @click="emit('reset')" class="">
                    <span>Reset filters</span>
                </SecondaryButton>
            </div>

            <WarningButton
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <span>Export</span>
            </WarningButton>
        </template>
    </FormSection>
</template>
