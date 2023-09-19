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
import { Splide, SplideSlide } from '@splidejs/vue-splide';
import { useWindowSize } from '@/Composables/window_size';
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import WaitlistModal from "./Partials/WaitlistModal.vue";
import FamilyBooking from "./Partials/FamilyBooking.vue";
import OtherFamilyMembersBooking from "./Partials/OtherFamilyMembersBooking.vue";
import CancelBooking from "./Partials/CancelBooking.vue";
import ClassDetail from "./Partials/ClassDetail.vue";
import ArrowLeft from '@/Components/ArrowLeft.vue';
import ArrowRight from '@/Components/ArrowRight.vue';
import WaiverListing from "./WaiverListing.vue";

const props = defineProps({
    classes: {
        type: Object,
        required: true,
    },
    business_settings: {
        Object,
        required: true,
    },
    class_types: {
        type: Array,
        required: true,
    },
    instructors: {
        type: Array,
        required: true,
    },
    waivers: {
        type: Object,
        required: true,
    },
    user_waiver_ids: {
        type: Array,
        required: true,
    },
    signed_waiver_ids: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    class_type: [],
    instructor: [],
    is_off_peak: "",
    date: "",
});

const timetableEl = ref(null);
const classesEl = ref(null);
const timetable = ref([]);

const onDateChange = (date) => {
    form.date = date.toSQLDate();
    // onSearch();
};

const user = ref(usePage().props.user);

const onSearch = () => {
    form.transform((data) => ({
        class_type: data.class_type,
        instructor: data.instructor,
    })).get(
        route("ss.classes.index", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: ["classes"],
        }
    );
};

onBeforeMount(() => {
    let queryParams = new URLSearchParams(window.location.search);

    if (queryParams.get("class_type"))
        form.class_type = queryParams.get("class_type");
    if (queryParams.get("instructor"))
        form.instructor = queryParams.get("instructor");
    // if(queryParams.get('time'))
    //     form.time = queryParams.get('time');

    let start = DateTime.now().setZone(props.business_settings.timezone);
    let end = DateTime.now().setZone(props.business_settings.timezone);
    let noOfDays =
        parseInt(
            props.business_settings.days_max_timetable ?? start.daysInMonth
        ) - 1; // Note: need to discuss the approach about showing unlimited dates here.
    end = end.plus(Duration.fromObject({ days: noOfDays }));

    let intervalObj = Interval.fromDateTimes(start, end);
    timetable.value = intervalObj.splitBy({ days: 1 }).map((d) => d.start);

    if (queryParams.get("date")) {
        form.date = queryParams.get("date");
    }
});

onMounted(() => {
    const timetableSplide = timetableEl.value?.splide;
    const classesSplide = classesEl.value?.splide;

    if (timetableSplide && classesSplide) {
        timetableEl.value?.sync(classesEl.value?.splide);
    }
});

watch(
    () => ({ class_type: form.class_type, instructor: form.instructor }),
    onSearch
);

const modal = ref(false);
const classDetails = ref({});

const showModal = (data) => {
    modal.value = true;
    data.start_date = DateTime.fromISO(data.start_date, { zone: props.business_settings?.timezone });
    data.end_date = DateTime.fromISO(data.end_date, { zone: props.business_settings?.timezone });
    classDetails.value = { ...data };
    let user_id = usePage().props?.user?.id;

    classDetails.value.has_family = user.value?.family?.length ? true : false
    classDetails.value.is_waiting = typeof classDetails.value.waitlists?.filter(item => item.user_id === user_id)[0] !== 'undefined';
}

const resetFamilyBookingDetails = () => {
    selectedFamilyMembers.value.user_id = user?.value?.id
    selectedFamilyMembers.value.family_member.ids = []
}

const closeModal = () => {
    modal.value = false;
    classDetails.value = {};
    resetFamilyBookingDetails()
};

const bookingForm = useForm({ class_id: "" });

