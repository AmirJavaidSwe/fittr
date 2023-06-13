<script setup>
import { ref, watch, computed } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Form from "./Form.vue";
import FormExport from "./FormExport.vue";
import FormFilter from "./FormFilter.vue";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import SideModal from "@/Components/SideModal.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import ImportIcon from "@/Icons/Import.vue";
import ExportIcon from "@/Icons/Export.vue";
import EditIcon from "@/Icons/Edit.vue";
import DuplicateIcon from "@/Icons/Duplicate.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

import ButtonLink from "@/Components/ButtonLink.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import StatusLabel from "@/Components/StatusLabel.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    classes: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,

    business_seetings: Object,

    statuses: Object,
    studios: Object,
    instructors: Object,
    classtypes: Object,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_class = useForm({
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: null,
    class_type_id: null,
    studio_id: null,
    file_type: "csv",
    is_off_peak: false,
    does_repeat: false,
    repeat_end_date: null,
    week_days: [],
});

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

const runSearch = () => {
    form.get(route("partner.classes.index"), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};
const deleteItem = () => {
    form.delete(
        route("partner.classes.destroy", { id: itemIdDeleting.value }),
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

// Filter modal:
const showFilterModal = ref(false);
const closeFilterModal = () => {
    showFilterModal.value = false;
};

//create confirmation modal:
const showCreateModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    form_class.reset().clearErrors();
};

const storeClass = () => {
    form_class.post(route("partner.classes.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form_class.reset();
            closeCreateModal();
        },
    });
};

// Edit Class Query
const showEditModal = ref(false);
const editModalData = ref({});
const closeEditModal = () => {
    showEditModal.value = false;
};

let formEdit = useForm({
    id: null,
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: null,
    class_type_id: null,
    studio_id: null,
    is_off_peak: null,
});
const handleUpdateForm = (data) => {
    showEditModal.value = true;
    formEdit.id = data.id;
    formEdit.title = data.title;
    formEdit.status = data.status;
    formEdit.start_date = data.start_date;
    formEdit.end_date = data.end_date;
    formEdit.instructor_id = data.instructor_id;
    formEdit.class_type_id = data.class_type_id;
    formEdit.studio_id = data.studio_id;
    formEdit.is_off_peak = data.is_off_peak;
};

const updateClass = () => {
    formEdit.put(route("partner.classes.update", formEdit.id), {
        preserveScroll: true,
        onSuccess: () => (showEditModal.value = false),
    });
};

//export form modal:
const showExportModal = ref(false);
const exportInitiated = ref(false);
const export_id = ref(null);
const closeExportModal = () => {
    showExportModal.value = false;
};
const resetExportFilters = () => {
    form_class.reset();
};

const exportClasses = () => {
    form_class
        .transform((data) => ({
            type: "classes",
            filters: {
                status: data.status,
                start_date: data.start_date,
                end_date: data.end_date,
                instructor_id: data.instructor_id,
                studio_id: data.studio_id,
                is_off_peak: data.is_off_peak,
            },
            file_type: data.file_type,
        }))
        .post(route("partner.exports.index"), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (data) => {
                form_class.reset();
                exportInitiated.value = true;
                export_id.value = data.props.extra?.export_id;
                checkExportStatus();
            },
        });
};
const completed_at = ref(null);
const checkExportStatus = () => {
    setTimeout(() => {
        // idea is to do quick query here to check for export status. The job may have been complete and we can show a
        // download link inside modal, to avoid visit exports page

        axios
            .get(route("partner.exports.show", { id: export_id.value }), {
                headers: {
                    "X-Inertia": true,
                    "X-Inertia-Version": usePage().version,
                },
            })
            .then((response) => {
                // completed_at.value = null;
                showLink(response.data.props.exporting);
            });
    }, 5000);
};

