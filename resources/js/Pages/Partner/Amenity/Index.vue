<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import SideModal from "@/Components/SideModal.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import StatusLabel from "../../../Components/StatusLabel.vue";
import DateValue from "../../../Components/DataTable/DateValue.vue";
import ActionsIcon from '@/Icons/ActionsIcon.vue';
import CloseModal from "@/Components/CloseModal.vue";
import { hideAllPoppers } from 'floating-vue';

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
    business_settings: Object,
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
    status: false,
});

const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
};

const storeAmenity = () => {
    form_class.post(route("partner.amenity.store"), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), showCreateModal.value = false]
    });
};

let form_edit = form_class;
const showEditModal = ref(false);
const closeEditModal = () => {
    showEditModal.value = false;
};

const handleUpdateForm = (data) => {
    hideAllPoppers();
    showEditModal.value = true;
    form_edit["id"] = data.id;
    form_edit["title"] = data.title;
    form_edit["status"] = data.status ? true : false;
};

const updateAmenities = () => {
    form_edit.put(route("partner.amenity.update", form_edit), {
        preserveScroll: true,
        onSuccess: () => [form_edit.reset(), showEditModal.value = false]
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
    <DataTableLayout :disableButton="true">
        <template #button>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal = true"
            >
                Create new
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
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
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <TableHead
                title="Status"
                @click="setOrdering('status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status'"
            />
            <TableHead
                title="Created At"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <TableHead
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <TableHead title="Action" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="(amenity, index) in amenities.data" :key="index">
                <TableData>
                    <ButtonLink :href="route('partner.amenity.show', amenity)">
                        {{ amenity.title }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    <StatusLabel
                        :status="amenity.status ? 'Active' : 'Inactive'"
                    />
                </TableData>
                <TableData>
                    <DateValue :date="DateTime.fromISO(amenity.created_at)
                                .setZone(business_settings.timezone)
                                .toFormat(business_settings.date_format?.format_js)" />
                </TableData>
                <TableData>
                    <DateValue
                        :date="DateTime.fromISO(amenity.updated_at).toRelative()"
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
                                    @click="handleUpdateForm(amenity)"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(amenity.id)"
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
                :links="amenities.links"
                :to="amenities.to"
                :from="amenities.from"
                :total="amenities.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>

    <!-- Create new amenity Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new amenity </template>

        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

        <template #content>
            <Form :form="form_class" :submitted="storeAmenity" modal />
        </template>
    </SideModal>

    <!-- Update amenity Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update amenity </template>

        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

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
