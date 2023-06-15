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

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    studios: Object,
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

const runSearch = () => {
    form.get(route('partner.studios.index'), {
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
    form.delete(route('partner.studios.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

</script>
<template>
    <data-table-layout
        button-title="Create new"
        :button-link="route('partner.studios.create')"
        :disable-search="disableSearch">

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
            <table-head title="Location"/>
            <table-head title="Ordering"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="studio in studios.data" >
                <table-data :title="studio.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.studios.show', studio)"> {{ studio.title }} </Link>
                </table-data>
                <table-data :title="studio.location?.title"/>
                <table-data :title="studio.ordering"/>
                <table-data :title="DateTime.fromISO(studio.created_at)"/>
                <table-data :title="DateTime.fromISO(studio.updated_at).toRelative()"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.studios.edit', studio)">
                        Edit
                    </Link>
                    <br>
                    <button class="block text-red-500" @click="confirmDeletion(studio.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="studios.links"/>

            <p class="p-2 text-xs">Viewing {{studios.from}} - {{studios.to}} of {{studios.total}} results</p>
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