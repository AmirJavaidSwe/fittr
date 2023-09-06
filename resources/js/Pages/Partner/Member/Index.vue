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
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { faUserLock, faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import CloseModal from "@/Components/CloseModal.vue";
import { hideAllPoppers } from 'floating-vue';

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    members: Object,
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
    form.get(route("partner.members.index"), {
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

const storeMember = () => {
    form_class.post(route("partner.members.store"), {
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
    hideAllPoppers();
    showEditModal.value = true;
    form_edit.id = data.id;
    form_edit.name = data.name;
    form_edit.email = data.email;
};

const updateMember = () => {
    form_edit.put(route("partner.members.update", form_edit.id), {
        preserveScroll: true,
        onSuccess: () => [form_class.reset(), closeEditModal()],
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
        route("partner.members.destroy", { id: itemIdDeleting.value }),
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
                Create a new member
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.members.create')"
                type="primary"
            >
                Create a new member (direct)
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
                title="Name"
                @click="setOrdering('name')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'name'"
            />
            <TableHead
                title="Email"
                @click="setOrdering('email')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'email'"
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
            <tr v-for="(member, index) in members.data" :key="index">
                <TableData>
                    <ButtonLink :href="route('partner.members.show', member)">
                        {{ member.name }}
                    </ButtonLink>
                </TableData>
                <TableData :title="member.email" />
                <TableData>
                    <DateValue :date="DateTime.fromISO(member.created_at)
                    .setZone(business_settings.timezone)
                    .toFormat(business_settings.date_format.format_js + ' ' + business_settings.time_format.format_js)" />
                </TableData>
                <TableData>
                    <DateValue
                        :date="DateTime.fromISO(member.updated_at).toRelative()"
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
                                    @click="handleUpdateForm(member)"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="blank"
                                    size="small"
                                    class="w-full flex justify-between hover:bg-gray-100"
                                    :href="route('partner.login-as')"
                                    :data="{ id: member.id }"
                                    method="post"
                                    >
                                    <font-awesome-icon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" :icon="faUserLock" />
                                    <span> Login as </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(member.id)"
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
                :links="members.links"
                :to="members.to"
                :from="members.from"
                :total="members.total"
                @pp_changed="setPerPage"
            />
        </template>
    </DataTableLayout>

    <!-- Create new member Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new member </template>

        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

        <template #content>
            <Form :form="form_class" :submitted="storeMember" modal />
        </template>
    </SideModal>

    <!-- Update member Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Update member </template>

        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form :form="form_edit" :submitted="updateMember" modal />
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
