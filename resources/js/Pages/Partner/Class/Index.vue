use Class;
<script setup>
import { ref, watch, computed } from "vue";
import { Link, useForm, usePage, router } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import uniqBy from "lodash/uniqBy";
import map from "lodash/map";
import uniq from "lodash/uniq";
import find from "lodash/find";
import Form from "./Form.vue";
import FormExport from "./FormExport.vue";
import FormFilter from "./FormFilter.vue";
import BulkEditForm from "./BulkEditForm.vue";
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
import InstructorCreateForm from "@/Pages/Partner/Instructor/Form.vue";
import ClassTypeCreateForm from "@/Pages/Partner/Classtype/Form.vue";
import StudioCreateForm from "@/Pages/Partner/Studio/Form.vue";
import LocationCreateForm from "@/Pages/Partner/Location/Form.vue";
import GMCreateForm from "@/Pages/Partner/Users/Form.vue";
import RoleCreateForm from "@/Pages/Roles/Form.vue";
import AmenityCreateForm from "@/Pages/Partner/Amenity/Form.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import CloseModal from "@/Components/CloseModal.vue";
import { faCog, faPlus } from "@fortawesome/free-solid-svg-icons";
import StatusLabel from "@/Components/StatusLabel.vue";
import ColoredValue from "@/Components/DataTable/ColoredValue.vue";
import AvatarValue from "@/Components/DataTable/AvatarValue.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";
import OnTheFlyResourceCreate from "@/Components/OnTheFlyResourceCreate.vue";

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
    locations: Array,
    users: Array,
    amenities: Array,
    countries: Array,
    roles: Array,
    systemModules: Array,
});

const form = useForm({
    search: props.search,
    status: [],
    start_date: null,
    end_date: null,
    instructor_id: [],
    class_type_id: [],
    studio_id: [],
    is_off_peak: "",
    runFilter: false,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const form_class = useForm({
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: [],
    class_type_id: null,
    studio_id: null,
    file_type: "csv",
    is_off_peak: false,
    does_repeat: false,
    repeat_end_date: null,
    week_days: [],
});
const duplicateClassForm = useForm({
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: [],
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
watch(() => form.isDirty, runSearch);

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
const openFilterModal = () => {
    form.reset().clearErrors();
    form.runFilter = true;
    showFilterModal.value = true;
};
const closeFilterModal = () => {
    showFilterModal.value = false;
};
const resetClassFilters = () => {
    form.runFilter = false;
    form.reset().clearErrors();
    router.visit(route("partner.classes.index"));
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
            form_class.reset().clearErrors();
            closeCreateModal();
        },
    });
};
const storeDuplicateClass = () => {
    duplicateClassForm.post(route("partner.classes.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showDuplicateClassModal.value = false;
            duplicateClassForm.reset().clearErrors();
        },
    });
};

// Edit Class Query
const showEditModal = ref(false);
const showDuplicateClassModal = ref(false);
const editModalData = ref({});
const closeEditModal = () => {
    showEditModal.value = false;
};
const closeDuplicateClassModal = () => {
    showDuplicateClassModal.value = false;
    duplicateClassForm.reset().clearErrors();
};