const showWaiverModal = ref(false)
const signed_waiver_ids = computed(() => props.signed_waiver_ids)
const user_waiver_ids = computed(() => props.user_waiver_ids)

const handleBooking = () => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    bookingForm.class_id = classDetails.value.id;
    bookingForm.transform((data) => ({
        ...data,
        familyBooking: selectedFamilyMembers.value,
        is_family_booking: (is_family_booking.value == 2) ? true : false,
    })).post(
        route("ss.member.bookings.store", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            onSuccess: (res) => {
                if (res.props.flash.type === "success") {
                    closeModal();
                }
            },
        }
    );
};


const showCancelBookingModal = ref(false)
const cancelBookingAll = () => {

    bookingForm.transform((data) => ({
        class_id: classDetails.value.id,
        ids_cancellation: idsCancellation.value,
    })).post(
        route("ss.member.bookings.cancel-all", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            onSuccess: (res) => {
                if (res.props.flash.type === "success") {
                    idsCancellation.value = null
                    bookForOtherClass.value = null
                    showCancelBookingModal.value = false
                    modal.value = false
                }
            },
        }
    );
}
const cancelBooking = () => {
    let filtered;
    if (classDetails.value.user_bookings.length) {
        filtered = classDetails.value.user_bookings.filter(item => (item.user_id == user.value.id && item.is_family_booking == 1 && item.family_member_id != null))
    }

    if (filtered.length) {
        idsCancellation.value = null
        bookForOtherClass.value = classDetails.value
        showCancelBookingModal.value = true
        return;
    }
    bookingForm.class_id = classDetails.value.id;

    bookingForm.transform((data) => ({
            ...data,
            id: classDetails.value.user_bookings[0].id,
        })).post(
        route("ss.member.bookings.cancel", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            onSuccess: (res) => {
                if (res.props.flash.type === "success") {
                    closeModal();
                }
            },
        }
    );
};


const showWaitlistModal = ref(false)
const disableButton = ref(false)
const addRemoveUsersToWaitlist = ref(0)

// param values
// 1, just add current user to waitlist
// 2, for current user and family  add / remove to waitlist
// 3, remove current user from waitlist

const addRemoveFromWaitlist = (param) => {
    showWaitlistModal.value = true
    addRemoveUsersToWaitlist.value = param
}


const { screen } = useWindowSize();

const calendarPerPage = computed(() => {
    switch (true) {
        case screen.value == "2xl":
            return 6;
        case screen.value == "xl":
            return 5;
        case screen.value == "lg":
            return 4;
        case screen.value == "md":
            return 3;
        case screen.value == "sm":
            return 2;
        default:
            return 1;
    }
});

const classesPerPage = computed(() => {
    switch (true) {
        case screen.value == "2xl":
            return 6;
        case screen.value == "xl":
            return 5;
        case screen.value == "lg":
            return 4;
        case screen.value == "md":
            return 3;
        case screen.value == "sm":
            return 2;
        default:
            return 1;
    }
});

const handleMoved = (splide, index, prevIndex) => {
    const date = timetable.value[index].toSQLDate();
    form.date = date;
};

const handlePrev = () => {
    classesEl.value.go('<');
};

const handleNext = () => {
    classesEl.value.go('>');
};

const showFamilyBookingModal = ref(false);
const is_family_booking = ref(1);

const isFamilyBooking = ($event) => {
    if ($event.is_family_booking == 2) {
        is_family_booking.value = 2
        showFamilyBookingModal.value = true;
    } else {
        is_family_booking.value = 1
        showFamilyBookingModal.value = false;
        selectedFamilyMembers.value.user_id = user?.value?.id
        selectedFamilyMembers.value.family_member.ids = []
    }
};

const selectedFamilyMembers = ref({
    user_id: user?.value?.id,
    family_member: {
        ids: []
    }
})

