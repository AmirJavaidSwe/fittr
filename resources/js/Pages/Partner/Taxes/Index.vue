<script setup>
import { ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import TopMenu from "./TopMenu.vue";
import SideModal from "@/Components/SideModal.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import ButtonLink from "@/Components/ButtonLink.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ActionsIcon from '@/Icons/ActionsIcon.vue';

const props = defineProps({
    taxes: Array,
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
    form.get(route("partner.taxes.index"), {
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

// Create/Edit taxes Queries
let form_class = useForm({
    tax_name: null,
    tax_id: null,
    is_default_tax: null,
    tax_percentage_value: null,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeTax = () => {
    form_class.post(route("partner.taxes.store"), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), closeCreateModal()],
    });
};

let form_edit = useForm({
    id: null,
    tax_name: null,
    tax_id: null,
    is_default_tax: null,
    tax_percentage_value: null,
});

const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (tax) => {
    showEditModal.value = true;
    form_edit.id = tax.id;
    form_edit.tax_name = tax.tax_name;
    form_edit.tax_id = tax.tax_id;
    form_edit.is_default_tax = tax.is_default_tax;
    form_edit.tax_percentage_value = tax.tax_percentage_value;
};

const updateTax = () => {
    form_edit.put(route("partner.taxes.update", form_edit), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), closeEditModal()],
    });
};

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(
        route("partner.taxes.destroy", { id: itemIdDeleting.value }),
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                itemDeleting.value = false;
                itemIdDeleting.value = null;
            },
        }
        );
    };
</script>
<template>

    <div class="mb-12">
        <TopMenu />
    </div>
    <data-table-layout :disable-search="false" :disableButton="true">
        <template #button>
            <ButtonLink
            styling="secondary"
            size="default"
            @click="showCreateModal = true"
            >
            <font-awesome-icon class="mr-2" :icon="faPlus" />
            <span>
                Create Tax
            </span>
        </ButtonLink>
        <!-- <ButtonLink
            styling="secondary"
            size="default"
            :href="route('partner.taxes.create')"
            type="primary"
            >
            Create a new tax (direct)
        </ButtonLink> -->
    </template>

    <template #search>
        <Search
        v-model="form.search"
        :disable-search="false"
        @reset="form.search = null"
        noFilter
        />
        </template>

        <template #tableHead v-if="!taxes.length">
            <p class="text-xl font-medium pl-12">
                Create your first tax…
            </p>
        </template>
        <template #tableHead v-else>
            <!-- <table-head
            title="Id"
            @click="setOrdering('id')"
            :arrowSide="form.order_dir"
            :currentSort="form.order_by === 'id'"
            /> -->
            <table-head
            title="Title"
            @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head title="Location"/>
            <table-head
                title="Ordering"
                @click="setOrdering('ordering')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'ordering'"
            />
            <table-head
                title="Created At"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <table-head title="Action" :justifyContent="'justify-end'" />
        </template>

        <template #tableData>
            <tr v-for="(tax, index) in taxes.data" :key="index">
            </tr>
        </template>

        <template #pagination>
            <!-- <pagination
                :links="taxes.links"
                :to="taxes.to"
                :from="taxes.from"
                :total="taxes.total"
                @pp_changed="setPerPage"
            /> -->
        </template>
    </data-table-layout>

    <!-- Create new tax Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new Tax </template>

        <template #content>
            <Form :form="form_class" :submitted="storeTax" modal />
        </template>
    </SideModal>

    <!-- Update tax Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update Tax </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateTax" modal />
        </template>
    </SideModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title> Confirmation required </template>

        <template #content>
            Are you sure you would like to delete this?
        </template>

        <template #footer>
            <ButtonLink
                size="default"
                styling="default"
                @click="itemDeleting = null"
            >
                Cancel
            </ButtonLink>

            <ButtonLink
                size="default"
                styling="danger"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deleteItem"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>
</template>
