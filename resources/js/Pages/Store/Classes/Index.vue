<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
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

const props = defineProps({
    classes: {
        type: Object,
        required: true,
    },
    business_seetings: {
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
    time: '',
    date: '',
});

const timetableEl = ref(null);
const classesEl = ref(null);
const timetable = ref([]);

const onDateChange = (date) => {
    form.date = date.toSQLDate();
    // onSearch();
}

const onSearch = () => {
    form.transform((data) => ({class_type: data.class_type, instructor: data.instructor}))
    .get(route('ss.classes.index', { subdomain: props.business_seetings.subdomain }), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: ['classes']
    });
}

onBeforeMount(() => {

    let queryParams = new URLSearchParams(window.location.search);

    if(queryParams.get('class_type'))
        form.class_type = queryParams.get('class_type');
    if(queryParams.get('instructor'))
        form.instructor = queryParams.get('instructor');
    if(queryParams.get('time'))
        form.time = queryParams.get('time');

    let start = DateTime.now().setZone(props.business_seetings.timezone);
    let end = DateTime.now().setZone(props.business_seetings.timezone);
    let noOfDays = parseInt(props.business_seetings.days_max_timetable ?? start.daysInMonth) - 1; // Note: need to discuss the approach about showing unlimited dates here.
    end = end.plus(Duration.fromObject({days: noOfDays}));

    let intervalObj = Interval.fromDateTimes(start, end);
    timetable.value = intervalObj.splitBy({ days: 1}).map(d => d.start);

    if(queryParams.get('date')) {
        form.date = queryParams.get('date');
    }
});

onMounted(() => {

    const timetableSplide = timetableEl.value?.splide;
    const classesSplide = classesEl.value?.splide;

    if(timetableSplide && classesSplide) {
        timetableEl.value?.sync(classesEl.value?.splide);
    }

});

watch(() => ({class_type: form.class_type, instructor: form.instructor}), onSearch);

const modal = ref(false);
const classDetails = ref({});

const showModal = (data) => {
    modal.value = true;
    data.start_date = DateTime.fromISO(data.start_date, {zone: props.business_seetings?.timezone});
    data.end_date = DateTime.fromISO(data.end_date, {zone: props.business_seetings?.timezone});
    classDetails.value = {...data};
}

const closeModal = () => {
    modal.value = false;
    classDetails.value = {};
}

const bookingForm = useForm({ class_id: ''});

const handleBooking = () => {
    bookingForm.class_id = classDetails.value.id;

    bookingForm.post(route('ss.member.bookings.store', {subdomain: props.business_seetings.subdomain}), {
        onSuccess: (res) => {
            if(res.props.flash.type === 'success') {
                closeModal();
            }
        }
    });
}

const cancelBooking = () => {
    bookingForm.class_id = classDetails.value.id;

    bookingForm.post(route('ss.member.bookings.cancel', {subdomain: props.business_seetings.subdomain}), {
        onSuccess: (res) => {
            if(res.props.flash.type === 'success') {
                closeModal();
            }
        }
    });
}

const { windowWidth, windowHeight, screen } = useWindowSize();

const calendarPerPage = computed(() => {
    switch(true) {
        case screen.value == '2xl':
            return 7;
        case screen.value == 'xl':
            return 6;
        case screen.value == 'lg':
            return 5;
        case screen.value == 'md':
            return 4;
        case screen.value == 'sm':
            return 5;
        default:
            return 4;
    };
});

const classesPerPage = computed(() => {
    switch(true) {
        case screen.value == '2xl':
            return 7;
        case screen.value == 'xl':
            return 6;
        case screen.value == 'lg':
            return 5;
        case screen.value == 'md':
            return 4;
        case screen.value == 'sm':
            return 2;
        default:
            return 1;
    };
});

const handleMoved = (splide, index, prevIndex) => {
    const date = timetable.value[index].toSQLDate();
    form.date = date;
}

</script>

