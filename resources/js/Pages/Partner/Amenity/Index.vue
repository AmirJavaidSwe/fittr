<script setup>
import { ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import relativeTime from "dayjs/plugin/relativeTime";
import dayjs from "dayjs";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
dayjs.extend(relativeTime);

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false
    },
    amenities: Object,
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
    form.get(route('partner.amenity.index'), {
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
    form.delete(route('partner.amenity.destroy', { id: itemIdDeleting.value }), {
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
        :button-link="route('partner.amenity.create')">

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
            <table-head title="Icon"/>
            <table-head title="Title"/>
            <table-head title="Ordering"/>
            <table-head title="Status"/>
            <table-head title="Created At"/>
            <table-head title="Updated At"/>
            <table-head title="Action"/>
        </template>

        <template #tableData>
            <tr v-for="amenity in amenities.data">
                <table-data :title="amenity.id"/>
                <table-data>
                    <img v-if="amenity.image_url" :src="amenity.image_url" alt="icon" class="w-10 h-10">
                </table-data>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.amenity.show', amenity)"> {{ amenity.title }} </Link>
                </table-data>
                <table-data :title="amenity.ordering"/>
                <table-data>
                    <span v-bind:class="amenity.status ? 'text-green-800 bg-green-100' : ' text-red-800 bg-red-100'"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ">
                        {{ amenity.status ? 'Active' : 'Inactive' }}
                    </span>
                </table-data>
                <table-data :title="dayjs(amenity.created_at).fromNow()"/>
                <table-data :title="dayjs(amenity.updated_at).fromNow()"/>
                <table-data>
                    <Link class="font-medium text-indigo-600 hover:text-indigo-500"
                          :href="route('partner.amenity.edit', amenity)">
                        Edit
                    </Link>
                    <br>
                    <button class="block text-red-500" @click="confirmDeletion(amenity.id)">
                        Delete
                    </button>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="amenities.links"/>
            <p class="p-2 text-xs">Viewing {{amenities.from}} - {{amenities.to}} of {{amenities.total}} results</p>
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


