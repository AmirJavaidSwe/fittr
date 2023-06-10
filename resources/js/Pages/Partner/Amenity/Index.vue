<script setup>
import { ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import SideModal from "@/Components/SideModal.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
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
    form.get(route("partner.amenity.index"), {
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

// Create/Edit Class
let form_class = useForm({
    title: "",
    icon: "",
    contents: "",
    ordering: "",
    studio_id: "",
    status: false,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeAmenity = () => {
    form_class.post(route("partner.amenity.store"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

let form_edit = form_class;
const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (data) => {
    console.log(data);
    showEditModal.value = true;
    form_edit["id"] = data.id;
    form_edit["title"] = data.title;
    form_edit["icon"] = data.icon;
    form_edit["ordering"] = data.ordering;
    form_edit["status"] = data.status;
    form_edit["studio_id"] = data.studio_id;
    form_edit["contents"] = data.contents;
};

const updateAmenities = () => {
    form_edit.put(route("partner.amenity.update", form_edit), {
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
        route("partner.amenity.destroy", { id: itemIdDeleting.value }),
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
    <data-table-layout :disableButton="true">
        <template #button>
            <WarningButton @click="showCreateModal = true">
                Create a new amenity
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </WarningButton>
            <WarningButton
                :href="route('partner.amenity.create')"
                type="primary"
            >
                Create a new amenity (direct)
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
            <table-head title="Icon" />
            <table-head
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head
                title="Ordering"
                @click="setOrdering('ordering')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'ordering'"
            />
            <table-head
                title="Status"
                @click="setOrdering('status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status'"
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
            <tr v-for="(amenity, index) in amenities.data">
                <table-data :title="amenity.id" />
                <table-data>
                    <img
                        v-if="amenity.image_url"
                        :src="amenity.image_url"
                        alt="icon"
                        class="w-10 h-10"
                    />
                </table-data>
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.amenity.show', amenity)"
                    >
                        {{ amenity.title }}
                    </Link>
                </table-data>
                <table-data :title="amenity.ordering" />
                <table-data>
                    <span
                        v-bind:class="
                            amenity.status
                                ? 'text-green-800 bg-green-100'
                                : ' text-red-800 bg-red-100'
                        "
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    >
                        {{ amenity.status ? "Active" : "Inactive" }}
                    </span>
                </table-data>
                <table-data :title="DateTime.fromISO(amenity.created_at)" />
                <table-data
                    :title="DateTime.fromISO(amenity.updated_at).toRelative()"
                />
                <table-data>
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > amenities.data.length - 3"
                        :content-classes="['bg-white']"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <font-awesome-icon :icon="faCog" />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="route('partner.amenity.edit', amenity)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-24vw h-4 lg:h-24vw mr-0 md:mr-2"
                                />
                                Edit
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="handleUpdateForm(amenity)"
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
                                @click="confirmDeletion(amenity.id)"
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
                :links="amenities.links"
                :to="amenities.to"
                :from="amenities.from"
                :total="amenities.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Create new amenity Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new amenity </template>

        <template #content>
            <Form :form="form_class" :submitted="storeAmenity" modal />
        </template>
    </SideModal>

    <!-- Update studio Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update studio </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateAmenities" modal />
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
