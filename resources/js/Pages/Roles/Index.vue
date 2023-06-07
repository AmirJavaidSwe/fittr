<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import WarningButton from "@/Components/WarningButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { DateTime } from "luxon";
import {
    faPencil,
    faChevronRight,
    faPlus,
} from "@fortawesome/free-solid-svg-icons";
const props = defineProps({
    roles: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
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
const confirmDeletion = (slug) => {
    itemIdDeleting.value = slug;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(route("partner.roles.destroy", { id: itemIdDeleting.value }), {
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
    form.get(route(`${usePage().props.user.source}.roles.index`), {
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
            <template #title> All Roles </template>
        </SectionTitle>
        <Search v-model="form.search" @reset="form.search = null" />
        <WarningButton
            v-can="{
                module: 'roles',
                roles: $page.props.user.user_roles,
                permission: 'create',
                user: $page.props.user,
            }"
            :href="route(`${$page.props.user.source}.roles.create`)"
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
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
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
            <tr v-for="role in props.roles.data" :key="role.id">
                <table-data>{{ role.id }}</table-data>
                <table-data>{{ role.title }}</table-data>
                <table-data>
                    <div v-if="business_seetings">
                        {{
                            DateTime.fromISO(role.created_at)
                                .setZone(business_seetings.timezone)
                                .toFormat(
                                    business_seetings.date_format.format_js +
                                        " " +
                                        business_seetings.time_format?.format_js
                                )
                        }}
                    </div>
                    <div v-else>
                        {{
                            DateTime.fromISO(role.created_at).toLocaleString(
                                DateTime.DATETIME_HUGE
                            )
                        }}
                    </div>
                </table-data>
                <table-data>
                    <div class="flex gap-4 justify-end">
                        <Link
                            :href="role.url_edit"
                            v-can="{
                                module: 'roles',
                                roles: $page.props.user.user_roles,
                                permission: 'update',
                                user: $page.props.user,
                            }"
                        >
                            <font-awesome-icon :icon="faPencil" />
                        </Link>
                        <Link
                            :href="role.url_show"
                            v-can="{
                                module: 'roles',
                                roles: $page.props.user.user_roles,
                                permission: 'view',
                                user: $page.props.user,
                            }"
                        >
                            <font-awesome-icon :icon="faChevronRight" />
                        </Link>
                        <button
                            class="block text-red-500"
                            @click="confirmDeletion(role.slug)"
                        >
                            Delete
                        </button>
                    </div>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="roles.links"
                :to="roles.to"
                :from="roles.from"
                :total="roles.total"
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
