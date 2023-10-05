<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
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
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faUserLock, faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import { Dialog } from "@headlessui/vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import { hideAllPoppers } from 'floating-vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    instructors: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
});

const form_search = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const runSearch = () => {
    form_search.get(route("partner.instructors.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const setOrdering = (col) => {
    //reverse same col order
    if (form_search.order_by == col) {
        form_search.order_dir = form_search.order_dir == "asc" ? "desc" : "asc";
    }
    form_search.order_by = col;
    runSearch();
};

const setPerPage = (n) => {
    form_search.per_page = n;
    runSearch();
};

// form.search getter only;
watch(() => form_search.search, runSearch);

// Create/Edit Member Queries
const form_item = useForm({
    id: null,
    first_name: null,
    last_name: null,
    email: null,
    phone: null,
    profile_description: null,
    profile_image: null,
    old_profile_image: false,
});

const showCreateModal = ref(false);
const handleCreateForm = () => {
    form_item.reset();
    showCreateModal.value = true;
};
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeInstructor = () => {
    form_item.post(route("partner.instructors.store"), {
        preserveScroll: true,
        onSuccess: () => [form_item.reset(), closeCreateModal()],
    });
};

const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (data) => {
    hideAllPoppers();
    showEditModal.value = true;
    form_item.id = data.id;
    form_item.first_name = data.first_name;
    form_item.last_name = data.last_name;
    form_item.email = data.email;
    form_item.phone = data.phone;
    form_item.profile_description = data.profile?.description;
    form_item.profile_image = data.profile?.images?.length ? { ...data.profile?.images[0] } : null;
    form_item.old_profile_image = !!form_item.profile_image;
};

const updateInstructors = () => {
    form_item
        .transform((data) => ({
            ...data,
            old_profile_image: (data.profile_image instanceof File) === false && !!form_item.profile_image?.filename,
            _method: "put",
        }))
        .post(route("partner.instructors.update", form_item.id), {
            preserveScroll: true,
            onSuccess: () => [form_item.reset(), closeEditModal()],
        });
};

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    hideAllPoppers();
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form_item.delete(
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
    <DataTableLayout :disable-search="disableSearch" :disableButton="true">
        <template #button>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="handleCreateForm"
            >
                Create new instructor
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
                v-model="form_search.search"
                :disable-search="disableSearch"
                @reset="form_search.search = null"
                noFilter
            />
        </template>

        <template #tableHead>
            <TableHead
                title="First Name"
                @click="setOrdering('first_name')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'first_name'"
            />
            <TableHead
                title="Last Name"
                @click="setOrdering('last_name')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'last_name'"
            />
            <TableHead
                title="Email"
                @click="setOrdering('email')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'email'"
            />
            <TableHead
                title="Phone"
                @click="setOrdering('phone')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'phone'"
            />
            <TableHead
                title="Created"
                @click="setOrdering('created_at')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'created_at'"
            />
            <TableHead
                title="Updated"
                @click="setOrdering('updated_at')"
                :arrowSide="form_search.order_dir"
                :currentSort="form_search.order_by === 'updated_at'"
            />
            <TableHead title="Action" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="(instructor, index) in instructors.data" :key="index">
                <TableData>
                    {{ instructor.first_name }}
                </TableData>
                <TableData>
                    {{ instructor.last_name }}
                </TableData>
                <TableData>
                    <ButtonLink :href="route('partner.instructors.show', instructor)">
                        {{ instructor.email }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    {{ instructor.phone }}
                </TableData>
                <TableData>
                    <!-- <DateValue :date="DateTime.fromISO(instructor.created_at).toLocaleString()"/> -->
                    <DateValue :date="DateTime.fromISO(instructor.created_at)
                    .setZone(business_settings.timezone)
                    .toFormat(business_settings.date_format.format_js + ' ' + business_settings.time_format.format_js)" />
                </TableData>
                <TableData>
                    <DateValue
                        :date="DateTime.fromISO(instructor.updated_at).toRelative()"
                    />
                </TableData>
                <TableData class="text-right">
                    <VDropdown placement="bottom-end">
                        <button><ActionsIcon /></button>
                        <template #popper>
                            <div class="p-2 w-40 space-y-4">
                                <ButtonLink
                                    styling="blank"
                                    size="small"
                                    class="w-full flex justify-between hover:bg-gray-100"
                                    @click="handleUpdateForm(instructor)"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="blank"
                                    size="small"
                                    class="w-full flex justify-between hover:bg-gray-100"
                                    :href="route('partner.login-as')"
                                    :data="{ id: instructor.id }"
                                    method="post"
                                    type="button"
                                    as="button"
                                    >
                                    <font-awesome-icon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" :icon="faUserLock" />
                                    <span> Login as </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(instructor.id)"
                                    >
                                    <DeleteIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Delete </span>
                                </ButtonLink>
                            </div>
                        </template>
                    </VDropdown>
                </TableData>
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
    </DataTableLayout>

    <!-- Create new instructor Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new instructor </template>

        <template #content>
            <Form :form="form_item" :submitted="storeInstructor" modal />
        </template>
    </SideModal>

    <!-- Update instructor Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update instructor </template>

        <template #content>
            <Form :form="form_item" :submitted="updateInstructors" modal />
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
                :class="{ 'opacity-25': form_item.processing }"
                :disabled="form_item.processing"
                @click="deleteItem"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>
</template>
