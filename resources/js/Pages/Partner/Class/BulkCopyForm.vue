<script setup>
import { ref, computed } from "vue";
import Multiselect from "@vueform/multiselect";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import Avatar from "@/Components/Avatar.vue";
import MapMarker from "@/Icons/MapMarker.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    classtypes: Object,
    studios: Object,
    business_settings: Object,
});

const form = useForm({
    start_date: null,
    end_date: null,
    class_type_id: [],
    studio_id: [],
});

const studiosOptions = computed(() => {
    return props.studios.map(item => ({value: item.id, label: item.title}));
});


</script>

<template>
    <div class="mb-4">
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

    <div class="mb-4">
        <InputLabel for="end_date" value="End Date and Time" />
        <Datepicker
            class="mt-1 block w-full"
            v-model="form.end_date"
            :enable-time-picker="true"
            :flow="['calendar', 'time']"
            :format="formatDate"
            :timezone="business_settings?.timezone"
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
    <div class="flex justify-end">
        <ActionMessage :on="form.recentlySuccessful" class="mr-3">
            Saved.
        </ActionMessage>

        <ButtonLink

            :disabled="form.processing"
            styling="secondary"
            size="default"
            type="submit"
        >
            Next
        </ButtonLink>
    </div>
</template>
