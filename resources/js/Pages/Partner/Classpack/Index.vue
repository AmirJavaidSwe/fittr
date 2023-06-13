<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import SideModal from "@/Components/SideModal.vue";
import { faPlus, faCog } from "@fortawesome/free-solid-svg-icons";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import FormFilter from "./FormFilter.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
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
    form.get(route("partner.classpacks.index"), {
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

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(
        route("partner.classpacks.destroy", { id: itemIdDeleting.value }),
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

//create modal:
const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_item.reset().clearErrors();
};
//create item from modal
const storeItem = () => {
    form_item.post(route("partner.classpacks.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeCreateModal();
        },
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
    form_item.put(route("partner.classpacks.update", { id: editedId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            form_item.reset();
            closeEditModal();
        },
    });
};

//create modal:
const showFilterModal = ref(false);
const closeFilterModal = () => {
    showFilterModal.value = false;
};
</script>
<template>
    <data-table-layout :disableButton="true">
        <template #button>
            <div class="flex gap-2">
                <ButtonLink
                    styling="secondary"
                    size="default"
                    @click="showCreateModal = true"
                >
                    Create new
                    <font-awesome-icon class="ml-2" :icon="faPlus" />
                </ButtonLink>
                <ButtonLink
                    styling="secondary"
                    size="default"
                    :href="route('partner.classpacks.create')"
                    type="primary"
                >
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
                @onFilter="showFilterModal = true"
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
                title="Sessions"
                @click="setOrdering('sessions')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'sessions'"
            />
            <table-head
                title="Price"
                @click="setOrdering('price')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'price'"
            />
            <table-head
                title="Type"
                @click="setOrdering('type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'type'"
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
            <tr v-for="(classpack, index) in classpacks.data">
                <table-data :title="classpack.id" />
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.classpacks.show', classpack)"
                    >
                        {{ classpack.title }}
                    </Link>
                </table-data>
                <table-data :title="classpack.sessions" />
                <table-data :title="classpack.price" />
                <table-data :title="classpack.type" />
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(classpack.created_at)
                                .setZone(business_seetings.timezone)
                                .toFormat(
                                    business_seetings.date_format.format_js
                                )
                        "
                    />
                </table-data>
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(classpack.updated_at).toRelative()
                        "
                    />
                </table-data>
                <table-data>
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > classpacks.data.length - 3"
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
                                    route('partner.classpacks.edit', classpack)
                                "
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                Edit
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="showEditModal(classpack)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                <span> Edit (Modal) </span>
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="confirmDeletion(classpack.id)"
                            >
                                <span class="text-danger-500 flex items-center">
                                    <DeleteIcon
                                        class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
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
                :links="classpacks.links"
                :to="classpacks.to"
                :from="classpacks.from"
                :total="classpacks.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Create new Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new Class pack </template>

        <template #content>
            <Form
                :form="form_item"
                :isNew="true"
                :options_types="options_types"
                :options_periods="options_periods"
                :classtypes="classtypes"
                :submitted="storeItem"
                modal
            />
        </template>
    </SideModal>

    <!-- Edit Modal -->
    <SideModal :show="editModalOpened" @close="closeEditModal">
        <template #title> Edit Class pack </template>

        <template #content>
            <Form
                :form="form_item"
                :options_types="options_types"
                :options_periods="options_periods"
                :classtypes="classtypes"
                :submitted="updateItem"
                modal
            />
        </template>
    </SideModal>

    <!-- Filter Modal -->
    <SideModal :show="showFilterModal" @close="closeFilterModal">
        <template #title> Filter Class pack </template>

        <template #content>
            <FormFilter
                :form="form_item"
                :options_types="options_types"
                :options_periods="options_periods"
                :classtypes="classtypes"
                modal
            />
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
