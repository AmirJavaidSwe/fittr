<script setup>
import { ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import { DateTime } from "luxon";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import {
    faPencil,
    faChevronRight,
    faPlus,
    faEye,
    faCog,
} from "@fortawesome/free-solid-svg-icons";
import DateValue from "../../Components/DataTable/DateValue.vue";
import { hideAllPoppers } from 'floating-vue';

const props = defineProps({
    roles: Object,
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

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (slug) => {
    hideAllPoppers();
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
    <DataTableLayout :disableButton="true">
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
                    module: 'roles',
                    roles: $page.props.user.user_roles,
                    permission: 'create',
                    user: $page.props.user,
                }"
                :href="route(`${$page.props.user.source}.roles.create`)"
                type="primary"
                styling="secondary"
                size="default"
                >Add new <font-awesome-icon class="ml-2" :icon="faPlus"
            /></ButtonLink>
        </template>

        <template #tableHead>
            <TableHead
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <TableHead
                title="Date created"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <TableHead title="Actions" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr v-for="(role, index) in props.roles.data" :key="role.id">
                <TableData>
                     <ButtonLink
                        :href="role.url_show"
                        v-can="{
                            module: 'roles',
                            roles: $page.props.user.user_roles,
                            permission: 'view',
                            user: $page.props.user,
                        }">
                        {{ role.title }}
                    </ButtonLink>
                </TableData>
                <TableData>
                    <div v-if="business_settings">
                        <DateValue
                            :date="DateTime.fromISO(role.created_at)
                                    .setZone(business_settings.timezone)
                                    .toFormat(business_settings.date_format.format_js + ' ' +
                                            business_settings.time_format?.format_js)"
                        />
                    </div>
                    <div v-else>
                        <DateValue
                            :date="DateTime.fromISO(role.created_at).toLocaleString(DateTime.DATETIME_HUGE)"
                        />
                    </div>
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
                                    :href="role.url_edit"
                                    v-can="{
                                        module: 'roles',
                                        roles: $page.props.user.user_roles,
                                        permission: 'update',
                                        user: $page.props.user,
                                    }"
                                    >
                                    <EditIcon class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2" />
                                    <span> Edit </span>
                                </ButtonLink>
                                <ButtonLink
                                    styling="transparent"
                                    size="small"
                                    class="w-full flex justify-between text-danger-500 hover:text-danger-700 hover:bg-gray-100"
                                    @click="confirmDeletion(role.slug)"
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
                :links="roles.links"
                :to="roles.to"
                :from="roles.from"
                :total="roles.total"
                @pp_changed="runSearch"
            />
        </template>
    </DataTableLayout>

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
