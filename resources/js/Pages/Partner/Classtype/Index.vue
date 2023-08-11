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
import ButtonLink from "@/Components/ButtonLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "../../../Components/DataTable/DateValue.vue";
import ActionsIcon from '@/Icons/ActionsIcon.vue';
import CloseModal from "@/Components/CloseModal.vue";
import StatusLabel from "@/Components/StatusLabel.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    classtypes: Object,
    statuses: Array,
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
let form_new = useForm({
    status: false,
    title: null,
    description: null,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeItem = () => {
    form_new.post(route("partner.classtypes.store"), {
        preserveScroll: true,
        onSuccess: () => form_new.reset(),
    });
};

let form_edit = useForm({
    id: "",
    status: false,
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
    form_edit.status = data.status;
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
    <DataTableLayout :disable-search="disableSearch" :disableButton="true">
        <template #button>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal = true"
            >
                Create a new classtype
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.classtypes.create')"
                type="primary"
            >
                Create a new classtype (direct)
            </ButtonLink> -->
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
            <TableHead
                title="Status"
                @click="setOrdering('status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status'"
            />
            <TableHead
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <TableHead
                title="Description"
                @click="setOrdering('description')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'description'"
            />
            <TableHead
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            >
                <div>
                    Created
                    <span v-tooltip="DateTime.now().setZone(business_settings.timezone).toFormat('z')">
                        ({{ DateTime.now().setZone(business_settings.timezone).toFormat('ZZZZ')}})
                    </span>
                </div>
            </TableHead>
            <TableHead
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <TableHead title="Action" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="(classtype, index) in classtypes.data" :key="index">
                <table-data class="text-center">
                    <StatusLabel :status="props.statuses.find(el => el.value == classtype.status).label" />
                </table-data>
                <table-data>
                    <Link
                        class="font-semibold text-primary-500 hover:text-primary-900"
                        :href="route('partner.classtypes.show', classtype)"
                    >
                        {{ classtype.title }}
                    </Link>
                </table-data>
                <table-data :title="classtype.description" />
                <table-data>
                    <DateValue :date="DateTime.fromISO(classtype.created_at)
                    .setZone(business_settings.timezone)
                    .toFormat(business_settings.date_format.format_js + ' ' + business_settings.time_format.format_js)" />
                </table-data>
                <table-data>
                    <DateValue :date="DateTime.fromISO(classtype.updated_at).toRelative()"/>
                </table-data>
                <table-data class="text-right">
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > classtypes.data.length - 3"
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
                                @click="handleUpdateForm(classtype)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                <span> Edit </span>
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="confirmDeletion(classtype.id)"
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
                :links="classtypes.links"
                :to="classtypes.to"
                :from="classtypes.from"
                :total="classtypes.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>

    <!-- Create new classtype Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new classtype </template>

        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

        <template #content>
            <Form :form="form_new" :submitted="storeItem" modal :statuses="statuses" />
        </template>
    </SideModal>

    <!-- Update classtype Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update classtype </template>

        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateItem" modal :statuses="statuses" />
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
