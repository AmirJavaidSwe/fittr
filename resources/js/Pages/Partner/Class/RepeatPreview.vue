<script setup>

import {useForm} from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CardIcon from '@/Components/CardIcon.vue';
import { 
    faSignHanging,
    faBookOpen,
    faUserTie,
    faLocationDot,
    faTornado,
    faRetweet
} from '@fortawesome/free-solid-svg-icons';
import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
dayjs.extend(utc);

const confirm = () => {
    form.post(route('partner.classes.store'));
};

const props = defineProps({
    form_data: Object,
    class_duration: Number,
    instructor: Object,
    repeats: Array,
    repeats_count: Number,
    studio: Object,
});

const form = useForm(props.form_data);

</script>

<template>
    <FormSection @submitted="confirm">
        <template #form>
            <div class="bg-white flex flex-wrap gap-8 my-4 p-4 rounded-lg">
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faSignHanging" />
                    </template>

                    <template #title>
                        {{props.form_data.status}}
                    </template>

                    <template #default>
                        Status
                    </template>
                </CardIcon>
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faBookOpen" />
                    </template>

                    <template #title>
                        {{props.repeats_count}}
                    </template>

                    <template #default>
                        Total Classes
                    </template>
                </CardIcon>
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faRetweet" />
                    </template>

                    <template #title>
                        {{props.class_duration}}
                    </template>

                    <template #default>
                        Duration, minutes
                    </template>
                </CardIcon>
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faUserTie" />
                    </template>

                    <template #title>
                        {{props.instructor.name}}
                    </template>

                    <template #default>
                        Instructor
                    </template>
                </CardIcon>
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faLocationDot" />
                    </template>

                    <template #title>
                        {{props.studio.title}}
                    </template>

                    <template #default>
                        Studio
                    </template>
                </CardIcon>
                <CardIcon class="w-full sm:w-auto">
                    <template #icon>
                        <font-awesome-icon :icon="faTornado" />
                    </template>

                    <template #title>
                        {{props.form_data.is_offpeak ? 'YES': 'NO'}}
                    </template>

                    <template #default>
                        Offpeak class
                    </template>
                </CardIcon>
            </div>

            <p>Starts on:</p>
            <dl class="max-w-md text-gray-900 divide-y divide-gray-200">
                <div v-for="repeat in props.repeats" class="flex flex-col py-3">
                    <dt class="mb-1 text-gray-500">{{dayjs(repeat).format('dddd')}}</dt>
                    <dd class="text-lg font-semibold">{{dayjs(repeat).utc().format('MMMM D, YYYY h:mm A UTC Z')}}</dd>
                </div>
            </dl>
        </template>

        <template #actions>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Confirm and Create {{props.repeats_count}} classes
            </PrimaryButton>
        </template>
    </FormSection>
</template>