let formEdit = useForm({
    id: null,
    title: null,
    status: null,
    start_date: null,
    end_date: null,
    instructor_id: [],
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
    formEdit.instructor_id = map(data.instructor, "id");
    formEdit.class_type_id = data.class_type_id;
    formEdit.studio_id = data.studio_id;
    formEdit.is_off_peak = data.is_off_peak;
};
const handleDuplicateForm = (data) => {
    showDuplicateClassModal.value = true;
    duplicateClassForm.title = data.title;
    duplicateClassForm.status = "inactive";
    duplicateClassForm.start_date = data.start_date;
    duplicateClassForm.end_date = data.end_date;
    duplicateClassForm.instructor_id = map(data.instructor, "id");
    duplicateClassForm.class_type_id = data.class_type_id;
    duplicateClassForm.studio_id = data.studio_id;
    duplicateClassForm.is_off_peak = data.is_off_peak;
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
    form_class.reset().clearErrors();
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
                form_class.reset().clearErrors();
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

const showInstructorCreateForm = ref(false);

const closeInstructorCreateForm = () => {
    showInstructorCreateForm.value = false
    let filtered = form_class.instructor_id.filter((item) => item != 'create_new_instructor')
    form_class.instructor_id = filtered
    filtered = formEdit.instructor_id.filter((item) => item != 'create_new_instructor')
    formEdit.instructor_id = filtered
    filtered = formBulkEdit.instructor_id.filter((item) => item != 'create_new_instructor')
    formBulkEdit.instructor_id = filtered
};

const showClassTypeCreateForm = ref(false);
const closeClassTypeCreateForm = () => {
    showClassTypeCreateForm.value = false
    form_class.class_type_id = null
    formEdit.class_type_id = null
    formBulkEdit.class_type_id = null
};

const showStudioCreateForm = ref(false);
const closeStudioCreateForm = () => {
    showStudioCreateForm.value = false;
    form_class.studio_id = null
    formEdit.studio_id = null
    formBulkEdit.studio_id = null
};
const createStudioFrom = useForm({
    title: null,
    location_id: null,
});

const rowSelected = ref([]);

const now = DateTime.now().setZone(props.business_seetings.timezone);

const addIdsToSelection = () => {
    const now = DateTime.now().setZone(props.business_seetings.timezone);
    const upcomingItems = props.classes.data.filter((item) => {
        const itemDate = DateTime.fromISO(item.start_date).setZone(
            props.business_seetings.timezone
        );
        return itemDate > now;
    });

    const ids = uniq(map(upcomingItems, "id"));
    if (ids.length == rowSelected.value.length) {
        rowSelected.value = [];
    } else {
        rowSelected.value = ids;
    }
};
const selectableRowsCount = computed(() => {
    const now = DateTime.now().setZone(props.business_seetings.timezone);
    const upcomingItems = props.classes.data.filter((item) => {
        const itemDate = DateTime.fromISO(item.start_date).setZone(
            props.business_seetings.timezone
        );
        return itemDate > now;
    });
    return upcomingItems.length;
});

const notUpcommingItem = (id) => {
    const now = DateTime.now().setZone(props.business_seetings.timezone);
    const item = find(props.classes.data, function (o) {
        return o.id == id;
    });
    const itemDate = item
        ? DateTime.fromISO(item.start_date).setZone(
              props.business_seetings.timezone
          )
        : null;
    return itemDate > now ? true : false;
};

const showBulkEditForm = ref(false);
const bulkEdit = () => {
    showBulkEditForm.value = true;
};

const closeBulkEditForm = () => {
    formBulkEdit.reset().clearErrors();
    showBulkEditForm.value = false;
};
const updateBulkEdit = () => {
    formBulkEdit
        .transform((data) => ({
            ...data,
            ids: rowSelected.value,
            _method: "PUT",
        }))
        .post(route("partner.classes.bulk-edit"), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (data) => {
                formBulkEdit.reset().clearErrors();
                rowSelected.value = [];
                showBulkEditForm.value = false;
            },
        });
};
const formBulkEdit = useForm({
    status: null,
    instructor_id: [],
    class_type_id: null,
    studio_id: null,
    password: null,
});
const showBulkDeleteConfirmationModal = ref(false);
const closeBulkDeleteConfirmationModal = () => {
    showBulkDeleteConfirmationModal.value = false;
    formBulkDelete.reset().clearErrors();
}

const formBulkDelete = useForm({
    password: null,
});

const bulkDelete = () => {
    showBulkDeleteConfirmationModal.value = true
};
const handleBulkDelete = () => {
    formBulkDelete
        .transform((data) => ({
            ...data,
            ids: rowSelected.value,
            _method: "DELETE",
        }))
        .post(route("partner.classes.bulk-delete"), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (data) => {
                rowSelected.value = [];
                closeBulkDeleteConfirmationModal();
            },
        });
};

