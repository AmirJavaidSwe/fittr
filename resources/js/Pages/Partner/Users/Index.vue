<script setup>
import { ref, watch, computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
// import Section from '@/Components/Section.vue';
import SectionTitle from "@/Components/SectionTitle.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Pagination from "@/Components/Pagination.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { DateTime } from "luxon";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import Avatar from "@/Components/Avatar.vue";
import Search from "@/Components/DataTable/Search.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Form from "./Form.vue";
import EditForm from "./EditForm.vue";
import SideModal from "@/Components/SideModal.vue";
import uniqBy from "lodash/uniqBy";
import RoleCreateForm from "@/Pages/Roles/Form.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import {
    faPencil,
    faChevronRight,
    faPlus,
    faEye,
    faCog,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    users: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
    roles: Array,
    systemModules: Array,
    business_settings: Object,
});
const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});
//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route("partner.users.destroy", { id: itemIdDeleting.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            itemDeleting.value = false;
            itemIdDeleting.value = null;
        },
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

const runSearch = () => {
    form.get(route(`partner.users.index`), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);

const showUserCreateModal = ref(false);
const closeUserCreateModal = () => {
    createUserFrom.reset().clearErrors();
    showUserCreateModal.value = false;
};
const createUserFrom = useForm({
    first_name: null,
    last_name: null,
    email: null,
    // password: "",
    is_super: true,
    roles: [],
});
const showUserEditModal = ref(false);
const closeUserEditModal = () => {
    editUserFrom.reset().clearErrors();
    showUserEditModal.value = false;
};
const editUserFrom = useForm({
    first_name: null,
    last_name: null,
    id: null,
    email: null,
    password: null,
    is_super: true,
    roles: []
});


const updateUser = () => {
    editUserFrom.transform((data) => ({
        ...data,
        roles: data.is_super ? [] : data.roles,
        is_super: data.is_super,
    })).put(route("partner.users.update", editUserFrom.id), {
        preserveScroll: true,
        onSuccess: () => closeUserEditModal(),
    });
};

const getUserRoles = (roles) => {
    const rolesArray = Object.keys(roles);
    const userRoles = [];
    for (let i = 0; i < props.roles.length; i++) {
        if(rolesArray.includes(props.roles[i].slug)) {
            userRoles.push(props.roles[i].id);
        }
    }
    return userRoles
};
const handleUpdateForm = (data) => {
    showUserEditModal.value = true;
    editUserFrom.id = data.id;
    editUserFrom.first_name = data.first_name;
    editUserFrom.last_name = data.last_name;
    editUserFrom.email = data.email;
    editUserFrom.password = null;
    editUserFrom.is_super = data.is_super;
    editUserFrom.roles = getUserRoles(data.user_roles);
};

const rolesList = computed(() => {
    let newRolesList = props.roles;
    newRolesList.push({
        id: "create_new_role",
        title: "Add New",
    });
    return uniqBy(newRolesList, "id");
});

const storeUser = () => {
    createUserFrom
        .transform((data) => ({
            ...data,
            roles: data.is_super ? [] : data.roles,
            is_super: data.is_super,
            is_new: 1,
        }))
        .post(route("partner.users.store"), {
            preserveScroll: true,
            onSuccess: () => closeUserCreateModal(),
        });
};

const showRoleCreateModal = ref(false);
const closeRoleCreateModal = () => {
    createRoleFrom.reset();
    showRoleCreateModal.value = false;
    let filtered = createUserFrom.roles.filter(
        (item) => item != "create_new_role"
    );
    createUserFrom.roles = filtered;
    filtered = editUserFrom.roles.filter(
        (item) => item != "create_new_role"
    );
    createUserFrom.roles = filtered;
};
const createRoleFrom = useForm({});

const storeRole = ($event) => {
    let title = null;
    let permissions = [];
    if ($event.title) {
        title = $event.title;
    }
    if ($event.permissions) {
        permissions = $event.permissions;
    }
    createRoleFrom
        .transform((data) => ({
            title: title,
            permissions: permissions,
            returnTo: "partner.users.index",
        }))
        .post(route(`${usePage().props.user.source}.roles.store`), {
            preserveScroll: true,
            onSuccess: () => [
                createRoleFrom.reset(),
                closeRoleCreateModal(),
                (createUserFrom.roles = []),
            ],
        });
};
</script>
<template>
    <data-table-layout :disableButton="true">
        <template #search>
            <Search
                :noFilter="true"
                v-model="form.search"
                @reset="form.search = null"
            />
        </template>

        <template #button>
            <ButtonLink
                v-can="{
                    module: 'users',
                    roles: $page.props.user.user_roles,
                    permission: 'create',
                    user: $page.props.user,
                }"
                styling="secondary"
                size="default"
                @click="showUserCreateModal = true"
            >
                Create new
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <!-- <ButtonLink v-can="{
                module: 'users',
                roles: $page.props.user.user_roles,
                permission: 'create',
                user: $page.props.user,
            }" :href="route(`partner.users.create`)" type="primary" styling="secondary" size="default">Add new
                <font-awesome-icon class="ml-2" :icon="faPlus" /></ButtonLink> -->
        </template>

        <template #tableHead>
            <TableHead
                title="First Name"
                @click="setOrdering('first_name')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'first_name'"
            />
            <TableHead
                title="Last Name"
                @click="setOrdering('last_name')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'last_name'"
            />
            <TableHead
                title="Email"
                @click="setOrdering('email')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'email'"
            />
            <TableHead title="Is Super Admin" />
            <TableHead title="Roles" />
            <TableHead title="Avatar" />
            <TableHead
                title="Date created"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <TableHead title="Actions" />
        </template>

        <template #tableData>
            <tr v-for="(user, index) in props.users.data" :key="user.id">
                <TableData>
                    {{ user.first_name }}
                </TableData>
                <TableData>
                    {{ user.last_name }}
                </TableData>
                <TableData>{{ user.email }}</TableData>
                <TableData class="items-center text-center">
                    <div class="block">
                        <StatusLabel :status="user.is_super ? 'Yes' : 'No'" />
                    </div>
                </TableData>
                <TableData class="items-center">
                    <template v-if="!user.is_super">
                        <template v-for="(role, role_i) in user.user_roles" :key="role_i">
                            <div class="block mb-2">
                                <StatusLabel :bg-color="'amber'" :status="role" />
                            </div>
                        </template>
                    </template>
                </TableData>
                <TableData class="text-center">
                    <Avatar
                        class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                        :initials="user.initials"
                        :imageUrl="user.profile_photo_url"
                        v-tooltip="user.full_name"
                    />
                </TableData>
                <TableData>
                    <DateValue
                        :date="
                            DateTime.fromISO(user.created_at).toLocaleString()
                        "
                    />
                </TableData>
                <TableData>
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > props.users.data.length - 3"
                        :content-classes="['bg-white']"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <ActionsIcon />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                v-can="{
                                    module: 'users',
                                    roles: $page.props.user.user_roles,
                                    permission: 'update',
                                    user: $page.props.user,
                                }"
                                as="button"
                                @click="handleUpdateForm(user)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                <span> Edit </span>
                            </DropdownLink>
                            <DropdownLink
                                v-if="$page.props.user.id != user.id"
                                v-can="{
                                    module: 'users',
                                    roles: $page.props.user.user_roles,
                                    permission: 'destroy',
                                    user: $page.props.user,
                                }"
                                as="button"
                                @click="confirmDeletion(user.id)"
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
                </TableData>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="users.links"
                :to="users.to"
                :from="users.from"
                :total="users.total"
                @pp_changed="runSearch"
            />
        </template>
    </data-table-layout>

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

    <!-- GM Create Modal -->
    <SideModal :show="showUserCreateModal" @close="closeUserCreateModal">
        <template #title> Create new User </template>

        <template #content>
            <Form
                :form="createUserFrom"
                :roles="rolesList"
                @create-new-role="showRoleCreateModal = true"
                :submitted="storeUser"
                modal
            />
        </template>
    </SideModal>
    <!-- GM Create Modal -->
    <SideModal :show="showUserEditModal" @close="closeUserEditModal">
        <template #title> Edit User </template>

        <template #content>
            <EditForm
                :form="editUserFrom"
                :roles="rolesList"
                @create-new-role="showRoleCreateModal = true"
                :submitted="updateUser"
                modal
            />
        </template>
    </SideModal>

    <!-- Role Create Modal -->
    <SideModal :show="showRoleCreateModal" @close="closeRoleCreateModal">
        <template #title> Create new Role </template>

        <template #content>
            <RoleCreateForm
                :form="createRoleFrom"
                :system-modules="systemModules"
                @submitted="storeRole"
                modal
            />
        </template>
    </SideModal>
</template>
