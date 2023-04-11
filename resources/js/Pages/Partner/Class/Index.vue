<script setup>
import { ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from "dayjs/plugin/relativeTime";

import Form from "./Form.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";

import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

// import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
dayjs.extend(relativeTime);

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    classes: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,

    statuses: Object,
    studios: Object,
    instructors: Object,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_class = useForm({
    name: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: null,
    studio_id: null,
    is_offpeak: false,
    does_repeat: false,
    repeat_end_date: null,
    week_days: [],
});

const runSearch = () => {
    form.get(route('partner.classes.index'), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const setPerPage = (n) => {
    form.per_page = n;
    runSearch();
};

// form.search getter only;
watch(() => form.search, runSearch);

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route('partner.classes.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

//create confirmation modal:
const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_class.reset().clearErrors();
};

const storeClass = () => {
    console.log('storeClass');
    form_class.post(route('partner.classes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form_class.reset();
            closeCreateModal();
        }
    });
};

</script>
<template>
    <data-table-layout
        :disableButton="true">

        <template #button>
            <div class="flex gap-2">
                <SecondaryButton @click="null">
                    Import
                </SecondaryButton>
                <SecondaryButton @click="null">
                    Export
                </SecondaryButton>
                <SecondaryButton @click="showCreateModal = true">
                    Create new (modal)
                </SecondaryButton>
                <ButtonLink :href="route('partner.classes.create')" type="primary">
                    Create new (direct)
                </ButtonLink>
            </div>
            <DialogModal :show="showCreateModal" @close="closeCreateModal">
                <template #title>
                    Create new Class
                </template>

                <template #content>
                    <Form 
                        :form="form_class"
                        :isNew="true"
                        :statuses="statuses"
                        :studios="studios"
                        :instructors="instructors"
                        :submitted="storeClass"
                        />
                </template>
            </DialogModal>
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @pp_changed="setPerPage"
                />
        </template>

        <template #tableHead>
            <table-head title="Id"/>
            <table-head title="Name"/>
            <table-head title="Studio ID"/>
            <table-head title="Instructor ID"/>
            <table-head title="Status"/>
            <table-head title="Start/End Date"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="class_lesson in classes.data" >
                <table-data :title="class_lesson.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classes.show', class_lesson)"> {{ class_lesson.name }} </Link>
                </table-data>
                <table-data :title="class_lesson.studio_id"/>
                <table-data :title="class_lesson.instructor_id"/>
                <table-data>
                    <!-- <span :class="classStatuses[class_lesson.status].color"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ">
                        {{ classStatuses[class_lesson.status].label }}
                    </span> -->
                    <span 
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ">
                        {{ class_lesson.status_label }}
                    </span>
                </table-data>
                <table-data :title="dayjs(class_lesson.start_date).format('dddd, MMMM D, YYYY (h:mm A)')" :subtitle="dayjs(class_lesson.end_date).format('dddd, MMMM D, YYYY (h:mm A)')"/>
                <table-data :title="dayjs(class_lesson.updated_at).fromNow()"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classes.edit', class_lesson)">
                        Edit
                    </Link>
                    <br>
                    <button class="block text-red-500"
                            @click="confirmDeletion(class_lesson.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="classes.links"/>

            <p class="p-2 text-xs">Viewing {{classes.from}} - {{classes.to}} of {{classes.total}} results</p>
        </template>    
    </data-table-layout>
    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title>
            Confirmation required
        </template>

        <template #content>
            Are you sure you would like to delete this?
        </template>

        <template #footer>
            <SecondaryButton @click="itemDeleting = null">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteItem"
            >
                Delete
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>