<script setup>
import { ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import SideModal from "@/Components/SideModal.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faUserLock, faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import { Dialog } from "@headlessui/vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import CloseModal from "@/Components/CloseModal.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    instructors: Object,
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
    form.get(route("partner.instructors.index"), {
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

// Create/Edit Member Queries
let form_class = useForm({
    name: "",
    email: "",
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeInstructor = () => {
    form_class.post(route("partner.instructors.store"), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), closeCreateModal()],
    });
};

let form_edit = useForm({
    id: "",
    name: "",
    email: "",
});

const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (data) => {
    showEditModal.value = true;
    form_edit.id = data.id;
    form_edit.name = data.name;
    form_edit.email = data.email;
};

const updateInstructors = () => {
    form_edit.put(route("partner.instructors.update", form_edit.id), {
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
        route("partner.instructors.destroy", { id: itemIdDeleting.value }),
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
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal = true"
            >
                Create a new instructor
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.instructors.create')"
                type="primary"
            >
                Create a new instructor (direct)
            </ButtonLink> -->
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                noFilter
            />
        </template>

        <template #tableHead>
            <!-- <table-head title="Id" @click="setOrdering('id')" :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'id'" /> -->
            <table-head
                title="Name"
                @click="setOrdering('name')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'name'"
            />
            <table-head
                title="Email"
                @click="setOrdering('email')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'email'"
            />
            <table-head
                title="Created"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head
                title="Updated"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <table-head title="Action" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="(instructor, index) in instructors.data" :key="index">
                <!-- <table-data :title="instructor.id" /> -->
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.instructors.show', instructor)"
                    >
                        {{ instructor.name }}
                    </Link>
                </table-data>
                <table-data :title="instructor.email" />
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(
                                instructor.created_at
                            ).toLocaleString()
                        "
                    />
                </table-data>
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(instructor.updated_at).toRelative()
                        "
                    />
                </table-data>
                <table-data class="text-right">
                    <div class="flex justify-end">
                        <Link
                            :href="route('partner.login-as')"
                            :data="{ id: instructor.id }"
                            method="post"
                            as="button"
                            type="button"
                            class="mr-2"
                            title="Login as"
                        >
                            <font-awesome-icon
                                class="mr-2"
                                :icon="faUserLock"
                            />
                        </Link>
                        <Dropdown
                            align="right"
                            width="48"
                            :top="index > instructors.data.length - 3"
                            :content-classes="['bg-white']"
                        >
                            <template #trigger>
                                <button class="text-dark text-lg">
                                    <ActionsIcon />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink
                                    as="button"
                                    @click="handleUpdateForm(instructor)"
                                >
                                    <EditIcon
                                        class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                    />
                                    <span> Edit </span>
                                </DropdownLink>
                                <DropdownLink
                                    as="button"
                                    @click="confirmDeletion(instructor.id)"
                                >
                                    <span
                                        class="text-danger-500 flex items-center"
                                    >
                                        <DeleteIcon
                                            class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                        />
                                        <span> Delete </span>
                                    </span>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="instructors.links"
                :to="instructors.to"
                :from="instructors.from"
                :total="instructors.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Create new instructor Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new instructor </template>
        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

        <template #content>
            <Form :form="form_class" :submitted="storeInstructor" modal />
        </template>
    </SideModal>

    <!-- Update instructor Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update instructor </template>
        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateInstructors" modal />
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
