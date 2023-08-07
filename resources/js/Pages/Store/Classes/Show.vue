<script setup>
import Section from '@/Components/Section.vue';
import { useForm } from '@inertiajs/vue3';
import Avatar from '@/Components/Avatar.vue';
import { DateTime } from 'luxon';
import ButtonLink from '@/Components/ButtonLink.vue';


const props = defineProps({
    classDetail: {
        type: Object,
        required: true,
    },
    business_settings: {
        Object,
        required: true,
    },
});

const bookingForm = useForm({ class_id: ''});

const handleBooking = () => {
    bookingForm.class_id = props.classDetail.id;

    bookingForm.post(route('ss.member.bookings.store', {subdomain: props.business_settings.subdomain}));
}

const cancelBooking = () => {
    bookingForm.class_id = props.classDetail.id;

    bookingForm.post(route('ss.member.bookings.cancel', {subdomain: props.business_settings.subdomain}));
}

</script>

<template>
    <div class="container mx-auto">
        <Section bg="bg-transparent" class="flex flex-col">
            <div class="flex flex-row bg-white w-full p-4 rounded-lg">
                <div class="w-3/6 mb-4 mr-6">
                    <img class="w-full rounded-md" v-if="classDetail.studio?.location?.images?.length" :src="classDetail.studio?.location?.images[0].url" :alt="classDetail.studio?.location?.images[0].original_filename">
                </div>
                <div class="flex flex-col w-3/6">
                    <div class="flex text-3xl font-bold mb-4 items-center">
                        <span class="inline-flex mr-4">{{ classDetail.title }}</span>
                        <span class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2">Free</span>
                    </div>

                    <div class="flex flex-row mb-4">
                        <div class="flex flex-row">
                            <div class="flex mr-2">
                                <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                                <Avatar :title="classDetail.instructor?.name ?? ''" />
                            </div>
                            <div class="flex">
                                <span class="inline-flex flex-grow text-gray-800 items-center font-bold">{{ classDetail.instructor?.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="flex flex-row mb-3">
                        <div class="flex mr-2 w-5 justify-center items-center">
                            <span class="inline-block rounded-xl w-3 h-3 bg-red-500"></span>
                        </div>
                        <div>
                            {{ classDetail.class_type?.title }}
                        </div>
                    </div> -->

                    <div class="flex flex-row mb-3">
                        <div class="w-1/2 flex mr-2 items-center">
                            Class Type:
                        </div>
                        <div class="flex w-1/2 justify-end font-bold">
                            {{ classDetail.class_type?.title }}
                        </div>
                    </div>

                    <div class="flex flex-row mb-3">
                        <div class="w-1/2 flex mr-2 items-center">
                            <!-- <FontAwesomeIcon :icon="faClock" class="text-gray-400" /> -->
                            Time:
                        </div>
                        <div class="flex w-1/2 justify-end font-bold">
                            {{ DateTime.fromISO(classDetail.start_date, {zone: props.business_settings?.timezone}).toFormat('hh:mm a') }} - {{ DateTime.fromISO(classDetail.end_date, {zone: props.business_settings?.timezone}).toFormat('hh:mm a') }}
                        </div>
                    </div>

                    <div class="flex flex-row mb-3">
                        <div class="w-1/2 flex mr-2 items-center">
                            Lession Time:
                        </div>
                        <div class="flex w-1/2 justify-end font-bold">
                            {{ classDetail.duration }} minutes
                        </div>
                    </div>

                    <div class="flex flex-row mb-3">
                        <div class="w-1/2 flex mr-2 items-center">
                            Date:
                        </div>
                        <div class="flex w-1/2 justify-end font-bold">
                            {{ DateTime.fromISO(classDetail.start_date, {zone: props.business_settings?.timezone}).toFormat(business_settings.date_format?.format_js) }}
                        </div>
                    </div>
                    <div class="flex justify-end mt-3">
                        <ButtonLink v-if="classDetail.bookings?.length" styling="secondary" size="default" @click="cancelBooking">Cancel Booking</ButtonLink>
                        <ButtonLink v-else-if="$page.props.user" styling="secondary" size="default" @click="handleBooking" :class="{ 'opacity-25': bookingForm.processing }" :disabled="bookingForm.processing">Book</ButtonLink>
                        <ButtonLink v-else styling="secondary" size="default" @click="handleBooking" :class="{ 'opacity-25': bookingForm.processing }" :disabled="bookingForm.processing">Sign in to Book</ButtonLink>
                    </div>
                </div>
            </div>
        </Section>
    </div>
</template>