const addRemoveFamilyMember = (isParent, id) => {
    if (isParent) {
        if (selectedFamilyMembers.value.user_id !== null) {
            selectedFamilyMembers.value.user_id = null
        } else {
            selectedFamilyMembers.value.user_id = user.value.id
        }
    } else if (!isParent) {
        const filtered = selectedFamilyMembers.value.family_member.ids.filter((item) => item == id)
        if (filtered.length) {
            const filtered = selectedFamilyMembers.value.family_member.ids.filter((item) => item != id)
            selectedFamilyMembers.value.family_member.ids = filtered
        } else {
            selectedFamilyMembers.value.family_member.ids.push(id)
        }
    }
}

const isCancelAbleBooking = (classDetails) => {
    if (classDetails.is_booked && user?.value?.id && (/* !classDetails.spaces_left || */ (classDetails.user_bookings.length == (user.value?.family?.length + 1)))) {
        return true
    }
    return false
}

const showBookForOtherFamilyMembersModal = ref(false)
const bookForOtherClass = ref(null)
const otherFamilyMemberBookingIds = ref(null)
const bookForOtherFamilyMembers = (classDetails) => {
    showBookForOtherFamilyMembersModal.value = true
    bookForOtherClass.value = classDetails
}
const closeBookForOtherFamilyMembersModal = (param) => {
    if (!param) {
        showBookForOtherFamilyMembersModal.value = false
        bookForOtherClass.value = null
        otherFamilyMemberBookingIds.value = null
    } else {
        confirmBookingOtherFamilyMemberBooking(bookForOtherClass.value, otherFamilyMemberBookingIds.value)
    }
}

const confirmBookingOtherFamilyMemberBooking = (classDetail, members) => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    bookingForm.transform((data) => ({
        class_id: classDetail.id,
        members: members,
        is_new_family_booking: true,
    })).post(
        route("ss.member.bookings.other-famly", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            onSuccess: (res) => {
                closeBookForOtherFamilyMembersModal(false);
                if (res.props.flash.type === "success") {
                    modal.value = false
                }
            },
        }
    );
}
const closeAddFamilyModal = (param) => {
    if (param === false) {
        selectedFamilyMembers.value.family_member.ids = []
        is_family_booking.value = 1
    } else {
        if (!selectedFamilyMembers.value.family_member.ids.length) {
            selectedFamilyMembers.value.family_member.ids = []
            is_family_booking.value = 1
        }
    }
    showFamilyBookingModal.value = false
};
const idsCancellation = ref(null)
const addRemoveMemberForCancellation = (isParent, id) => {
    if (idsCancellation.value == null) {
        idsCancellation.value = []
    }
    if (isParent) {
        const filtered = idsCancellation.value.filter((item) => {
            return item.is_parent && item.id == id
        })
        if (filtered.length) {
            const filtered = idsCancellation.value.filter((item) => {
                return item.is_parent && (item.id != id)
            })
            idsCancellation.value = filtered
        } else {
            idsCancellation.value.push({
                id: id,
                is_parent: true
            })
        }
    } else {
        const filtered = idsCancellation.value.filter((item) => {
            return !item.is_parent && (item.id == id)
        })
        if (filtered.length) {
            const filtered = idsCancellation.value.filter((item) => {
                return !item.is_parent && (item.id != id)
            })
            idsCancellation.value = filtered
        } else {
            idsCancellation.value.push({
                id: id,
                is_parent: false
            })
        }
    }
    if (!idsCancellation.value.length) {
        idsCancellation.value = null
    }
}
const addRemoveFamilyMemberForOtherBookings = (isParent, id) => {
    if (otherFamilyMemberBookingIds.value == null) {
        otherFamilyMemberBookingIds.value = []
    }
    if (isParent) {
        const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
            return item.is_parent && item.id == id
        })
        if (filtered.length) {
            const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
                return item.is_parent && (item.id != id)
            })
            otherFamilyMemberBookingIds.value = filtered
        } else {
            otherFamilyMemberBookingIds.value.push({
                id: id,
                is_parent: true
            })
        }
    } else {
        const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
            return !item.is_parent && (item.id == id)
        })
        if (filtered.length) {
            const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
                return !item.is_parent && (item.id != id)
            })
            otherFamilyMemberBookingIds.value = filtered
        } else {
            otherFamilyMemberBookingIds.value.push({
                id: id,
                is_parent: false
            })
        }
    }
    if (!otherFamilyMemberBookingIds.value.length) {
        otherFamilyMemberBookingIds.value = null
    }
}
const isBookable = (time) => {
    time = DateTime.fromISO(time, { zone: props.business_settings.timezone });
    const interval = time.diff(DateTime.now().setZone(props.business_settings.timezone), 'minutes');
    return interval.minutes > 0;
};

