<script setup>
import { ref, watch } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import TopMenu from "./TopMenu.vue";
import { DateTime } from "luxon";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    exportings: Object,
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
    form.get(route("partner.exports.index"), {
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

//delete confiramtion modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(
        route("partner.exports.destroy", { id: itemIdDeleting.value }),
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

const bytesToKibibytes = (bytes) => {
    return Math.floor(bytes / 1024) + " KB";
};

const requestExport = (exporting) => {
    axios
        .get(route("partner.exports.request-to-download", { id: exporting }))
        .then((response) => {
            window.location.replace(response.data.url);
        })
        .catch((error) => {
            console.log(error);
        });
};
</script>

<template>
    <TopMenu />

    <data-table-layout
        button-title="Create new"
        button-link="#"
        :disable-search="disableSearch"
    >
        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                :noFilter="true"
                @reset="form.search = null"
                @pp_changed="setPerPage"
            />
        </template>

        <template #tableHead>
            <table-head
                title="Id"
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
                title="Status"
                @click="setOrdering('status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status'"
            />
            <table-head
                title="Type"
                @click="setOrdering('type')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'type'"
            />
            <table-head
                title="Size"
                @click="setOrdering('file_size')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'file_size'"
            />
            <table-head
                title="Created At"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head
                title="Updated By"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <table-head title="Action" />
        </template>

        <template #tableData>
            <tr v-for="exporting in exportings.data">
                <table-data :title="exporting.id" />

                <table-data>
                    <template v-if="exporting.status === 'completed'">
                        <Link
                            class="text-indigo-600 hover:text-indigo-500 via-indigo-950"
                            :href="route('partner.exports.show', exporting)"
                        >
                            {{ exporting.file_name }}
                        </Link>
                    </template>
                    <template v-else>
                        {{ exporting.file_name }}
                    </template>
                </table-data>

                <table-data>
                    <ColoredValue color="#F47560" :title="exporting.status" />
                </table-data>
                <table-data>
                    <ColoredValue color="#334455" :title="exporting.type" />
                </table-data>
                <table-data :title="bytesToKibibytes(exporting.file_size)" />
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(exporting.created_at).toRelative()
                        "
                    />
                </table-data>

                <table-data>
                    <template v-if="exporting.created_by">
                        {{
                            $page.props.user.id === exporting.created_by
                                ? "You"
                                : exporting.user.name
                        }}
                        <!-- <Link
                            class="text-indigo-600 hover:text-indigo-500 via-indigo-950"
                            :href="route('partner.members.show', exporting.created_by)">
                            {{ 'ID#'+exporting.user.id+': ' +exporting.user.name }}
                        </Link> -->
                    </template>
                </table-data>

                <table-data>
                    <ButtonLink
                        v-if="exporting.status === 'completed'"
                        size="default"
                        styling="secondary"
                        @click.prevent="requestExport(exporting.id)"
                    >
                        Download
                    </ButtonLink>
                    <!--                    <Link class="font-medium text-white hover:text-white bg-indigo-600 hover:bg-indigo-700 rounded py-2 px-4 inline-block mr-2"-->
                    <!--                          :href="route('partner.exports.show', exporting)">-->
                    <!--                        View-->
                    <!--                    </Link>-->
                    <ButtonLink
                        size="default"
                        styling="danger"
                        @click="confirmDeletion(exporting.id)"
                    >
                        Delete
                    </ButtonLink>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="exportings.links"
                :to="exportings.to"
                :from="exportings.from"
                :total="exportings.total"
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
</template>
