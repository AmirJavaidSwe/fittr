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
    classtypes: Object,
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
    form.get(route('partner.classtypes.index'), {
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
    form.delete(route('partner.classtypes.destroy', { id: itemIdDeleting.value }), {
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
        :button-link="route('partner.classtypes.create')"
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
            <table-head title="Description"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="classtype in classtypes.data" >
                <table-data :title="classtype.id"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classtypes.show', classtype)"> {{ classtype.title }} </Link>
                </table-data>
                <table-data :title="classtype.description"/>
                <table-data :title="DateTime.fromISO(classtype.created_at)"/>
                <table-data :title="DateTime.fromISO(classtype.updated_at).toRelative()"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.classtypes.edit', classtype)">
                        Edit
                    </Link>
                    <br>
                    <button class="block text-red-500" @click="confirmDeletion(classtype.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="classtypes.links"/>

            <p class="p-2 text-xs">Viewing {{classtypes.from}} - {{classtypes.to}} of {{classtypes.total}} results</p>
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
