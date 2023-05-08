<script setup>
import { ref, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import TopMenu from './TopMenu.vue';
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
    exportings: Object,
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
    form.get(route('partner.exports.index'), {
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
    form.delete(route('partner.exports.destroy', { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
    });
};

const bytesToKibibytes = (bytes) => {
    return Math.floor(bytes / 1024)+' KB';
}

const requestExport = (exporting) => {
    axios.get(route('partner.exports.request-to-download', { id: exporting }))
        .then(response => {
            window.location.replace(response.data.url);
        })
        .catch(error => {
            console.log(error);
        });
};


</script>

<template>
    <TopMenu />

    <data-table-layout
        button-title="Create new"
        button-link="#"
        :disable-search="disableSearch">

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @pp_changed="setPerPage"/>
        </template>

        <template #tableHead>
            <table-head title="Id"/>
            <table-head title="Name"/>
            <table-head title="Status"/>
            <table-head title="Type"/>
            <table-head title="Size"/>
            <table-head title="Created At"/>
            <table-head title="Created By"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="exporting in exportings.data" >
                <table-data :title="exporting.id"/>

                <table-data>
                    <template v-if="exporting.status === 'completed'">
                        <Link
                            class="text-indigo-600 hover:text-indigo-500 via-indigo-950"
                            :href="route('partner.exports.show', exporting)">
                            {{ exporting.file_name }}
                        </Link>
                    </template>
                    <template v-else>
                        {{ exporting.file_name }}
                    </template>
                </table-data>

                <table-data :title="exporting.status"/>
                <table-data :title="exporting.type"/>
                <table-data :title="bytesToKibibytes(exporting.file_size)"/>
                <table-data :title="DateTime.fromISO(exporting.created_at).toRelative()"/>

                <table-data>
                    <template v-if="exporting.created_by">
                        {{$page.props.user.id === exporting.created_by ? 'You' : exporting.user.name }}
                        <!-- <Link
                            class="text-indigo-600 hover:text-indigo-500 via-indigo-950"
                            :href="route('partner.members.show', exporting.created_by)">
                            {{ 'ID#'+exporting.user.id+': ' +exporting.user.name }}
                        </Link> -->
                    </template>
                </table-data>

                <table-data>
                    <button v-if="exporting.status === 'completed'"
                            class="font-medium text-white hover:text-white bg-green-500 hover:bg-green-600 rounded py-2 px-4 inline-block mr-2"
                            @click.prevent="requestExport(exporting.id)">
                        Download
                    </button>
<!--                    <Link class="font-medium text-white hover:text-white bg-indigo-600 hover:bg-indigo-700 rounded py-2 px-4 inline-block mr-2"-->
<!--                          :href="route('partner.exports.show', exporting)">-->
<!--                        View-->
<!--                    </Link>-->
                    <button class="text-white bg-red-500 hover:bg-red-600 rounded py-2 px-4 inline-block" @click="confirmDeletion(exporting.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="exportings.links"/>

            <p class="p-2 text-xs">Viewing {{exportings.from}} - {{exportings.to}} of {{exportings.total}} results</p>
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
                @click="deleteItem">
                Delete
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
