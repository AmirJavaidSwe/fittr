<script setup>
import { ref, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
// import Section from '@/Components/Section.vue';
import SectionTitle from "@/Components/SectionTitle.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Pagination from "@/Components/Pagination.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import { DateTime } from "luxon";
import { faPencil, faChevronRight } from "@fortawesome/free-solid-svg-icons";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import Avatar from "@/Components/Avatar.vue";

const props = defineProps({
    users: Object,
});
const form = useForm({});
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
</script>
<template>
    <div class="flex items-center justify-between">
        <SectionTitle>
            <template #title>All Users</template>
        </SectionTitle>
        <ButtonLink
            styling="secondary"
            size="default"
            :href="route('partner.users.create')"
            class="ml-3"
            >Add new <font-awesome-icon class="ml-2" :icon="faPlus"
        /></ButtonLink>
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
            <table-head title="Is Super Admin" />
            <table-head title="Roles" />
            <table-head title="Avatar" />
            <table-head
                title="Date created"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head title="" />
        </template>

        <template #tableData>
            <tr v-for="user in props.users" :key="user.id">
                <table-data>{{ user.id }}</table-data>
                <table-data>{{ user.name }}</table-data>
                <table-data>{{ user.email }}</table-data>
                <table-data>
                    <div
                        class="ml-4 w-16 rounded my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 dark:bg-gray-700 dark:text-gray-300 text-center"
                    >
                        {{ user.is_super ? "Yes" : "No" }}
                    </div>
                </table-data>
                <table-data>
                    <template v-if="!user.is_super">
                        <template
                            v-for="(role, role_i) in user.user_roles"
                            :key="role_i"
                        >
                            <div
                                class="block my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300"
                            >
                                {{ role }}
                            </div>
                        </template>
                    </template>
                </table-data>
                <table-data>
                    <Avatar :title="user.name" size="small" />
                </table-data>
                <table-data>
                    <DateValue :date="DateTime.fromISO(user.created_at)" />
                </table-data>
                <table-data>
                    <div class="flex gap-4 justify-end">
                        <Link
                            class="flex items-center"
                            :href="route('partner.users.edit', user.id)"
                        >
                            <EditIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                            />
                            Edit
                        </Link>
                        <!-- <Link :href="role.url_show">
                        <font-awesome-icon :icon="faChevronRight" />
                        </Link> -->
                        <button
                            class="flex items-center text-danger-500"
                            v-if="$page.props.user.id != user.id"
                            @click="confirmDeletion(user.id)"
                        >
                            <DeleteIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                            />
                            Delete
                        </button>
                    </div>
                </table-data>
            </tr>
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
