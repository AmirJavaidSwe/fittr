<script setup>
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GeneralSettingsMenu from '@/Pages/Partner/Settings/GeneralSettingsMenu.vue';

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from "@/Components/SelectInput.vue";
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    formats_date: Array,
    formats_time: Array,
    form_data: Object,
});

const date_notes = ref(null);
const dateChanged = () => {
    let d = props.formats_date.find(({ id }) => id == form.date_format);
    date_notes.value = d?.notes ?? null;
};
const time_notes = ref(null);
const timeChanged = () => {
    let t = props.formats_time.find(({ id }) => id == form.time_format);
    time_notes.value = t?.notes ?? null;
};
onMounted(() => {
    dateChanged();
    timeChanged();
});

const form = useForm({
    date_format: props.form_data.date_format,
    time_format: props.form_data.time_format,
});

const submitForm = () => {
    form.put(route('partner.settings.general-formats'), {
        preserveScroll: true
    });
};
</script>

<template>
    <FormSection @submitted="submitForm">
        <template #description>
            <GeneralSettingsMenu />
        </template>

        <template #form>
            <!-- Date Format -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="date_format" value="Date Format" />
                <SelectInput
                    id="date_format"
                    v-model="form.date_format"
                    :options="props.formats_date"
                    option_value="id"
                    option_text="example"
                    class="mt-1 block w-full"
                    @change="dateChanged"
                >
                </SelectInput>
                <InputError :message="form.errors.date_format" class="mt-2" />
                <div v-if="date_notes" v-text="date_notes" class="mt-2"></div>
            </div>

            <!-- Time Format -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="time_format" value="Time Format" />
                <SelectInput
                    id="time_format"
                    v-model="form.time_format"
                    :options="props.formats_time"
                    option_value="id"
                    option_text="example"
                    class="mt-1 block w-full"
                    @change="timeChanged"
                >
                </SelectInput>
                <InputError :message="form.errors.time_format" class="mt-2" />
                <div v-if="time_notes" v-text="time_notes" class="mt-2"></div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