const showPassword = ref(false);
const inputPasswordType = computed(() =>
    showPassword.value ? "text" : "password"
);
</script>
<template>
    <data-table-layout
        :disableButton="true"
        :extra-actions="rowSelected.length"
        @bulk-edit="bulkEdit"
        @bulk-delete="bulkDelete"
    >
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
            <span class="ml-5">
                <ButtonLink
                    styling="secondary"
                    size="default"
                    @click="showCreateModal = true"
                >
                    Create new
                    <font-awesome-icon class="ml-2" :icon="faPlus" />
                </ButtonLink>
            </span>

            <!-- <ButtonLink
                styling="secondary"
                size="default"
                :href="route('partner.classes.create')"
            >
                Create new (direct)
            </ButtonLink> -->
        </template>

        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @onFilter="openFilterModal"
            />
        </template>

        <template #tableHead>
            <table-head>
                <template #checkbox>
                    <Checkbox
                        :checked="rowSelected.length == selectableRowsCount && selectableRowsCount > 0"
                        class="p-2.5"
                        @click="addIdsToSelection"
                    />
                </template>
            </table-head>
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
            <table-head title="Action" class="flex justify-end" />
        </template>

        <template #tableData>
            <tr
                v-for="(class_lesson, index) in classes.data"
                :key="class_lesson.id"
            >
                <table-data>
                    <div v-if="notUpcommingItem(class_lesson.id)">
                        <label class="flex items-center">
                            <Checkbox
                                v-model:checked="rowSelected"
                                :checked="rowSelected.includes(class_lesson.id)"
                                :value="class_lesson.id"
                            />
                        </label>
                    </div>
                </table-data>
                <table-data>
                    <ButtonLink
                        :href="route('partner.classes.show', class_lesson)"
                    >
                        <span
                            v-if="class_lesson.title.length > 25"
                            v-tooltip="class_lesson.title"
                        >
                            {{ class_lesson.title.substring(0, 25) }}...
                        </span>
                        <span v-else>
                            {{ class_lesson.title }}
                        </span>
                    </ButtonLink>
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
                        :title="class_lesson?.class_type?.title ?? 'Test'"
                    />
                </table-data>
                <table-data class="text-center">
                    <template v-if="class_lesson?.instructor.length">
                        <template
                            v-for="(
                                instructor, ins
                            ) in class_lesson?.instructor"
                            :key="ins"
                        >
                            <AvatarValue
                                class="cursor-pointer inline-flex justify-center mr-1 text-center items-center"
                                :onlyTooltip="true"
                                :title="instructor?.name ?? 'Demo Ins'"
                            />
                        </template>
                    </template>
                    <template v-else>
                        <AvatarValue :title="'Demo Ins'" />
                    </template>
                </table-data>
                <table-data class="text-center">
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
                <table-data class="text-right">
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > classes.data.length - 3"
                        :content-classes="['bg-white']"
                        :row-index="index"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <ActionsIcon />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                as="button"
                                @click="handleUpdateForm(class_lesson)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                Edit
                            </DropdownLink>
                            <DropdownLink
                                as="button"
                                @click="handleDuplicateForm(class_lesson)"
                            >
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
        <template #close>
            <CloseModal @click="closeFilterModal" />
        </template>
        <template #content>
            <FormFilter
                :form="form"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                :classtypes="classtypes"
                @close="closeFilterModal"
                @reset="resetClassFilters"
            />
        </template>
    </SideModal>

    <!-- Export form Modal -->
    <SideModal :show="showExportModal" @close="closeExportModal">
        <template #title> Export Classes </template>
        <template #close>
            <CloseModal @click="closeExportModal" />
        </template>

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
        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

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
                @create-new-instructor="showInstructorCreateForm = true"
                @create-new-class-type="showClassTypeCreateForm = true"
                @create-new-studio="showStudioCreateForm = true"
                modal
            />
        </template>
    </SideModal>

    <!-- Edit class Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Edit Class </template>
        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form
                :form="formEdit"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                :classtypes="classtypes"
                :business_seetings="business_seetings"
                :submitted="updateClass"
                @create-new-instructor="showInstructorCreateForm = true"
                @create-new-class-type="showClassTypeCreateForm = true"
                @create-new-studio="showStudioCreateForm = true"
                modal
            />
        </template>
    </SideModal>

    <!-- Duplicate class Modal -->
    <SideModal
        :show="showDuplicateClassModal"
        @close="closeDuplicateClassModal"
    >
        <template #title> Duplicate Class </template>
        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form
                :form="duplicateClassForm"
                :statuses="statuses"
                :studios="studios"
                :instructors="instructors"
                :classtypes="classtypes"
                :business_seetings="business_seetings"
                :submitted="storeDuplicateClass"
                modal
                :isDuplicate="true"
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

    <SideModal :show="showBulkEditForm" @close="closeBulkEditForm">
        <template #title> Edit Detail for Select Items </template>
        <template #close>
            <CloseModal @click="closeBulkEditForm" />
        </template>

        <template #content>
            <BulkEditForm
                :form="formBulkEdit"
                :statuses="statuses"
                :studios="studioList"
                :instructors="instructors"
                :classtypes="classtypes"
                :business_seetings="business_seetings"
                :submitted="updateBulkEdit"
                @create-new-instructor="showInstructorCreateForm = true"
                @create-new-class-type="showClassTypeCreateForm = true"
                @create-new-studio="showStudioCreateForm = true"
                modal
            />
        </template>
    </SideModal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="showBulkDeleteConfirmationModal" @close="closeBulkDeleteConfirmationModal">
        <template #title> Confirmation required </template>

        <template #content>
            <div class="mt-5">
                <span class="text-danger-500">
                    Are you sure you would like to delete this all selected items?
                </span>
            </div>
            <div class="mt-3">
                <span class="font-semibold">
                    You need to provide your password to confirm
                </span>
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Your Password" class="mb-1" />
                <div class="relative">
                    <TextInput
                        :type="inputPasswordType"
                        id="password"
                        v-model="formBulkDelete.password"
                        class="mt-1 block w-full"
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-dark-400 focus:outline-none"
                    >
                        <template v-if="showPassword">
                            <font-awesome-icon
                                :icon="faEyeSlash"
                                style="color: #b0b2b5"
                            />
                        </template>
                        <template v-else>
                            <font-awesome-icon
                                style="color: #4ca054"
                                :icon="faEye"
                                class="blue-grey-50"
                            />
                        </template>
                    </button>
                </div>
                <InputError class="mt-2" :message="formBulkDelete.errors.password" />
            </div>
        </template>

        <template #footer>
            <ButtonLink
                size="default"
                styling="default"
                @click="closeBulkDeleteConfirmationModal"
            >
                Cancel
            </ButtonLink>

            <ButtonLink
                size="default"
                styling="danger"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="handleBulkDelete"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <OnTheFlyResourceCreate
        :show-instructor-create-form="showInstructorCreateForm"
        :show-class-type-create-form="showClassTypeCreateForm"
        :show-studio-create-form="showStudioCreateForm"
        @close-instructor-create-form="closeInstructorCreateForm"
        @close-class-type-create-form="closeClassTypeCreateForm"
        @close-studio-create-form="closeStudioCreateForm"
        />
</template>
