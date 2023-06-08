<script setup>
import { ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import SideModal from "@/Components/SideModal.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import WarningButton from "@/Components/WarningButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
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
    form.get(route("partner.classtypes.index"), {
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

// Create/Edit classtypes Queries
let form_class = useForm({
    title: null,
    description: null,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeItem = () => {
    form.post(route("partner.classtypes.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

let form_edit = useForm({
    id: "",
    title: null,
    description: null,
});

const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (data) => {
    showEditModal.value = true;
    form_edit.id = data.id;
    form_edit.title = data.title;
    form_edit.description = data.description;
};

const updateItem = () => {
    form_edit.put(route("partner.classtypes.update", form_edit), {
        preserveScroll: true,
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
        route("partner.classtypes.destroy", { id: itemIdDeleting.value }),
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
    <data-table-layout :disable-search="disableSearch" :disableButton="true">
        <template #button>
            <WarningButton @click="showCreateModal = true">
                Create a new classtype
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </WarningButton>
            <WarningButton
                :href="route('partner.classtypes.create')"
                type="primary"
            >
                Create a new classtype (direct)
            </WarningButton>
        </template>
        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @pp_changed="setPerPage"
                noFilter
            />
        </template>

        <template #tableHead>
            <table-head
                title="Id"
                @click="setOrdering('id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'id'"
            />
            <table-head
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head
                title="Description"
                @click="setOrdering('description')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'description'"
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
            <table-head title="Action" />
        </template>

        <template #tableData>
            <tr v-for="(classtype, index) in classtypes.data">
                <table-data :title="classtype.id" />
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.classtypes.show', classtype)"
                    >
                        {{ classtype.title }}
                    </Link>
                </table-data>
                <table-data :title="classtype.description" />
                <table-data :title="DateTime.fromISO(classtype.created_at)" />
                <table-data
                    :title="DateTime.fromISO(classtype.updated_at).toRelative()"
                />
                <table-data>
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > classtypes.data.length - 3"
                        :content-classes="['bg-white']"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <font-awesome-icon :icon="faCog" />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="
                                    route('partner.classtypes.edit', classtype)
                                "
                            >
                                <EditIcon
                                    class="w-4 lg:w-24vw h-4 lg:h-24vw mr-0 md:mr-2"
                                />
                                Edit
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="handleUpdateForm(classtype)"
                            >
                                <span class="text-danger flex items-center">
                                    <EditIcon
                                        class="w-4 lg:w-24vw h-4 lg:h-24vw mr-0 md:mr-2"
                                    />
                                    <span> Edit (Modal) </span>
                                </span>
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="confirmDeletion(classtype.id)"
                            >
                                <span class="text-danger flex items-center">
                                    <DeleteIcon
                                        class="w-4 lg:w-24vw h-4 lg:h-24vw mr-0 md:mr-2"
                                    />
                                    <span> Delete </span>
                                </span>
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="classtypes.links"
                :to="classtypes.to"
                :from="classtypes.from"
                :total="classtypes.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Create new classtype Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new classtype </template>

        <template #content>
            <Form :form="form_class" :submitted="storeItem" modal />
        </template>
    </SideModal>

    <!-- Update classtype Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update classtype </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateItem" modal />
        </template>
    </SideModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title> Confirmation required </template>

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