const showLink = (exporting) => {
    completed_at.value = exporting.completed_at;
};
</script>
<template>
    <data-table-layout :disableButton="true">
        <template #button>
            <ButtonLink styling="default" size="default" @click="null">
                <ImportIcon class="w-4 h-4 2xl:w-6 2xl:h-6 mr-0 md:mr-2" />
                <span class="hidden md:block">Import</span>
            </ButtonLink>
            <ButtonLink
                styling="default"
                size="default"
                @click="showExportModal = true"
            >
                <ExportIcon class="w-4 h-4 2xl:w-6 2xl:h-6 mr-0 md:mr-2" />
                <span class="hidden md:block">Export</span>
            </ButtonLink>
            <ButtonLink
                styling="secondary"
                size="default"
                @click="showCreateModal = true"
            >
                Create new
                <font-awesome-icon class="ml-2" :icon="faPlus" />
            </ButtonLink>
            <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.classes.create')"
            >
                Create new (direct)
            </ButtonLink>
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @onFilter="showFilterModal = true"
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
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head
                title="Studio"
                @click="setOrdering('studio_id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'studio_id'"
            />
            <table-head
                title="Class Type"
                @click="setOrdering('class_type_id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'class_type_id'"
            />
            <table-head
                title="Instructor"
                @click="setOrdering('instructor_id')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'instructor_id'"
            />
            <table-head
                title="Status"
                @click="setOrdering('status_label')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status_label'"
            />
            <table-head
                title="Date"
                @click="setOrdering('start_date')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'start_date'"
            />
            <table-head
                title="Time"
                @click="setOrdering('start_date')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'start_date'"
            />
            <table-head
                title="Duration"
                @click="setOrdering('duration')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'duration'"
            />
            <table-head
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <table-head title="Action" />
        </template>

        <template #tableData>
            <tr
                v-for="(class_lesson, index) in classes.data"
                :key="class_lesson.id"
            >
                <table-data :title="class_lesson.id" />
                <table-data>
                    <Link
                        class="font-medium text-indigo-600 hover:text-indigo-500"
                        :href="route('partner.classes.show', class_lesson)"
                    >
                        {{ class_lesson.title }}
                    </Link>
                </table-data>
                <table-data
                    :title="
                        class_lesson?.studio?.title ?? class_lesson?.studio?.id
                    "
                />
                <!-- <table-data :title="class_lesson.class_type_id" /> -->
                <table-data>
                    <ColoredValue
                        color="#F47560"
                        :title="class_lesson?.classType?.title ?? 'Test'"
                    />
                </table-data>
                <table-data>
                    <AvatarValue
                        :title="class_lesson?.instructor?.name ?? 'Demo Ins'"
                    />
                </table-data>
                <table-data>
                    <StatusLabel :status="class_lesson.status_label" />
                </table-data>
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(class_lesson.start_date)
                                .setZone(business_seetings.timezone)
                                .toFormat(
                                    business_seetings.date_format?.format_js
                                )
                        "
                    />
                </table-data>
                <table-data>
                    <DateValue
                        isTime
                        :date="
                            DateTime.fromISO(class_lesson.start_date)
                                .setZone(business_seetings.timezone)
                                .toFormat(
                                    business_seetings.time_format?.format_js
                                )
                        "
                    />
                </table-data>

                <table-data :title="class_lesson.duration + ' minutes'" />
                <table-data
                    :title="
                        DateTime.fromISO(class_lesson.updated_at).toRelative()
                    "
                />
                <table-data>
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > classes.data.length - 3"
                        :content-classes="['bg-white']"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <font-awesome-icon :icon="faCog" />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="
                                    route('partner.classes.edit', class_lesson)
                                "
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                Edit
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="handleUpdateForm(class_lesson)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                Edit (Modal)
                            </DropdownLink>
                            <DropdownLink href="#">
                                <DuplicateIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                Duplicate
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="confirmDeletion(class_lesson.id)"
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
                </table-data>
            </tr>
        </template>
        <template #pagination>
            <Pagination
                :links="classes.links"
                :to="classes.to"
                :from="classes.from"
                :total="classes.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Export form Modal -->
    <SideModal :show="showFilterModal" @close="closeFilterModal">
        <template #title>Filter</template>
        <template #content>
            <FormFilter
                :form="form_class"
                :initiated="exportInitiated"
                :ready="completed_at"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                @submitted="exportClasses"
                @reset="resetExportFilters"
            />
        </template>
    </SideModal>

    <!-- Export form Modal -->
    <SideModal :show="showExportModal" @close="closeExportModal">
        <template #title> Export Classes </template>

        <template #content>
            <FormExport
                :form="form_class"
                :initiated="exportInitiated"
                :ready="completed_at"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                modal
                @submitted="exportClasses"
                @reset="resetExportFilters"
            />
        </template>
    </SideModal>

    <!-- Create new class Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Create new Class </template>

        <template #content>
            <Form
                :form="form_class"
                :isNew="true"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                :classtypes="classtypes"
                :business_seetings="business_seetings"
                :submitted="storeClass"
                modal
            />
        </template>
    </SideModal>

    <!-- Edit class Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Edit Class </template>

        <template #content>
            <Form
                :form="formEdit"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                :classtypes="classtypes"
                :business_seetings="business_seetings"
                :submitted="updateClass"
                modal
            />
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
