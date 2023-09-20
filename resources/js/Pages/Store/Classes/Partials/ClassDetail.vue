<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/tailwind.css'
import ClockIcon from '@/Icons/ClockIcon.vue';
import LocationIcon from '@/Icons/LocationIcon.vue';
import { DateTime, Duration, Interval } from 'luxon';
import Avatar from '@/Components/Avatar.vue';
import ColoredValue from '@/Components/DataTable/ColoredValue.vue';
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import SideModal from '@/Components/SideModal.vue';
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import { useWindowSize } from '@/Composables/window_size';
import { useSwal } from '@/Composables/swal';
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import AttendiesSelection from "./AttendiesSelection.vue";
import AttendiesSelected from "./AttendiesSelected.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ArrowLeft from '@/Components/ArrowLeft.vue';
import ArrowRight from '@/Components/ArrowRight.vue';

const props = defineProps(["is_cancel_able_booking", "business_settings", "class_types", "instructors", 'show', 'classDetails', 'user', 'is_family_booking', 'bookingForm', 'selected_family_members', 'other_family_member_booking_ids'])
const emit = defineEmits(['close', 'isFamilyBooking', 'bookForOtherFamilyMembers', 'addRemoveFromWaitlist', 'cancelBooking', 'handleBooking'])

</script>
<template>
    <SideModal :show="props.show" @close="$emit('close')">
        <template #title>Details</template>
        <template #content>
            <!-- <div class="w-full mb-4">
                <img class="w-full rounded-md" v-if="props.classDetails.studio?.location?.images?.length"
                    :src="props.classDetails.studio?.location?.images[0].url" :alt="props.classDetails.studio?.location?.images[0]
                            .original_filename
                        " />
            </div> -->
            <div class="flex flex-col">
                <div class="flex text-3xl font-bold mb-4 items-center">
                    <div class="flex flex-grow mr-4">{{ props.classDetails.title }}</div>
                    <div class="flex flex-col shrink-0">
                        <div v-if="classDetails.is_free" class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2 justify-center mb-2">Free</div>
                        <div class="inline-flex text-sm font-normal rounded-lg text-white p-2 justify-center"
                            :class="{ 'bg-danger-600': !props.classDetails.spaces_left, 'bg-gray-500': props.classDetails.spaces_left }">
                            {{ props.classDetails.spaces_left > 0 ? 'Not Full' : 'Full' }}</div>
                    </div>
                </div>

                <div class="flex flex-row mb-4">
                    <div class="flex flex-row">
                        <div class="flex flex-col mr-2">
                            <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                            <template v-if="classDetails?.instructor.length">
                                <template v-for="(
                                        instructor, ins
                                    ) in classDetails?.instructor" :key="ins">
                                    <AvatarValue
                                        class="cursor-pointer mb-3"
                                        :title="instructor?.name ?? 'Demo Ins'"
                                        :useIcon="true"
                                    />
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center"></div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ props.classDetails.class_type?.title }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Time:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ props.classDetails.start_date?.toFormat("hh:mm a") }} -
                        {{ props.classDetails.end_date?.toFormat("hh:mm a") }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Lession Time:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ props.classDetails.duration }} minutes
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Date:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ props.classDetails.start_date?.toFormat(props.business_settings.date_format?.format_js) }}
                    </div>
                </div>

                <div v-if="props.classDetails.waitlists?.length" class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Waiting list:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ props.classDetails.waitlists?.length }}
                    </div>
                </div>
            </div>
            <AttendiesSelection :user="props.user" :current_class="props.classDetails" :is_family_booking="props.is_family_booking" @is-family-booking="$emit('isFamilyBooking', $event)" />
            <AttendiesSelected :selected_family_members="props.selected_family_members" :user="props.user" :other_family_member_booking_ids="props.other_family_member_booking_ids" :current_class="props.classDetails" :is_family_booking="props.is_family_booking" />
        </template>
        <template #footer>
            <ButtonLink class="mr-2" styling="default" size="default" @click="$emit('close')">Close</ButtonLink>

            <ButtonLink v-if="!$page.props.user" styling="secondary" size="default" @click="$emit('handleBooking')"
                :disabled="bookingForm.processing">Sign in to Book</ButtonLink>
            <ButtonLink v-else-if="props.is_cancel_able_booking" styling="secondary" size="default" @click="$emit('cancelBooking')"
                :disabled="bookingForm.processing">Cancel Booking</ButtonLink>
            <div v-else-if="!props.classDetails.spaces_left" class="inline-flex">
                <ButtonLink v-if="!props.classDetails.has_family && props.classDetails.is_waiting" styling="secondary" size="default" @click="$emit('addRemoveFromWaitlist', 3)" :disable="disableButton"
                    >Remove from waitlist</ButtonLink>
                    <ButtonLink v-else-if="!props.classDetails.has_family && !props.classDetails.is_waiting" styling="secondary" size="default" @click="$emit('addRemoveFromWaitlist', 1)" :disable="disableButton"
                    >Add to waitlist</ButtonLink>
                <ButtonLink v-else styling="secondary" size="default" @click="$emit('addRemoveFromWaitlist', 2)" :disable="disableButton"
                    >Add or Remove from waitlist</ButtonLink>
            </div>
            <ButtonLink v-else-if="!props.classDetails.is_booked" styling="secondary" size="default" @click="$emit('handleBooking')" :disabled="props.bookingForm.processing">
                Book</ButtonLink>
            <ButtonLink v-else-if="!props.is_cancel_able_booking" styling="secondary" size="default" @click="$emit('bookForOtherFamilyMembers', props.classDetails)" :disabled="props.bookingForm.processing">
                Book For Other Family Members</ButtonLink>
        </template>
    </SideModal>


</template>
