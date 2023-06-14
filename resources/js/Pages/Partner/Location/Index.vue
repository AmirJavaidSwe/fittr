<script setup>
import { ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Form from './Form.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from 'axios';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    locations: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    users: Array,
    amenities: Array,
    countries: Array
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const createForm = useForm({
    title: '',
    brief: '',
    manager_id: '',
    manager_email: '',
    address_line_1: '',
    address_line_2: '',
    country_id: '',
    city: '',
    postcode: '',
    map_latitude: '',
    map_longitude: '',
    tel: '',
    email: '',
    image: null,
    amenity_ids: [],
});

const runSearch = () => {
    form.get(route('partner.locations.index'), {
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

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route('partner.locations.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

// create new location modal
const modal = ref(false);
const editMode = ref(false);

const saveForm = () => {
    createForm.post(route('partner.locations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            modal.value = false;
            createForm.reset().clearErrors();
        }
    });
};

const showCreateModal = () => {

}

const showEditModal = (data) => {
    console.log({data});
    createForm.title = data.title;
    createForm.brief = data.brief;
    createForm.manager_id = data.manager?.id;
    createForm.manager_email = data.manager?.email;
    createForm.address_line_1 = data.address_line_1;
    createForm.address_line_2 = data.address_line_2;
    createForm.country_id = data.country_id;
    createForm.city = data.city;
    createForm.postcode = data.postcode;
    createForm.map_latitude = data.map_latitude;
    createForm.map_longitude = data.map_longitude;
    createForm.tel = data.tel;
    createForm.email = data.email;
    createForm.image = data.image;
    // createForm.amenity_ids = data.amenity_ids;
    editMode.value = true;
    modal.value = true;
}

</script>
<template>
    <data-table-layout
        button-title="Create new"
        :button-link="route('partner.locations.create')"
        :disable-button="true"
        :disable-search="disableSearch">

        <template #button>
            <button class="cursor-pointer inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto" @click="modal = true">
                Create new
            </button>
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
            <table-head title="Title"/>
            <table-head title="General Manager"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="location in locations.data" >
                <table-data :title="location.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.locations.show', location)"> {{ location.title }} </Link>
                </table-data>
                <table-data :title="location.manager?.name"/>
                <table-data :title="DateTime.fromISO(location.created_at)"/>
                <table-data :title="DateTime.fromISO(location.updated_at).toRelative()"/>
                <table-data>
                    <button class="font-medium text-indigo-600 hover:text-indigo-500" @click="showEditModal(location)">
                        Edit
                    </button>
                    <br>
                    <button class="block text-red-500" @click="confirmDeletion(location.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="locations.links"/>

            <p class="p-2 text-xs">Viewing {{locations.from}} - {{locations.to}} of {{locations.total}} results</p>
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

    <!-- Create Location Modal -->
    <DialogModal :show="modal" @close="modal = false">
        <template #title>
            Create Location
        </template>

        <template #content>
            <Form :form="createForm" :users="users" :amenities="amenities" :countries="countries" />
        </template>
        <template #footer>
            <SecondaryButton type="button" class="mr-2" @click="modal = false">Cancel</SecondaryButton>
            <PrimaryButton type="button" @click="saveForm">Save</PrimaryButton>
        </template>
    </DialogModal>
</template>