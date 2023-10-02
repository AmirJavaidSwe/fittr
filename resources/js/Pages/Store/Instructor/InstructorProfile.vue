<script setup>
import { useWindowSize } from "@/Composables/window_size";
import { computed, onBeforeMount, onMounted, ref, reactive } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { DateTime, Duration, Interval } from "luxon";
import Section from "@/Components/Section.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import "@vueform/multiselect/themes/tailwind.css";
import ClockIcon from "@/Icons/ClockIcon.vue";
import LocationIcon from "@/Icons/LocationIcon.vue";
import Avatar from "@/Components/Avatar.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import { Splide, SplideSlide } from "@splidejs/vue-splide";
import ArrowLeft from "@/Components/ArrowLeft.vue";
import ArrowRight from "@/Components/ArrowRight.vue";
import Classes from "./..//Classes/Index.vue";
const subdomain = ref(usePage().props.business_settings.subdomain);
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
    classes: {
        type: Object,
        required: true,
    },
    business_settings: {
        Object,
        required: true,
    },
    locations: {
        type: Array,
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
const filters = reactive({
    locations: [],
    class_types: [],
    instructors: [],
    off_peak: null,
});
const clearFilters = () => {
    filters.locations = [];
    filters.class_types = [];
    filters.instructors = [];
    filters.is_off_peak = null;
};
const classes_filtered = computed(() => {
    const classes = { ...props.classes };
    for (const date_key in classes) {
        if (filters.class_types.length) {
            classes[date_key] = classes[date_key].filter((c) =>
                filters.class_types.includes(c.class_type_id)
            );
        }
        if (filters.instructors.length) {
            classes[date_key] = classes[date_key].filter((c) => {
                let class_instructors = c.instructors.map((i) => i.id);
                //keep class if at least one of class instructors array match filter ids array
                for (var i = 0; i < class_instructors.length; i++) {
                    if (filters.instructors.includes(class_instructors[i])) {
                        return true;
                    }
                }
                return false;
            });
        }
        if (filters.is_off_peak === true) {
            classes[date_key] = classes[date_key].filter(
                (c) => filters.is_off_peak === c.is_off_peak
            );
        }
        if (filters.locations.length) {
            classes[date_key] = classes[date_key].filter((c) =>
                filters.locations.includes(c.studio.location_id)
            );
        }
    }
    return classes;
});

const timetableEl = ref(null);
const classesEl = ref(null);
const timetable = ref([]);
const user = ref(usePage().props.user);

onBeforeMount(() => {
    let start = DateTime.now().setZone(props.business_settings.timezone);
    let end = DateTime.now().setZone(props.business_settings.timezone);
    let noOfDays = parseInt(
        props.business_settings.days_max_timetable ?? start.daysInMonth
    ); // Note: need to discuss the approach about showing unlimited dates here.
    end = end.plus(Duration.fromObject({ days: noOfDays }));

    let intervalObj = Interval.fromDateTimes(start, end);
    timetable.value = intervalObj.splitBy({ days: 1 }).map((d) => d.start);
});

onMounted(() => {
    const timetableSplide = timetableEl.value?.splide;
    const classesSplide = classesEl.value?.splide;

    if (timetableSplide && classesSplide) {
        timetableEl.value?.sync(classesEl.value?.splide);
    }
});

const modal = ref(false);
const classDetails = ref({});

const showModal = (data) => {
    modal.value = true;
    data.start_date = DateTime.fromISO(data.start_date, {
        zone: props.business_settings?.timezone,
    });
    data.end_date = DateTime.fromISO(data.end_date, {
        zone: props.business_settings?.timezone,
    });
    classDetails.value = { ...data };
    let user_id = usePage().props?.user?.id;

    classDetails.value.has_family = user.value?.family?.length ? true : false;
    classDetails.value.is_waiting =
        typeof classDetails.value.waitlists?.filter(
            (item) => item.user_id === user_id
        )[0] !== "undefined";
};

const resetFamilyBookingDetails = () => {
    selectedFamilyMembers.value.user_id = user?.value?.id;
    selectedFamilyMembers.value.family_member.ids = [];
};

const closeModal = () => {
    modal.value = false;
    classDetails.value = {};
    resetFamilyBookingDetails();
};

const bookingForm = useForm({ class_id: "" });

const showWaiverModal = ref(false);
const signed_waiver_ids = computed(() => props.signed_waiver_ids);
const user_waiver_ids = computed(() => props.user_waiver_ids);

const handleBooking = () => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    bookingForm.class_id = classDetails.value.id;
    bookingForm
        .transform((data) => ({
            ...data,
            familyBooking: selectedFamilyMembers.value,
            is_family_booking: is_family_booking.value == 2 ? true : false,
        }))
        .post(
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

const showCancelBookingModal = ref(false);
const cancelBookingAll = () => {
    bookingForm
        .transform((data) => ({
            class_id: classDetails.value.id,
            ids_cancellation: idsCancellation.value,
        }))
        .post(
            route("ss.member.bookings.cancel-all", {
                subdomain: props.business_settings.subdomain,
            }),
            {
                onSuccess: (res) => {
                    if (res.props.flash.type === "success") {
                        idsCancellation.value = null;
                        bookForOtherClass.value = null;
                        showCancelBookingModal.value = false;
                        modal.value = false;
                    }
                },
            }
        );
};

const cancelBooking = () => {
    let filtered;
    if (classDetails.value.user_bookings.length) {
        filtered = classDetails.value.user_bookings.filter(
            (item) =>
                item.user_id == user.value.id &&
                item.is_family_booking == 1 &&
                item.family_member_id != null
        );
    }

    if (filtered.length) {
        idsCancellation.value = null;
        bookForOtherClass.value = classDetails.value;
        showCancelBookingModal.value = true;
        return;
    }
    bookingForm.class_id = classDetails.value.id;

    bookingForm
        .transform((data) => ({
            ...data,
            id: classDetails.value.user_bookings[0].id,
        }))
        .post(
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

const showWaitlistModal = ref(false);
const disableButton = ref(false);
const addRemoveUsersToWaitlist = ref(0);

// param values
// 1, just add current user to waitlist
// 2, for current user and family  add / remove to waitlist
// 3, remove current user from waitlist

const addRemoveFromWaitlist = (param) => {
    showWaitlistModal.value = true;
    addRemoveUsersToWaitlist.value = param;
};

// splidejs
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

const activeDateIndex = ref();
const handleMoved = (splide, index, prevIndex) => {
    activeDateIndex.value = index;
};

const handlePrev = () => {
    classesEl.value.go("<");
};

const handleNext = () => {
    classesEl.value.go(">");
};

const showFamilyBookingModal = ref(false);
const is_family_booking = ref(1);

const isFamilyBooking = ($event) => {
    if ($event.is_family_booking == 2) {
        is_family_booking.value = 2;
        showFamilyBookingModal.value = true;
    } else {
        is_family_booking.value = 1;
        showFamilyBookingModal.value = false;
        selectedFamilyMembers.value.user_id = user?.value?.id;
        selectedFamilyMembers.value.family_member.ids = [];
    }
};

const selectedFamilyMembers = ref({
    user_id: user?.value?.id,
    family_member: {
        ids: [],
    },
});

const addRemoveFamilyMember = (isParent, id) => {
    if (isParent) {
        if (selectedFamilyMembers.value.user_id !== null) {
            selectedFamilyMembers.value.user_id = null;
        } else {
            selectedFamilyMembers.value.user_id = user.value.id;
        }
    } else if (!isParent) {
        const filtered = selectedFamilyMembers.value.family_member.ids.filter(
            (item) => item == id
        );
        if (filtered.length) {
            const filtered =
                selectedFamilyMembers.value.family_member.ids.filter(
                    (item) => item != id
                );
            selectedFamilyMembers.value.family_member.ids = filtered;
        } else {
            selectedFamilyMembers.value.family_member.ids.push(id);
        }
    }
};

const isCancelAbleBooking = (classDetails) => {
    if (
        classDetails.is_booked &&
        user?.value?.id &&
        /* !classDetails.spaces_left || */ classDetails.user_bookings.length ==
            user.value?.family?.length + 1
    ) {
        return true;
    }
    return false;
};

const showBookForOtherFamilyMembersModal = ref(false);
const bookForOtherClass = ref(null);
const otherFamilyMemberBookingIds = ref(null);
const bookForOtherFamilyMembers = (classDetails) => {
    showBookForOtherFamilyMembersModal.value = true;
    bookForOtherClass.value = classDetails;
};
const closeBookForOtherFamilyMembersModal = (param) => {
    if (!param) {
        showBookForOtherFamilyMembersModal.value = false;
        bookForOtherClass.value = null;
        otherFamilyMemberBookingIds.value = null;
    } else {
        confirmBookingOtherFamilyMemberBooking(
            bookForOtherClass.value,
            otherFamilyMemberBookingIds.value
        );
    }
};

const confirmBookingOtherFamilyMemberBooking = (classDetail, members) => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    bookingForm
        .transform((data) => ({
            class_id: classDetail.id,
            members: members,
            is_new_family_booking: true,
        }))
        .post(
            route("ss.member.bookings.other-famly", {
                subdomain: props.business_settings.subdomain,
            }),
            {
                onSuccess: (res) => {
                    closeBookForOtherFamilyMembersModal(false);
                    if (res.props.flash.type === "success") {
                        modal.value = false;
                    }
                },
            }
        );
};
const closeAddFamilyModal = (param) => {
    if (param === false) {
        selectedFamilyMembers.value.family_member.ids = [];
        is_family_booking.value = 1;
    } else {
        if (!selectedFamilyMembers.value.family_member.ids.length) {
            selectedFamilyMembers.value.family_member.ids = [];
            is_family_booking.value = 1;
        }
    }
    showFamilyBookingModal.value = false;
};
const idsCancellation = ref(null);
const addRemoveMemberForCancellation = (isParent, id) => {
    if (idsCancellation.value == null) {
        idsCancellation.value = [];
    }
    if (isParent) {
        const filtered = idsCancellation.value.filter((item) => {
            return item.is_parent && item.id == id;
        });
        if (filtered.length) {
            const filtered = idsCancellation.value.filter((item) => {
                return item.is_parent && item.id != id;
            });
            idsCancellation.value = filtered;
        } else {
            idsCancellation.value.push({
                id: id,
                is_parent: true,
            });
        }
    } else {
        const filtered = idsCancellation.value.filter((item) => {
            return !item.is_parent && item.id == id;
        });
        if (filtered.length) {
            const filtered = idsCancellation.value.filter((item) => {
                return !item.is_parent && item.id != id;
            });
            idsCancellation.value = filtered;
        } else {
            idsCancellation.value.push({
                id: id,
                is_parent: false,
            });
        }
    }
    if (!idsCancellation.value.length) {
        idsCancellation.value = null;
    }
};
const addRemoveFamilyMemberForOtherBookings = (isParent, id) => {
    if (otherFamilyMemberBookingIds.value == null) {
        otherFamilyMemberBookingIds.value = [];
    }
    if (isParent) {
        const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
            return item.is_parent && item.id == id;
        });
        if (filtered.length) {
            const filtered = otherFamilyMemberBookingIds.value.filter(
                (item) => {
                    return item.is_parent && item.id != id;
                }
            );
            otherFamilyMemberBookingIds.value = filtered;
        } else {
            otherFamilyMemberBookingIds.value.push({
                id: id,
                is_parent: true,
            });
        }
    } else {
        const filtered = otherFamilyMemberBookingIds.value.filter((item) => {
            return !item.is_parent && item.id == id;
        });
        if (filtered.length) {
            const filtered = otherFamilyMemberBookingIds.value.filter(
                (item) => {
                    return !item.is_parent && item.id != id;
                }
            );
            otherFamilyMemberBookingIds.value = filtered;
        } else {
            otherFamilyMemberBookingIds.value.push({
                id: id,
                is_parent: false,
            });
        }
    }
    if (!otherFamilyMemberBookingIds.value.length) {
        otherFamilyMemberBookingIds.value = null;
    }
};
const isBookable = (time) => {
    time = DateTime.fromISO(time, { zone: props.business_settings.timezone });
    const interval = time.diff(
        DateTime.now().setZone(props.business_settings.timezone),
        "minutes"
    );
    return interval.minutes > 0;
};
</script>
<template>
    <Section bg="bg-transparent mx-40">
        <div class="container mx-auto mt-8 p-4 gap-0 flex-grow-1">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Instructors</h1>
                <p class="text-lg font-semibold">Getting you powered up</p>
            </div>
            <div class="flex flex-wrap justify-center my-8">
                <div class="w-96">
                    <img
                        :src="instructor?.profile?.images[0]?.url"
                        alt="Instructor's Image"
                        class="w-full h-full object-fit"
                    />
                </div>
                <div
                    class="xl:w-[32rem] w-96 x-8 my-8 px-2 sm:mx-8 lg:my-0 overflow-y-auto"
                >
                    <div class="text-2xl font-semibold mb-2">
                        {{ instructor.full_name }}
                    </div>
                    <div class="text-sm font-semibold mb-2">
                        <span
                            v-if="
                                instructor.class_types.join(' / ').length > 20
                            "
                            v-tooltip="instructor.class_types.join(' / ')"
                        >
                            {{
                                instructor.class_types
                                    .join(" / ")
                                    .substring(0, 20)
                            }}...
                        </span>
                        <span v-else>
                            {{ instructor.class_types.join(" / ") }}
                        </span>
                    </div>
                    <div class="text-gray-600 h-48 max-h-full">
                        {{ instructor?.profile?.description }}
                    </div>
                </div>
            </div>

            <div class="my-12">
                <p class="text-2xl font-semibold mb-8">
                    Next sessions with {{ instructor.full_name }}
                </p>
            </div>
            <div class="my-12">
                <Classes
                    :business_settings="props.business_settings"
                    :classes="props.classes"
                    :locations="props.locations"
                    :class_types="props.class_types"
                    :instructors="props.instructors"
                    :waivers="props.waivers"
                    :user_waiver_ids="props.user_waiver_ids"
                    :signed_waiver_ids="props.signed_waiver_ids"
                />
            </div>
        </div>
    </Section>
</template>
