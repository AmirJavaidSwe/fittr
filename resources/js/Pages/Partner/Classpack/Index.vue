<script setup>
import { ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import Form from "./Form.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    business_seetings: Object,
    options_types: Object,
    options_periods: Object,
    classtypes: Object,
    classpacks: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_fields = {
    title: null,
    sessions: null,
    is_expiring: false,
    expiration: null,
    expiration_period: null,
    is_active: false,
    price: null,
    type: null,
    is_renewable: true,
    is_intro: false,
    is_private: false,
    private_url: null,
    active_from: null,
    active_to: null,
    is_restricted: false,
    restrictions: null,
};
const form_item = useForm(form_fields);

const runSearch = () => {
    form.get(route('partner.classpacks.index'), {
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
    form.delete(route('partner.classpacks.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

//create modal:
const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_item.reset().clearErrors();
};
//create item from modal
const storeItem = () => {
    form_item.post(route('partner.classpacks.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeCreateModal();
        }
    });
};

//edit modal:
const editModalOpened = ref(false);
const editedId = ref(null);
const showEditModal = (classpack) => {
    //set pack edited to form
    for (const property in form_fields) {
        form_item[property] = classpack[property];
    }
    editModalOpened.value = true;
    editedId.value = classpack.id;
};
const closeEditModal = () => {
    editModalOpened.value = false;
    editedId.value = null;
    form_item.reset().clearErrors();
};
//save item from modal:
const updateItem = () => {
    form_item.put(route('partner.classpacks.update', {id: editedId.value}), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeEditModal();
        }
    });
};
</script>
<template>
    <data-table-layout
        :disableButton="true"
       >
        <template #button>
            <div class="flex gap-2">
                <SecondaryButton @click="showCreateModal = true">
                    Create new (modal)
                </SecondaryButton>
                <ButtonLink :href="route('partner.classpacks.create')" type="primary">
                    Create new (direct)
                </ButtonLink>
            </div>
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
            <table-head title="Sessions"/>
            <table-head title="Price"/>
            <table-head title="Type"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="classpack in classpacks.data" >
                <table-data :title="classpack.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classpacks.show', classpack)"> {{ classpack.title }} </Link>
                </table-data>
                <table-data :title="classpack.sessions"/>
                <table-data :title="classpack.price"/>
                <table-data :title="classpack.type"/>
                <table-data :title="DateTime.fromISO(classpack.created_at).setZone(business_seetings.timezone).toFormat(business_seetings.date_format.format_js)"/>
                <table-data :title="DateTime.fromISO(classpack.updated_at).toRelative()"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classpacks.edit', classpack)">
                        Edit
                    </Link>
                    <a @click.stop="showEditModal(classpack)" class="cursor-pointer">Edit2</a>
                    <br>
                    <button class="block text-red-500" @click="confirmDeletion(classpack.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="classpacks.links"/>
            <p class="p-2 text-xs">Viewing {{classpacks.from}} - {{classpacks.to}} of {{classpacks.total}} results</p>
        </template>    
    </data-table-layout>

    <!-- Create new Modal -->
    <DialogModal :show="showCreateModal" @close="closeCreateModal">
        <template #title>
            Create new Class pack
        </template>

        <template #content>
            <Form :form="form_item"
                :isNew="true"
                :options_types="options_types"
                :options_periods="options_periods"
                :classtypes="classtypes"
                :submitted="storeItem"/>
        </template>
    </DialogModal>

    <!-- Edit Modal -->
    <DialogModal :show="editModalOpened" @close="closeEditModal">
        <template #title>
            Edit Class pack
        </template>

        <template #content>
            <Form :form="form_item"
                :options_types="options_types"
                :options_periods="options_periods"
                :classtypes="classtypes"
                :submitted="updateItem"/>
        </template>
    </DialogModal>

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