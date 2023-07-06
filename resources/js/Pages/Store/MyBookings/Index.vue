<script setup>
import Section from '@/Components/Section.vue';
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
import Search from '@/Components/DataTable/Search.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { watch } from 'vue';
import DateValue from '@/Components/DataTable/DateValue.vue';
import { faCog } from '@fortawesome/free-solid-svg-icons';
import { DateTime } from 'luxon';
import ColoredValue from '@/Components/DataTable/ColoredValue.vue';
import AvatarValue from '@/Components/DataTable/AvatarValue.vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    business_seetings: Object,
    bookings: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    page_title: String,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const runSearch = () => {
    form.get(route('ss.member.bookings.index', { subdomain: props.business_seetings.subdomain }), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const setOrdering = (col) => {
    //reverse same col order
    if (form.order_by == col) {
        form.order_dir = form.order_dir == "asc" ? "desc" : "asc";
    }
    form.order_by = col;
    runSearch();
};

const setPerPage = (n) => {
    form.per_page = n;
    runSearch();
};

// form.search getter only;
watch(() => form.search, runSearch);

const bookingForm = useForm({
    class_id: ''
});

const cancelBooking = (class_id) => {
    bookingForm.class_id = class_id;

    bookingForm.post(route('ss.member.bookings.cancel', {subdomain: props.business_seetings.subdomain}), {
        onSuccess: (res) => {
            // if(res.props.flash.type === 'success') {
            //     closeModal();
            // }
        }
    });
}

</script>
<template>
    <Section bg="bg-transparent" class="flex flex-col">
        <div class="text-xl font-bold mb-4"> {{ page_title }} </div>
        <data-table-layout
        :disable-button="true"
        :disable-search="disableSearch">

            <template #search>
                <Search
                    v-model="form.search"
                    :disable-search="disableSearch"
                    @reset="form.search = null"
                    @pp_changed="setPerPage"
                    :noFilter="true"
                    />
            </template>

            <template #tableHead>
                <table-head
                    title="Class Title"
                />
                <table-head
                    title="Class Type"
                />
                <table-head
                    title="Date"
                />
                <table-head
                    title="Start Time"
                />
                <table-head
                    title="End Time"
                />
                <table-head
                    title="Duration"
                />
                <table-head
                    title="Instructor"
                />
                <table-head
                    title="Location"
                />

                <table-head
                    title="Status"
                />

                <table-head title="Action" />
            </template>

            <template #tableData>
                <tr v-for="(booking, index) in bookings.data">
                    <table-data>{{ booking.class?.title }}</table-data>
                    <!-- <table-data :title="booking.class?.class_type?.title"/> -->
                    <table-data>
                        <ColoredValue :title="booking.class?.class_type?.title" color="#ccc" />
                    </table-data>
                    <table-data>
                        <DateValue :date="DateTime.fromISO(booking.class?.start_date).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js)" />
                    </table-data>
                    <table-data>
                        <DateValue :date="DateTime.fromISO(booking.class?.start_date).setZone(business_seetings.timezone).toFormat(business_seetings.time_format.format_js)" />
                    </table-data>
                    <table-data>
                        <DateValue :date="DateTime.fromISO(booking.class?.end_date).setZone(business_seetings.timezone).toFormat(business_seetings.time_format.format_js)" />
                    </table-data>
                    <table-data> {{ booking.class?.duration }} </table-data>
                    <table-data>
                        <AvatarValue :title="booking.class.instructor?.name" />
                    </table-data>
                    <table-data> {{ booking.class?.studio?.location?.title }} </table-data>
                    <table-data> {{ booking.status_text }} </table-data>
                    <table-data>
                        <Dropdown
                            v-if="booking.status_text.toLowerCase() == 'active'"
                            align="right"
                            width="48"
                            :top="index > bookings.data.length - 3"
                            :content-classes="['bg-white']"
                        >
                            <template #trigger>
                                <button class="text-dark text-lg">
                                    <font-awesome-icon :icon="faCog" />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink
                                    as="button"
                                    @click="cancelBooking(booking.class?.id)"
                                >
                                    <span class="text-danger-500 flex items-center">
                                        <!-- <DeleteIcon
                                            class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                        /> -->
                                        <span> Cancel </span>
                                    </span>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </table-data>
                </tr>
            </template>

            <template #pagination>
                <pagination
                    :links="bookings.links"
                    :to="bookings.to"
                    :from="bookings.from"
                    :total="bookings.total"
                    @pp_changed="setPerPage"
                />
            </template>
        </data-table-layout>
    </Section>
</template>