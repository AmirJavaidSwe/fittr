<script setup>
import { ref, watch } from 'vue';
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    form_data: Object,
    business_seetings: Object,
});

const form = useForm({
    days_max_booking: props.form_data.days_max_booking ?? null,
    days_max_timetable: props.form_data.days_max_timetable ?? null,
});

const toggleBooking = ref(form.days_max_booking > 0);
const toggleTimetable = ref(form.days_max_timetable > 0);

watch(toggleBooking, (val) => {
    if(val === false){
        form.days_max_booking = null;
    }
});
watch(toggleTimetable, (val) => {
    if(val === false){
        form.days_max_timetable = null;
    }
});

const submitForm = () => {
    form
    .transform((data) => ({
        ...data,
        days_max_booking_on: toggleBooking.value,
        days_max_timetable_on: toggleTimetable.value,
    }))
    .put(route("partner.settings.bookings"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="submitForm" class="max-w-xl">
        <template #description>
            Configure class bookings limits and timetable visibility.
        </template>

        <template #form>
            <!-- toggleBooking -->
            <div class="col-span-6 sm:col-span-4">
                <Switcher
                    v-model="toggleBooking"
                    title="Limit bookings"
                    description="Enable or disable booking ability."
                />
            </div>

            <!-- days_max_booking -->
            <div v-if="toggleBooking" class="col-span-6 sm:col-span-4">
                <InputLabel for="days_max_booking" value="Classes can booked between today and this number of days from today." />
                <TextInput
                    id="days_max_booking"
                    v-model="form.days_max_booking"
                    type="number"
                    min="1"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.days_max_booking" class="mt-2" />
            </div>

            <!-- toggleTimetable -->
            <div class="col-span-6 sm:col-span-4">
                <Switcher
                    v-model="toggleTimetable"
                    title="Limit timetable visibility"
                    description="Enable to limit."
                />
            </div>

            <!-- days_max_timetable -->
            <div v-if="toggleTimetable" class="col-span-6 sm:col-span-4">
                <InputLabel for="days_max_timetable" value="Timetable range will span from current day and this number of days into the future." />
                <TextInput
                    id="days_max_timetable"
                    v-model="form.days_max_timetable"
                    type="number"
                    min="1"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.days_max_timetable" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>
