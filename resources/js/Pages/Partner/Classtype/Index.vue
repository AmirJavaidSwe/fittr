<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
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
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ActionsIcon from '@/Icons/ActionsIcon.vue';
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import DuplicateIcon from "@/Icons/Duplicate.vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import { hideAllPoppers } from 'floating-vue';

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
    form_new.reset().clearErrors();
};

const storeItem = () => {
    form_new.post(route("partner.classtypes.store"), {
        preserveScroll: true,
        onSuccess: () => closeCreateModal()
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
    form_edit.clearErrors();
};

const handleUpdateForm = (data) => {
    hideAllPoppers();
    showEditModal.value = true;
    form_edit.id = data.id;
    form_edit.status = data.status;
    form_edit.title = data.title;
    form_edit.description = data.description;
};

const updateItem = () => {
    form_edit.put(route("partner.classtypes.update", form_edit), {
        preserveScroll: true,
        onSuccess: () => closeEditModal()
    });
};

//duplication modal:
const showDuplicateModal = ref(false);
const handleDuplicateForm = (data) => {
    hideAllPoppers();
    showDuplicateModal.value = true;
    form_new.status = data.status;
    form_new.title = data.title;
    form_new.description = data.description;
};
const closeDuplicateModal = () => {
    showDuplicateModal.value = false;
    form_new.reset().clearErrors();
};
const storeDuplicate = () => {
    form_new.post(route("partner.classtypes.store"), {
        preserveScroll: true,
        onSuccess: () => closeDuplicateModal(),
    });
};

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    hideAllPoppers();
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
                Create new
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
                <TableData class="text-center">
                    <StatusLabel :status="props.statuses.find(el => el.value == classtype.status).label" />
                </TableData>
                <TableData>
                    <ButtonLink :href="route('partner.classtypes.show', classtype)">
                        <span
                            v-if="classtype.title.length > 25"
                            v-tooltip="classtype.title"
                        >
                            {{ classtype.title.substring(0, 25) }}...
                        </span>
                        <span v-else>
                            {{ classtype.title }}
                        </span>
                    </ButtonLink>
                </TableData>
                <TableData>
                    <span
                        v-if="classtype.description.length > 25"
                        v-tooltip="classtype.description"
                    >
                        {{ classtype.description.substring(0, 25) }}...
                    </span>
                    <span v-else>
                        {{ classtype.description }}
                    </span>
                </TableData>
                <TableData>
                    <DateValue :date="DateTime.fromISO(classtype.created_at)
                    .setZone(business_settings.timezone)
                    .toFormat(business_settings.date_format.format_js + ' ' + business_settings.time_format.format_js)" />
                </TableData>
                <TableData>
                    <DateValue :date="DateTime.fromISO(classtype.updated_at).toRelative()"/>
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
                                    @click="handleUpdateForm(classtype)"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="blank"
                                    size="small"
                                    class="w-full flex justify-between hover:bg-gray-100"
                                    @click="handleDuplicateForm(classtype)"
                                    >
                                    <DuplicateIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Duplicate </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(classtype.id)"
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

        <template #content>
            <Form :form="form_new" :submitted="storeItem" modal :statuses="statuses" />
        </template>
    </SideModal>

    <!-- Update classtype Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update class type </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateItem" modal :statuses="statuses" />
        </template>
    </SideModal>

    <!-- Duplicate Modal -->
    <SideModal :show="showDuplicateModal" @close="closeDuplicateModal">
        <template #title> Duplicate class type </template>

        <template #content>
            <Form
                :form="form_new"
                :submitted="storeDuplicate"
                :statuses="statuses" 
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
