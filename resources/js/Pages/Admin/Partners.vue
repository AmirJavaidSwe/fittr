<script setup>
import { watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
// import Section from '@/Components/Section.vue';
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import WarningButton from "@/Components/WarningButton.vue";
import {
    faPencil,
    faChevronRight,
    faUser,
    faPlus,
} from "@fortawesome/free-solid-svg-icons";

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
    <div class="flex items-center justify-between">
        <SectionTitle>
            <template #title>All partners</template>
        </SectionTitle>
        <Search v-model="form.search" @reset="form.search = null" />
        <WarningButton
            v-can="{
                module: 'partner-management',
                roles: $page.props.user.user_roles,
                permission: 'create',
                user: $page.props.user,
            }"
            :href="route('admin.partners.index')"
            type="primary"
            class="ml-3"
            >Add new <font-awesome-icon class="ml-2" :icon="faPlus"
        /></WarningButton>
    </div>

    <data-table-layout :disableButton="true">
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
            <tr v-for="user in props.users.data" :key="user.id">
                <table-data>{{ user.id }}</table-data>
                <table-data>{{ user.business_id }}</table-data>
                <table-data>{{ user.business_name }}</table-data>
                <table-data>{{ user.name }}</table-data>
                <table-data>{{ user.email }}</table-data>
                <table-data>{{ user.created_at }}</table-data>
                <table-data>
                    <div class="flex gap-4 justify-end">
                        <Link
                            title="Logged in as partner"
                            :href="user.url_login_as"
                            v-can="{
                                module: 'partner-management',
                                roles: $page.props.user.user_roles,
                                permission: 'loginAs',
                                user: $page.props.user,
                            }"
                        >
                            <font-awesome-icon :icon="faUser" />
                        </Link>
                        <Link
                            :href="user.url_edit"
                            v-can="{
                                module: 'partner-management',
                                roles: $page.props.user.user_roles,
                                permission: 'update',
                                user: $page.props.user,
                            }"
                        >
                            <font-awesome-icon :icon="faPencil" />
                        </Link>
                        <Link
                            :href="user.url_show"
                            v-can="{
                                module: 'partner-management',
                                roles: $page.props.user.user_roles,
                                permission: 'view',
                                user: $page.props.user,
                            }"
                        >
                            <font-awesome-icon :icon="faChevronRight" />
                        </Link>
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
