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
import Form from './Form.vue';
import SideModal from "@/Components/SideModal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import uniqBy from 'lodash/uniqBy';
import RoleCreateForm from "@/Pages/Roles/Form.vue";
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
    business_seetings: Object,
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

const showUserCreateModal = ref(false)
const closeUserCreateModal = () => {
    createUserFrom.reset()
    showUserCreateModal.value = false
}
const createUserFrom = useForm({
    name: "",
    email: "",
    password: "",
    is_super: true,
    roles: []
});

const rolesList = computed(() => {
    let newRolesList =  props.roles
    newRolesList.push({
        id: "create_new_role",
        title: "Add New"
    });
    return uniqBy(newRolesList, 'id');

});

const storeUser = () => {
    createUserFrom.transform((data) => ({
        ...data,
        roles: data.is_super ? [] : data.roles,
        is_super: data.is_super,
        is_new: 1,
    })).post(route("partner.users.store"), {
        preserveScroll: true,
        onSuccess: () => [createUserFrom.reset(), closeUserCreateModal()],
    });
};

const showRoleCreateModal = ref(false)
const closeRoleCreateModal = () => {
    createRoleFrom.reset()
    showRoleCreateModal.value = false
}
const createRoleFrom = useForm({
});

const storeRole = ($event) => {
    let title = null
    let permissions = []
    if($event.title) {
        title = $event.title
    }
    if($event.permissions) {
        permissions = $event.permissions
    }
    createRoleFrom.transform((data) => ({
        title: title,
        permissions: permissions,
        returnTo: 'partner.users.index',
    })).post(route(`${usePage().props.user.source}.roles.store`), {
        preserveScroll: true,
        onSuccess: () => [createRoleFrom.reset(), closeRoleCreateModal(), createUserFrom.roles = []],
    });
};
</script>
<template>
    <data-table-layout :disableButton="true">
        <template #search>
            <Search :noFilter="true" v-model="form.search" @reset="form.search = null" />
        </template>

        <template #button>
            <ButtonLink v-can="{
                module: 'users',
                roles: $page.props.user.user_roles,
                permission: 'create',
                user: $page.props.user,
            }" styling="secondary" size="default" @click="showUserCreateModal = true">
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
            <table-head title="ID" @click="setOrdering('id')" :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'id'" />
            <table-head title="Name" @click="setOrdering('name')" :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'name'" />
            <table-head title="Email" @click="setOrdering('email')" :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'email'" />
            <table-head title="Is Super Admin" />
            <table-head title="Roles" />
            <table-head title="Avatar" />
            <table-head title="Date created" @click="setOrdering('created_at')" :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'" />
            <table-head title="" />
        </template>

        <template #tableData>
            <tr v-for="(user, index) in props.users.data" :key="user.id">
                <table-data>{{ user.id }}</table-data>
                <table-data>{{ user.name }}</table-data>
                <table-data>{{ user.email }}</table-data>
                <table-data>
                    <div
                        class="ml-4 w-16 rounded my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 dark:bg-gray-700 dark:text-gray-300 text-center">
                        {{ user.is_super ? "Yes" : "No" }}
                    </div>
                </table-data>
                <table-data>
                    <template v-if="!user.is_super">
                        <template v-for="(role, role_i) in user.user_roles" :key="role_i">
                            <div
                                class="block my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                {{ role }}
                            </div>
                        </template>
                    </template>
                </table-data>
                <table-data>
                    <Avatar :title="user.name" size="small" />
                </table-data>
                <table-data>
                    <DateValue :date="DateTime.fromISO(user.created_at).toLocaleString()" />
                </table-data>
                <table-data>
                    <Dropdown align="right" width="48" :top="index > props.users.data.length - 3"
                        :content-classes="['bg-white']">
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <font-awesome-icon :icon="faCog" />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('partner.users.edit', user.id)" v-can="{
                                module: 'users',
                                roles: $page.props.user.user_roles,
                                permission: 'update',
                                user: $page.props.user,
                            }">
                                <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                Edit
                            </DropdownLink>
                            <DropdownLink v-if="$page.props.user.id != user.id" v-can="{
                                module: 'users',
                                roles: $page.props.user.user_roles,
                                permission: 'destroy',
                                user: $page.props.user,
                            }" as="button" @click="confirmDeletion(user.id)">
                                <span class="text-danger-500 flex items-center">
                                    <DeleteIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Delete </span>
                                </span>
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination :links="users.links" :to="users.to" :from="users.from" :total="users.total"
                @pp_changed="runSearch" />
        </template>
    </data-table-layout>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="itemDeleting" @close="itemDeleting = false">
        <template #title> Confirmation required </template>

        <template #content> Are you sure you would like to delete this? </template>

        <template #footer>
            <ButtonLink size="default" styling="default" @click="itemDeleting = null">
                Cancel
            </ButtonLink>

            <ButtonLink size="default" styling="danger" class="ml-3" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing" @click="deleteItem">
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <!-- GM Create Modal -->
    <SideModal :show="showUserCreateModal" @close="closeUserCreateModal">
        <template #title> Create new General Manager </template>
        <template #close>
            <CloseModal @click="closeUserCreateModal" />
        </template>

        <template #content>
            <Form :form="createUserFrom" :roles="rolesList" @create-new-role='showRoleCreateModal = true' :submitted="storeUser"
                modal />
        </template>
    </SideModal>

    <!-- Role Create Modal -->
    <SideModal :show="showRoleCreateModal" @close="closeRoleCreateModal">
        <template #title> Create new Role </template>
        <template #close>
            <CloseModal @click="closeRoleCreateModal" />
        </template>
    
        <template #content>
            <RoleCreateForm :form="createRoleFrom" :system-modules="systemModules" @submitted="storeRole" modal />
        </template>
    </SideModal>
</template>