<template>
    <div class="container mx-auto">
        <Section bg="bg-transparent" class="flex flex-col">
            <!-- <div class="text-xl mb-4">
                Active classes list
            </div> -->
            <div class="flex flex-col md:flex-row md:divide-x-2 md:divide-gray-300 md:border-b-2 md:border-gray-300 mb-3">
                <div class="flex flex-col md:w-1/3 md:pr-3 h-full mb-4">
                    <InputLabel value="Class Type" for="class_type" />
                    <Multiselect
                        v-model="form.class_type"
                        :options="class_types"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        placeholder="Select Class Type"
                        mode="tags"
                        :style="{height: (form.class_type.length ? 'auto' : '')}"
                    >
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
                <div class="flex flex-col md:w-1/3 md:px-3 h-full mb-4">
                    <InputLabel value="Instructor" for="instructor" />
                    <Multiselect
                        v-model="form.instructor"
                        :options="instructors"
                        :searchable="true"
                        :close-on-select="true"
                        :show-labels="true"
                        placeholder="Select Instructor"
                        mode="tags"
                        :style="{height: (form.instructor.length ? 'auto' : '')}"
                    >
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
                <div class="flex flex-col md:w-1/3 md:pl-3 h-full mb-4">
                    <InputLabel value="Any Time" for="time" />
                    <Multiselect
                        id="time"
                        v-model="form.time"
                        :options="[
                            {value: 'am', label: 'AM'},
                            {value: 'pm', label: 'PM'},
                        ]"
                        :searchable="true"
                        class="mt-1"
                        placeholder="Select time"
                    />
                </div>
            </div>
            <Splide
                class="classes-timetable"
                :options="{
                    perPage: calendarPerPage,
                    perMove: 1,
                    arrows: false,
                    pagination: false,
                    gap: '1rem',
                    isNavigation: true,
                }"
                ref="timetableEl"
                @splide:moved="handleMoved"
            >
                <SplideSlide
                    v-for="(time, index) in timetable"
                    class="inline-flex shrink-0 py-2 px-3 rounded-lg justify-center bg-white"
                    :class="{
                        'bg-yellow-500': (!form.date && index == 0) || time.toSQLDate() == form.date,
                        'mr-3': index < timetable.length-1
                    }"
                >
                    <div
                        class="flex flex-col md:flex-row"
                        :class="{
                            'text-white': (!form.date && index == 0) || time.toSQLDate() == form.date,
                            'text-black': time.toSQLDate() != form.date,
                        }"
                    >
                        <div class="text-2xl font-bold mr-2 self-center">{{ time.toFormat('d') }}</div>
                        <div class="flex flex-row md:flex-col justify-center">
                            <div class="text-xs">{{ time.toFormat('MMM') }}</div>
                            <div class="text-xs md:hidden mx-1">/</div>
                            <div class="text-xs">{{ time.toFormat('EEE') }}</div>
                        </div>
                    </div>
                </SplideSlide>
            </Splide>

            <Splide
                class="mt-3"
                :options="{
                    perPage: classesPerPage,
                    perMove: 1,
                    arrows: false,
                    pagination: false,
                    gap: '1rem',
                }"
                ref="classesEl"
            >
                <SplideSlide
                    v-for="(time, index) in timetable"
                    :key="index"
                >
                    <div v-if="!classes[time.toSQLDate()]" class="flex flex-col">&nbsp;</div>
                    <div v-else
                        v-for="(item, index) in classes[time.toSQLDate()]"
                        class="flex flex-col cursor-pointer"
                        @click="showModal(item)"
                        :class="{'hidden': form.time && DateTime.fromISO(item.start_date).setZone(business_seetings.timezone).toFormat('a').toUpperCase() != form.time.toUpperCase()}"
                    >
                        <div class="flex flex-col bg-white rounded-md p-3 mb-3" :class="{'bg-yellow-300': classDetails.id == item.id}">
                            <div class="flex flex-row justify-between mb-4">
                                <div class="text-xl font-medium self-center" v-tooltip="DateTime.fromISO(item.start_date).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js +' '+ business_seetings.time_format.format_js)">
                                    {{ DateTime.fromISO(item.start_date).setZone(business_seetings.timezone).toFormat('hh:mm a') }}
                                </div>
                                <div class="flex flex-row rounded-lg text-green-800 px-2 py-1 h-8 items-center" style="background: rgba(66, 112, 95, 0.2)">
                                    <div class="mr-1"><ClockIcon /></div>
                                    <div class="text-base font-semibold">{{ item.duration }}</div>
                                </div>
                            </div>
                            <div class="flex flex-row mb-4 pt-4 border-t-2 border-gray-300 items-center">
                                <div class="flex mr-2 w-7 h-7 justify-center items-center">
                                    <span class="inline-flex rounded-xl w-3 h-3 bg-red-500"></span>
                                </div>
                                <div class="flex-grow uppercase font-medium">{{ item.class_type?.title }}</div>
                            </div>
                            <div class="flex flex-row mb-4 items-center">
                                <div class="flex mr-2 w-7 h-7">
                                    <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                                    <Avatar :title="item.instructor?.name" size="small" />
                                </div>
                                <div class="flex-grow font-medium text-gray-800">{{ item.instructor?.name }}</div>
                            </div>
                            <div class="flex flex-row justify-between pt-4 border-t-2 border-gray-300">
                                <div class="flex mr-2">
                                    <LocationIcon />
                                </div>
                                <div class="flex-grow font-medium text-green-700">{{ item.studio?.location?.title }}</div>
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
                <img class="w-full rounded-md" v-if="classDetails.studio?.location?.images?.length" :src="classDetails.studio?.location?.images[0].url" :alt="classDetails.studio?.location?.images[0].original_filename">
            </div>
            <div class="flex flex-col">
                <div class="flex text-3xl font-bold mb-4 items-center">
                    <span class="inline-flex mr-4">{{ classDetails.title }}</span>
                    <span class="inline-flex text-sm font-normal rounded-lg bg-green-500 text-white p-2">Free</span>
                </div>

                <div class="flex flex-row mb-4">
                    <div class="flex flex-row">
                        <div class="flex mr-2">
                            <!-- <img src="" class="inline-block rounded-xl w-full h-full bg-gray-500" alt="User" /> -->
                            <Avatar :title="classDetails.instructor?.name ?? ''" />
                        </div>
                        <div class="flex">
                            <span class="inline-flex flex-grow text-gray-800 items-center font-bold">{{ classDetails.instructor?.name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Class Type:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.class_type?.title }}
                    </div>
                </div>

                <div class="flex flex-row mb-3">
                    <div class="w-1/2 flex mr-2 items-center">
                        Time:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat('hh:mm a') }} - {{ classDetails.end_date?.toFormat('hh:mm a') }}
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
                    <div class="w-1/2 flex mr-2 items-center">
                        Date:
                    </div>
                    <div class="flex w-1/2 justify-end font-bold">
                        {{ classDetails.start_date?.toFormat(business_seetings.date_format?.format_js) }}
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <ButtonLink class="mr-2" styling="default" size="default" @click="closeModal">Close</ButtonLink>
            <ButtonLink v-if="classDetails.bookings?.length" styling="secondary" size="default" @click="cancelBooking">Cancel Booking</ButtonLink>
            <ButtonLink v-else-if="$page.props.user" styling="secondary" size="default" @click="handleBooking" :class="{ 'opacity-25': bookingForm.processing }" :disabled="bookingForm.processing">Book</ButtonLink>
            <ButtonLink v-else styling="secondary" size="default" @click="handleBooking" :class="{ 'opacity-25': bookingForm.processing }" :disabled="bookingForm.processing">Sign in to Book</ButtonLink>
        </template>
    </SideModal>
</template>
