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
import Modal from "@/Components/Modal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import CardBasic from "@/Components/CardBasic.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

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
    classDetails.value.is_waiting = typeof classDetails.value.waitlists?.filter(item => item.user_id === user_id)[0] !== 'undefined';
}

const closeModal = () => {
    modal.value = false;
    classDetails.value = {};
};

const bookingForm = useForm({ class_id: "" });

const handleBooking = () => {
    bookingForm.class_id = classDetails.value.id;

    bookingForm.transform((data) => ({
        ...data,
        familyBooking: selectedFamilyMembers.value,
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

const cancelBooking = () => {
    bookingForm.class_id = classDetails.value.id;

    bookingForm.post(
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

const waitlistForm = useForm({ class_id: '' });

const addToWaitList = () => {
    waitlistForm.class_id = classDetails.value.id;

    const swal = useSwal();

    swal.messageBox({
        title: 'This class is full',
        text: 'You can still book to waitlist. We will inform you if space becomes available.',
    }).then((result) => {

        waitlistForm.post(route('ss.member.bookings.add-to-waitlist', { subdomain: props.business_settings.subdomain }), {
            onSuccess: (res) => {
                if (res.props.flash.type === 'success') {
                    let classData = res.props.classes[classDetails.value.start_date.toSQLDate()];
                    classData = classData?.length ? classData.filter(item => item.id === classDetails.value.id)[0] : undefined;
                    showModal(classData);
                }
            }
        });
    });

};

const removeFromWaitList = () => {
    waitlistForm.class_id = classDetails.value.id;

    waitlistForm.post(route('ss.member.bookings.remove-from-waitlist', { subdomain: props.business_settings.subdomain }), {
        onSuccess: (res) => {
            if (res.props.flash.type === 'success') {
                let classData = res.props.classes[classDetails.value.start_date.toSQLDate()];
                classData = classData?.length ? classData.filter(item => item.id === classDetails.value.id)[0] : undefined;
                showModal(classData);
            }
        }
    });
};

const { screen } = useWindowSize();

const calendarPerPage = computed(() => {
    switch (true) {
        case screen.value == "2xl":
            return 7;
        case screen.value == "xl":
            return 6;
        case screen.value == "lg":
            return 5;
        case screen.value == "md":
            return 4;
        case screen.value == "sm":
            return 2;
        default:
            return 1;
    }
});

const classesPerPage = computed(() => {
    switch (true) {
        case screen.value == "2xl":
            return 7;
        case screen.value == "xl":
            return 6;
        case screen.value == "lg":
            return 5;
        case screen.value == "md":
            return 4;
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

const showFamilyBookingModal = ref(false);
const is_family_booking = ref(1);

const isFamilyBooking = () => {
    if (is_family_booking.value == 2) {
        showFamilyBookingModal.value = true;
    } else {
        showFamilyBookingModal.value = false;
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

const checkSelectedFamilyMembers = (id) => {
    const filtered = selectedFamilyMembers.value.family_member.ids.filter((item) => item == id)
    return filtered.length ? true : false
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
}
</script>

<template>
    <div class="container mx-auto">
        <Section bg="bg-transparent" class="flex flex-col">
            <!-- <div class="text-xl mb-4">
                Active classes list
            </div> -->
            <div class="flex flex-wrap gap-4 mb-3">
                <div class="w-full md:flex-1">
                    <InputLabel value="Class Type" for="class_type" />
                    <Multiselect v-model="form.class_type" :options="class_types" :searchable="true" :close-on-select="true"
                        :show-labels="true" placeholder="Select Class Type" mode="tags"
                        :style="{ height: 'auto', padding: 0 }">
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
                        :show-labels="true" placeholder="Select Instructor" mode="tags"
                        :style="{ height: 'auto', padding: 0 }">
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
            <Splide class="classes-timetable" :options="{
                perPage: calendarPerPage,
                perMove: 1,
                arrows: false,
                pagination: false,
                gap: '1rem',
                isNavigation: true,
            }" ref="timetableEl" @splide:moved="handleMoved">
                <SplideSlide v-for="(time, index) in timetable"
                    class="inline-flex shrink-0 py-2 px-3 rounded-lg justify-center bg-white" :class="{
                        'bg-yellow-500':
                            (!form.date && index == 0) ||
                            time.toSQLDate() == form.date,
                        'mr-3': index < timetable.length - 1,
                    }">
                    <div class="flex flex-col md:flex-row" :class="{
                        'text-white':
                            (!form.date && index == 0) ||
                            time.toSQLDate() == form.date,
                        'text-black': time.toSQLDate() != form.date,
                    }">
                        <div class="text-2xl font-bold mr-2 self-center">
                            {{ time.toFormat("d") }}
                        </div>
                        <div class="flex flex-row md:flex-col justify-center">
                            <div class="text-xs">
                                {{ time.toFormat("MMM") }}
                            </div>
                            <div class="text-xs md:hidden mx-1">/</div>
                            <div class="text-xs">
                                {{ time.toFormat("EEE") }}
                            </div>
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
                <SplideSlide v-for="(time, index) in timetable" :key="index">
                    <div v-if="!classes[time.toSQLDate()]" class="flex flex-col">
                        &nbsp;
                    </div>
                    <div v-else v-for="(item, index) in classes[time.toSQLDate()]"
                        class="cursor-pointer bg-white relative rounded-md p-3 mb-3" @click="showModal(item)" :class="{
                            hidden:
                                form.is_off_peak &&
                                item.is_off_peak != form.is_off_peak,
                            'bg-yellow-300':
                                classDetails.id == item.id || item.is_booked,
                        }">
                        <div v-if="item.is_booked"
                            class="absolute bg-secondary-300/80 flex inset-0 items-center justify-center text-4xl">
                            Booked
                        </div>
                        <div class="flex justify-between mb-4">
                            <div class="text-xl font-medium self-center" v-tooltip="DateTime.fromISO(item.start_date)
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
                            <div class="flex flex-row rounded-lg text-green-800 px-2 py-1 h-8 items-center"
                                style="background: rgba(66, 112, 95, 0.2)">
                                <div class="mr-1">
                                    <ClockIcon />
                                </div>
                                <div class="text-base font-semibold">
                                    {{ item.duration }}
                                </div>
                            </div>
                        </div>
                        <div class="flex mb-4 pt-4 border-t-2 border-gray-300 items-center">
                            <div class="flex mr-2 w-7 h-7 justify-center items-center">
                                <span class="inline-flex rounded-xl w-3 h-3 bg-red-500"></span>
                            </div>
                            <div class="flex-grow uppercase font-medium">
                                {{ item.class_type?.title }}
                            </div>
                        </div>
                        <div class="flex mb-4 items-center">
                            <div class="flex mr-2 w-7 h-7">
                                <template v-if="item?.instructor.length">
                                    <template v-for="(
                                            instructor, ins
                                        ) in item?.instructor" :key="ins">
                                        <AvatarValue
                                            class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                                            :onlyTooltip="true" :title="instructor?.name ?? 'Demo Ins'
                                                " />
                                    </template>
                                </template>
                            </div>
                        </div>
                        <div class="flex justify-between pt-4 border-t-2 border-gray-300">
                            <div class="flex mr-2">
                                <LocationIcon />
                            </div>
                            <div class="flex-grow font-medium text-green-700">
                                {{ item.studio?.location?.title }}
                            </div>
                        </div>
                    </div>
                </SplideSlide>
            </Splide>
        </Section>
    </div>

    <!-- Class detail Modal -->
    <SideModal :show="modal" @close="closeModal">
        <template #title>Details</template>
        <template #content>
            <div class="w-full mb-4">
                <img class="w-full rounded-md" v-if="classDetails.studio?.location?.images?.length"
                    :src="classDetails.studio?.location?.images[0].url" :alt="classDetails.studio?.location?.images[0]
                            .original_filename
                        " />
            </div>
            <div class="flex flex-col">
                <div class="flex text-3xl font-bold mb-4 items-center">
                    <div class="flex flex-grow mr-4">{{ classDetails.title }}</div>
                    <div class="flex flex-col shrink-0">
                        <div
                            class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2 justify-center mb-2">
                            Free</div>
                        <div class="inline-flex text-sm font-normal rounded-lg text-white p-2 justify-center"
                            :class="{ 'bg-danger-600': !classDetails.spaces_left, 'bg-gray-500': classDetails.spaces_left }">
                            {{ classDetails.spaces_left > 0 ? 'Not Full' : 'Full' }}</div>
                    </div>
                </div>

                <div class="flex flex-row mb-4">
                    <div class="flex flex-row">
                        <div class="flex mr-2">
                            <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                            <template v-if="classDetails?.instructor.length">
                                <template v-for="(
                                        instructor, ins
                                    ) in classDetails?.instructor" :key="ins">
                                    <AvatarValue
                                        class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                                        :onlyTooltip="true" :title="instructor?.name ?? 'Demo Ins'" />
                                </template>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Class Type:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.class_type?.title }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Time:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat("hh:mm a") }} -
                        {{ classDetails.end_date?.toFormat("hh:mm a") }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Lession Time:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.duration }} minutes
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">Date:</div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat(business_settings.date_format?.format_js) }}
                    </div>
                </div>

                <div v-if="classDetails.waitlists?.length" class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Waiting list:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.waitlists?.length }}
                    </div>
                </div>
            </div>
            <div class="text-right" v-if="user?.family?.length">
                <div class="relative inline-flex text-right items-center">
                    Booking for
                    <select class="ml-5 border-0 focus:border-0 cursor-pointer mr-0 pr-7 text-primary-500"
                        v-model="is_family_booking" @change="isFamilyBooking">
                        <option value="1">Only Myself</option>
                        <option value="2">Me and Others</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 bg-mainbg border-rounded text-right border-gray p-4" v-if="user?.family?.length">
                <template v-if="is_family_booking == 2 && selectedFamilyMembers.family_member.ids.length">
                    {{ parseInt(selectedFamilyMembers.family_member.ids.length) + 1 + " x Attendee(s) Selected" }}
                </template>
                <template v-else>
                    {{ 1 + " x Attendee(s) Selected" }}
                </template>
            </div>
        </template>
        <template #footer>
            <ButtonLink class="mr-2" styling="default" size="default" @click="closeModal">Close</ButtonLink>

            <ButtonLink v-if="!$page.props.user" styling="secondary" size="default" @click="handleBooking"
                :disabled="bookingForm.processing">Sign in to Book</ButtonLink>
            <ButtonLink v-else-if="classDetails.is_booked" styling="secondary" size="default" @click="cancelBooking"
                :disabled="bookingForm.processing">Cancel Booking</ButtonLink>
            <div v-else-if="!classDetails.spaces_left" class="inline-flex">
                <ButtonLink v-if="classDetails.is_waiting" styling="secondary" size="default" @click="removeFromWaitList"
                    :disabled="waitlistForm.processing">Remove from waitlist</ButtonLink>
                <ButtonLink v-else styling="secondary" size="default" @click="addToWaitList"
                    :disabled="waitlistForm.processing">Add to waitlist</ButtonLink>
            </div>
            <ButtonLink v-else styling="secondary" size="default" @click="handleBooking" :disabled="bookingForm.processing">
                Book</ButtonLink>
        </template>
    </SideModal>

    <Modal :show="showFamilyBookingModal" :sideModalOpened="modal">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-md mx-auto">Select Attendees</div>
                    <div>
                        <CloseModal @click="closeAddFamilyModal(false)" v-tooltip="'Cancel and Close'" />
                    </div>
                </div>
            </template>
            <template #default>
                <div class="flex items-center justify-between my-4 mx-4">
                    <div class="flex items-center">
                        <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-10 w-10 object-cover" />
                        <div class="pl-2">
                            <div class="block pl-2 font-semibold mb-2">
                                {{ user.name }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex items-center justify-start mr-20">
                        <input type="checkbox" @change="addRemoveFamilyMember(true, user.id)"
                            :checked="selectedFamilyMembers.user_id !== null ? true : false">
                    </div>
                </div>
                <hr />
                <template v-for="(familyMember, index) in user.family">
                    <div class="flex items-center justify-between my-4 mx-4">
                        <div class="flex items-center">
                            <img :src="familyMember.profile_photo_url" :alt="familyMember.name"
                                class="rounded-full h-10 w-10 object-cover" />
                            <div class="pl-2">
                                <div class="block pl-2 font-semibold mb-2">
                                    {{ familyMember.name }}
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex items-center justify-start mr-20">
                            <input type="checkbox" @change="addRemoveFamilyMember(false, familyMember.id)"
                                :checked="checkSelectedFamilyMembers(familyMember.id)">
                        </div>
                    </div>
                    <hr />
                </template>
            </template>
            <template #footer>
                <div class="text-right">
                    <ButtonLink class="mr-2" styling="default" size="default" @click="closeAddFamilyModal(false)">Cancel &
                        Close</ButtonLink>
                    <ButtonLink styling="secondary" size="default" @click="closeAddFamilyModal(true)">Confirm & Close
                    </ButtonLink>
                </div>
            </template>

        </CardBasic>
    </Modal>
</template>
