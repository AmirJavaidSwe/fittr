<script setup>
import { watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
// import Section from '@/Components/Section.vue';
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import {
    faUser,
    faPlus,
    faCog,
    faEye,
} from "@fortawesome/free-solid-svg-icons";
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

const props = defineProps({
    users: Object,
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

const setOrdering = (col) => {
    //reverse same col order
    if (form.order_by == col) {
        form.order_dir = form.order_dir == "asc" ? "desc" : "asc";
    }
    form.order_by = col;
    runSearch();
};

const runSearch = () => {
    form.get(route("admin.partners.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);
</script>

<template>
    <data-table-layout :disableButton="true">
        <template #search>
            <Search v-model="form.search" @reset="form.search = null" />
        </template>

        <template #button>
            <ButtonLink
                :href="route('admin.partners.index')"
                type="primary"
                styling="secondary"
                size="default"
                >Add new <font-awesome-icon class="ml-2" :icon="faPlus"
            /></ButtonLink>
        </template>

        <template #tableHead>
            <table-head
                title="ID"
                @click="setOrdering('id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'id'"
            />
            <table-head
                title="BID"
                @click="setOrdering('business_id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'business_id'"
            />
            <table-head title="Business" />
            <table-head
                title="Name"
                @click="setOrdering('name')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'name'"
            />
            <table-head
                title="Email"
                @click="setOrdering('email')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'email'"
            />
            <table-head
                title="Date created"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head title="" />
        </template>

        <template #tableData>
            <tr v-for="(user, index) in props.users.data" :key="user.id">
                <table-data>{{ user.id }}</table-data>
                <table-data>{{ user.business_id }}</table-data>
                <table-data>{{ user.business_name }}</table-data>
                <table-data> <AvatarValue :title="user.name" /></table-data>
                <table-data>{{ user.email }}</table-data>
                <table-data>
                    <DateValue :date="user.created_at" />
                </table-data>
                <table-data>
                    <div class="flex items-center">
                        <Link
                            :href="user.url_login_as"
                            v-can="{
                                module: 'partner-management',
                                roles: $page.props.user.user_roles,
                                permission: 'loginAs',
                                user: $page.props.user,
                            }"
                            class="mr-2"
                        >
                            <font-awesome-icon class="mr-2" :icon="faUser" />
                        </Link>
                        <Dropdown
                            align="right"
                            width="48"
                            :top="index > props.users.data.length - 3"
                            :content-classes="['bg-white']"
                        >
                            <template #trigger>
                                <button class="text-dark text-lg">
                                    <font-awesome-icon :icon="faCog" />
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink
                                    :href="user.url_edit"
                                    v-can="{
                                        module: 'partner-management',
                                        roles: $page.props.user.user_roles,
                                        permission: 'update',
                                        user: $page.props.user,
                                    }"
                                >
                                    <EditIcon
                                        class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                    />
                                    Edit
                                </DropdownLink>
                                <DropdownLink
                                    :href="user.url_show"
                                    v-can="{
                                        module: 'partner-management',
                                        roles: $page.props.user.user_roles,
                                        permission: 'view',
                                        user: $page.props.user,
                                    }"
                                >
                                    <font-awesome-icon
                                        class="mr-2"
                                        :icon="faEye"
                                    />
                                    View
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </table-data>
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
</template>
