<script setup>
import { ref, watch, computed } from "vue";
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
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import ButtonLink from "@/Components/ButtonLink.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import OnTheFlyResourceCreate from "@/Components/OnTheFlyResourceCreate.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import uniqBy from "lodash/uniqBy";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_seetings: Object,
    studios: Object,
    locations: Array,
    class_types: Array,
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
    form.get(route("partner.studios.index"), {
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

// Create/Edit Studios Queries
let form_class = useForm({
    title: null,
    location_id: null,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_class.reset().clearErrors();
};

const storeStudio = () => {
    form_class.post(route("partner.studios.store"), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), closeCreateModal()],
    });
};

let form_edit = useForm({
    id: "",
    title: null,
    location_id: null,
    class_type_studios: []
});

const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
    form_edit.reset().clearErrors();
};

const handleUpdateForm = (studio) => {
    showEditModal.value = true;
    form_edit.id = studio.id;
    form_edit.title = studio.title;
    form_edit.location_id = studio.location_id;
    form_edit.class_type_studios = [...studio.class_type_studios];
};

const updateStudios = () => {
    form_edit.put(route("partner.studios.update", form_edit), {
        preserveScroll: true,
        onSuccess: () => [form_edit.reset(), closeEditModal()],
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
        route("partner.studios.destroy", { id: itemIdDeleting.value }),
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

const showLocationCreateForm = ref(false);
const closeLocationCreateForm = () => {
    showLocationCreateForm.value = false;
    form_class.location_id = null;
    form_edit.location_id = null;
};

const locationList = computed(() => {
    let newLocationList = props.locations;
    newLocationList.push({
        id: "create_new_location",
        title: "Add New",
    });
    return uniqBy(newLocationList, "id");
});
</script>
<template>
    <data-table-layout :disable-search="disableSearch" :disableButton="true">
        <template #button>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal = true"
            >
                Create a new studio
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.studios.create')"
                type="primary"
            >
                Create a new studio (direct)
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
            <table-head title="Location" />
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
            <tr v-for="(studio, index) in studios.data" :key="index">
                <!-- <table-data :title="studio.id" /> -->
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.studios.show', studio)"
                    >
                        {{ studio.title }}
                    </Link>
                </table-data>
                <table-data :title="studio.location?.title" />
                <table-data :title="studio.ordering" />
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(studio.created_at)
                                .setZone(business_seetings.timezone)
                                .toFormat(
                                    business_seetings.date_format.format_js
                                )
                        "
                    />
                </table-data>
                <table-data>
                    <DateValue
                        :date="DateTime.fromISO(studio.updated_at).toRelative()"
                    />
                </table-data>
                <table-data class="justify-end flex">
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > studios.data.length - 3"
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
                                @click="handleUpdateForm(studio)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                <span> Edit </span>
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="confirmDeletion(studio.id)"
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
                :links="studios.links"
                :to="studios.to"
                :from="studios.from"
                :total="studios.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Create new studio Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new studio </template>

        <template #content>
            <Form
                :form="form_class"
                :submitted="storeStudio"
                :locations="locationList"
                @create-new-location="showLocationCreateForm = true"
                modal
            />
        </template>
    </SideModal>

    <!-- Update studio Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update studio </template>

        <template #content>
            <Form
                :form="form_edit"
                :submitted="updateStudios"
                :locations="locations"
                :class_types="class_types"
                @create-new-location="showLocationCreateForm = true"
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

    <OnTheFlyResourceCreate
        :show-location-create-form="showLocationCreateForm"
        @close-location-create-form="closeLocationCreateForm"
    />
</template>
