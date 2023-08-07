<script setup>
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import GeneralSettingsMenu from "@/Pages/Partner/Settings/GeneralSettingsMenu.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import GeneralSettingsVerticalTabs from "@/Pages/Partner/Settings/GeneralSettingsVerticalTabs.vue";
import FormSectionVertical from "@/Components/FormSectionVertical.vue";
import CalendarIcon from "@/Icons/CalendarIcon.vue";
import TimeIcon from "@/Icons/TimeIcon.vue";

const props = defineProps({
    formats_date: Array,
    formats_time: Array,
    form_data: Object,
});

const date_notes = computed(() => {
    let d = props.formats_date.find(({ id }) => id == form.date_format);
    return d?.notes ?? null;
});
const time_notes = computed(() => {
    let t = props.formats_time.find(({ id }) => id == form.time_format);
    return t?.notes ?? null;
});

const form = useForm({
    date_format: props.form_data.date_format.id,
    time_format: props.form_data.time_format.id,
});

const submitForm = () => {
    form.put(route("partner.settings.general-formats"), {
        preserveScroll: true,
    });
};
</script>

<template>

    <GeneralSettingsMenu class="lg:hidden" />
    <FormSectionVertical @submitted="submitForm">
        <template #tabsList>
            <GeneralSettingsVerticalTabs />
        </template>
        <template #heading>
            <h3 class="text-2xl pt-3 pb-3 font-bold">Formats</h3>
        </template>
        <template #form>
            <div class="col-span-12 sm:col-span-4 mt-4">
            <div class="relative">
                <span class="absolute top-7 lg:top-8 left-2">
                    <CalendarIcon />
                </span>
                <InputLabel for="date_format" value="Date Format" />
                <SelectInput id="date_format" v-model="form.date_format" :options="props.formats_date"
                    option_value="id" option_text="example" class="p-12 mt-1 block w-full">
                </SelectInput>
            </div>
            <InputError :message="form.errors.date_format" class="mt-2" />
            <div v-if="date_notes" v-text="date_notes" class="mt-2"></div>
        </div>

        <!-- Time Format -->
        <div class="col-span-12 sm:col-span-4 mt-4">
            <div class="relative">
                <span class="absolute top-7 lg:top-8 left-2">
                    <TimeIcon />
                </span>
                <InputLabel for="time_format" value="Time Format" />
                <SelectInput id="time_format" v-model="form.time_format" :options="props.formats_time"
                    option_value="id" option_text="example" class="p-12 mt-1 block w-full">
                </SelectInput>
            </div>
            <InputError :message="form.errors.time_format" class="mt-2" />
            <div v-if="time_notes" v-text="time_notes" class="mt-2"></div>
        </div>
        </template>
        <template #actions>
            <div class="flex mt-5">
                <ActionMessage :on="form.recentlySuccessful" class="font-semibold mr-3 mt-3">
                    Saved.
                </ActionMessage>
                <ButtonLink styling="secondary" size="default" :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Save
                </ButtonLink>
            </div>
        </template>
    </FormSectionVertical>

</template>