</script>

<template>
    <Section bg="bg-transparent" class="flex flex-col">
        <!-- <div class="text-xl mb-4">
            Active classes list
        </div> -->
        <div class="flex flex-wrap gap-4 flex-1 mb-4">
            <div class="w-full md:flex-1">
                <InputLabel value="Class Type" for="class_type" />
                <Multiselect v-model="form.class_type" :options="class_types" :searchable="true" :close-on-select="true"
                    :show-labels="true" placeholder="Select Class Type" mode="tags" :style="{ height: 'auto', padding: 0 }">
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <ColoredValue color="#ddd" :title="value.label" />
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <ColoredValue color="#ddd" :title="option.label" />
                    </template>
                </Multiselect>
            </div>
            <div class="w-full md:flex-1">
                <InputLabel value="Instructor" for="instructor" />
                <Multiselect v-model="form.instructor" :options="instructors" :searchable="true" :close-on-select="true"
                    :show-labels="true" placeholder="Select Instructor" mode="tags" :style="{ height: 'auto', padding: 0 }">
                    <template v-slot:singlelabel="{ value }">
                        <div class="multiselect-single-label flex items-center">
                            <Avatar size="small" :title="value.label" />
                            <span class="ml-2">{{ value.label }}</span>
                        </div>
                    </template>

                    <template v-slot:option="{ option }">
                        <Avatar size="small" :title="option.label" />
                        <span class="ml-2">{{ option.label }}</span>
                    </template>
                </Multiselect>
            </div>
            <div class="w-full md:flex-1">
                <InputLabel value="Peak/Off Peak" for="is_off_peak" />
                <Multiselect id="is_off_peak" v-model="form.is_off_peak" :options="[
                    { value: '0', label: 'Peak' },
                    { value: '1', label: 'Off Peak' },
                ]" :searchable="true" class="mt-1" placeholder="Select Peak/Off Peak"
                    :style="{ height: 'auto', padding: 0 }" />
            </div>
            <ButtonLink class="self-end" styling="primary" size="default" @click="(e) => {
                form.class_type = [];
                form.instructor = [];
                form.is_off_peak = '';
            }
                ">Reset</ButtonLink>
        </div>
        <div class="border border-t border-gray-200 mb-4"></div>
        <div class="flex mb-4">
            <div class="flex flex-grow justify-start">
                <ArrowLeft class="cursor-pointer" @click="handlePrev" />
            </div>
            <div class="flex flex-grow justify-end">
                <ArrowRight class="cursor-pointer" @click="handleNext" />
            </div>
        </div>
        <Splide class="classes-timetable" :options="{
            perPage: calendarPerPage,
            perMove: 1,
            arrows: false,
            pagination: false,
            gap: '1rem',
            isNavigation: true,
        }" ref="timetableEl" @splide:moved="handleMoved">
            <SplideSlide v-for="(time, index) in timetable" :key="index"
                class="inline-flex py-2 px-3 rounded-lg justify-center bg-white" :class="{
                    'bg-yellow-500':
                        (!form.date && index == 0) ||
                        time.toSQLDate() == form.date,
                    // 'mr-3': index < timetable.length - 1,
                }">
                <div class="flex flex-col" :class="{
                    'text-white':
                        (!form.date && index == 0) ||
                        time.toSQLDate() == form.date,
                    'text-black': time.toSQLDate() != form.date,
                }">
                    <div class="flex flex-row md:flex-col justify-center">
                        <div class="text-xs text-center">
                            {{ time.toFormat("MMM") }} {{ time.toFormat("d") }}
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-center">
                        {{ time.toFormat("EEE") }}
                    </div>
                </div>
            </SplideSlide>
        </Splide>

        <Splide class="mt-3" :options="{
            perPage: classesPerPage,
            perMove: 1,
            arrows: false,
            pagination: false,
            gap: '1rem',
        }" ref="classesEl">
            <SplideSlide v-for="(time, tIndex) in timetable" :key="tIndex">
                <div v-if="!classes[time.toSQLDate()]" class="flex flex-col">
                    &nbsp;
                </div>
                <div v-else v-for="(item, index1) in classes[time.toSQLDate()]" :key="index1"
                    class="cursor-pointer relative rounded-md p-3 mb-3" @click="showModal(item)" :class="{
                        hidden:
                            form.is_off_peak &&
                            item.is_off_peak != form.is_off_peak,
                        'border border-blue-500 bg-gray-200':
                            classDetails.id == item.id && !item.on_waitlist && !item.is_booked,
                        'opacity-30': !isBookable(item.end_date),
                        'border border-black hover:border-blue-500 bg-white':
                            !item.on_waitlist && !item.is_booked && classDetails.id != item.id,
                        'shadow-md': item.is_booked || item.on_waitlist,
                    }" :style="{
    backgroundColor: item.is_booked ? 'rgba(41, 181, 128, 0.5)'
        : (item.on_waitlist ? 'rgba(232, 168, 56, 0.5)' : ''),
}">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-2xl font-bold self-center" v-tooltip="DateTime.fromISO(item.start_date)
                            .setZone(business_settings.timezone)
                            .toFormat(
                                business_settings.date_format
                                    .format_js +
                                ' ' +
                                business_settings.time_format
                                    .format_js
                            )
                            ">
                            {{
                                DateTime.fromISO(item.start_date)
                                    .setZone(business_settings.timezone)
                                    .toFormat(
                                        business_settings.time_format
                                            .format_js
                                    )
                            }}
                        </div>
                        <div class="flex flex-row rounded-lg text-green-800 px-2 py-1 items-center justify-center"
                            style="background: rgba(66, 112, 95, 0.2)">
                            <div class="mr-1">
                                <ClockIcon />
                            </div>
                            <div class="text-sm font-semibold mt-[1px]">
                                {{ item.duration }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 pt-4 border-t border-gray-300 items-center">

                        <VTooltip v-if="item.title.length > 20" :triggers="['hover', 'click']">
                            <div class="font-bold truncate">{{item.title}}</div>

                            <template #popper>
                                <div class="w-40">{{item.title}}</div>
                            </template>
                        </VTooltip>
                        <div v-else class="font-bold truncate">{{item.title}}</div>

                        <div class="uppercase font-semibold">
                            {{ item.class_type?.title }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <template v-if="item?.instructor?.length">
                            <template v-for="(
                                    instructor, ins
                                ) in item?.instructor" :key="ins">
                                <AvatarValue
                                    class="cursor-pointer mb-2"
                                    :title="instructor?.name ?? 'Demo Ins'"
                                    :useIcon="true"
                                />
                            </template>
                        </template>
                    </div>
                    <div v-if="!item.is_booked && !item.on_waitlist" class="border-t border-gray-300"></div>
                    <div class="flex flex-wrap my-4 items-center">
                        <div class="mr-2">
                            <LocationIcon />
                        </div>
                        <div class="text-base text-green-700 uppercase">
                            <span class="mt-[1px]">{{ item.studio?.location?.title }}</span>
                        </div>
                    </div>

                    <div v-if="item.is_free" class="bg-primary-300 text-white rounded-lg px-2 py-1 font-bold text-center">FREE CLASS</div>

                    <template v-if="item.is_booked">
                        <div class="border-t border-gray-300"></div>
                        <div class="flex mt-4 items-center justify-center text-xl text-white font-bold uppercase">
                            Booked
                        </div>
                    </template>
                    <template v-else-if="item.on_waitlist">
                        <div class="border-t border-gray-300"></div>
                        <div class="flex mt-4 items-center justify-center text-xl text-white font-bold uppercase">
                            On Waitlist
                        </div>
                    </template>
                </div>
            </SplideSlide>
        </Splide>
    </Section>
    <!-- <div class="container mx-auto">
    </div> -->

    <!-- Class detail Modal -->
    <ClassDetail :business_settings="business_settings" :class_types="class_types" :instructors="instructors" :show="modal"
        :selected_family_members="selectedFamilyMembers" :other_family_member_booking_ids="otherFamilyMemberBookingIds"
        :is_cancel_able_booking="isCancelAbleBooking(classDetails)" :classDetails='classDetails' :user="user"
        :is_family_booking="is_family_booking" :bookingForm="bookingForm" @handleBooking="handleBooking"
        @addRemoveFromWaitlist="addRemoveFromWaitlist" @bookForOtherFamilyMembers="bookForOtherFamilyMembers"
        @isFamilyBooking="isFamilyBooking" @close="closeModal" @cancelBooking="cancelBooking" />

    <!-- Family Booking Modal -->
    <FamilyBooking :selected_family_members="selectedFamilyMembers" :show_family_booking_modal="showFamilyBookingModal"
        :side_modal_opened="modal" :user="user" @closeAddFamilyModal="closeAddFamilyModal"
        @addRemoveFamilyMember="addRemoveFamilyMember" />

    <!-- Other Family Member Booking Modal (which are not booked at first time booking) -->
    <OtherFamilyMembersBooking :selected_family_members="selectedFamilyMembers"
        :show_book_for_other_family_members_modal="showBookForOtherFamilyMembersModal" :side_modal_opened="modal"
        :user="user" :current_class="classDetails" :other_family_member_booking_ids="otherFamilyMemberBookingIds"
        :form="bookingForm" @closeBookForOtherFamilyMembersModal="closeBookForOtherFamilyMembersModal"
        @addRemoveFamilyMemberForOtherBookings="addRemoveFamilyMemberForOtherBookings" />

    <!-- Booking Cancellation Modal to choose from, When All members are booked -->
    <CancelBooking :show_cancel_booking_modal="showCancelBookingModal" :side_modal_opened="modal" :user="user"
        :current_class="classDetails" :ids_cancellation="idsCancellation" :booking_form="bookingForm"
        @hideCancelBookingModal="showCancelBookingModal = false" @cancelBookingAll="cancelBookingAll"
        @addRemoveMemberForCancellation="addRemoveMemberForCancellation" />

    <!-- Waitlist Modal to choose from, When All members are booked -->
    <WaitlistModal v-if="showWaitlistModal" :class-detail="classDetails" :modal="modal"
        :add-remove-users-to-waitlist="addRemoveUsersToWaitlist"
        @hide-both=";[showWaitlistModal = false, modal = false, addRemoveUsersToWaitlist = 0]"
        :business-settings="business_settings" @hide=";[showWaitlistModal = false, addRemoveUsersToWaitlist = 0]"
        @disable-button=";[disableButton = true]" @enable-button=";[disableButton = false]" />


        <WaiverListing
        :business_settings="business_settings"
        :show="showWaiverModal"
        :waivers="props.waivers"
        :user="user"
        :user_waiver_ids="user_waiver_ids"
        :signed_waiver_ids="signed_waiver_ids"
        @close="showWaiverModal = false"
    />
</template>
