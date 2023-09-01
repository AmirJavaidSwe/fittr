<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import Search from "@/Components/DataTable/Search.vue";
import Pagination from "@/Components/Pagination.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import Form from "./Form.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import EditIcon from "@/Icons/Edit.vue";
import ActionsIcon from "@/Icons/ActionsIcon.vue";
import DateValue from "@/Components/DataTable/DateValue.vue";
import SideModal from "@/Components/SideModal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import StatusLabel from "@/Components/StatusLabel.vue";
import PreviewNotificationTemplate from "./PreviewNotificationTemplate.vue";

const props = defineProps({
    disableSearch: {
        type: Boolean,
        default: false,
    },
    business_settings: Object,
    notificationTemplates: Object,
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

const editForm = useForm({
    title: "",
    key: "",
    sg_template_name: "",
    sg_template_id: "",
    placeholders: [],
    mail_driver: "",
    from_name: "",
    from_email: "",
    subject: "",
    content: "",
    content_plain: "",
    content_sms: "",
    unsubscribe: false,
    bypass: false,
    status: true,
    notes: "",
});

const runSearch = () => {
    form.get(route("partner.notification-templates.index"), {
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


// create new notification template modal
const modal = ref(false);
const editMode = ref(false);
const editId = ref(null);

const saveForm = () => {
    editForm
        .transform((data) => ({
            ...data,
            _method: "put",
        }))
        .post(route("partner.notification-templates.update", { id: editId.value }), {
            preserveScroll: true,
            onSuccess: () => {
                modal.value = false;
                editId.value = null;
                editForm.reset().clearErrors();
            },
        });
};

const showEditModal = (data) => {
    editForm.reset().clearErrors();

    data = Object.assign({}, data);

    editForm.title = data.title;
    editForm.key = data.key;
    editForm.sg_template_name = data.sg_template_name;
    editForm.sg_template_id = data.sg_template_id;
    editForm.placeholders = [...data.placeholders];
    editForm.mail_driver = data.mail_driver;
    editForm.from_name = data.from_name;
    editForm.from_email = data.from_email;
    editForm.subject = data.subject;
    editForm.content = data.content;
    editForm.content_plain = data.content_plain;
    editForm.content_sms = data.content_sms;
    editForm.unsubscribe = data.unsubscribe;
    editForm.bypass = data.bypass;
    editForm.status = data.status;
    editForm.notes = data.notes;

    editId.value = data.id;
    editMode.value = true;
    modal.value = true;
};

const preview = ref(false);
const preivewProcessing = ref(false);
const previewHtml = ref('');

const showPreview = async () => {
    preivewProcessing.value = true;
    const res = await axios.post(route('partner.notification-templates.preview', {content: form.content}))
        .catch(console.error);

    previewHtml.value = res.data;
    preview.value = true;
    preivewProcessing.value = false;
}

</script>

<template>
    <data-table-layout :disable-button="true" :disable-search="disableSearch">
        <template #search>
            <Search
                v-model="form.search"
                :disable-search="disableSearch"
                @reset="form.search = null"
                @pp_changed="setPerPage"
                :noFilter="true"
            />
        </template>

        <template #tableHead>
            <table-head
                title="Title"
                @click="setOrdering('title')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'title'"
            />
            <table-head
                title="Subject"
                @click="setOrdering('subject')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'subject'"
            />
            <table-head
                title="Unsubscribe"
                @click="setOrdering('unsubscribe')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'unsubscribe'"
            />
            <table-head
                title="Bypass"
                @click="setOrdering('bypass')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'bypass'"
            />
            <table-head
                title="Status"
                @click="setOrdering('status')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'status'"
            />
            <table-head
                title="Created At"
                @click="setOrdering('created_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'created_at'"
            />
            <table-head
                title="Updated At"
                @click="setOrdering('updated_at')"
                :arrowSide="form.order_dir"
                :currentSort="form.order_by === 'updated_at'"
            />
            <table-head title="Action" class="justify-end flex" />
        </template>

        <template #tableData>
            <tr v-for="(notificationTemplate, index) in notificationTemplates.data">
                <!-- <table-data :title="notificationTemplate.id"/> -->
                <table-data>
                    <span
                        v-if="notificationTemplate.title?.length > 25"
                        v-tooltip="notificationTemplate.title"
                    >
                        {{ notificationTemplate.title.substring(0, 25) }}...
                    </span>
                    <span v-else>
                        {{ notificationTemplate.title }}
                    </span>
                </table-data>
                <table-data>
                    <span
                        v-if="notificationTemplate.subject?.length > 25"
                        v-tooltip="notificationTemplate.subject"
                    >
                        {{ notificationTemplate.subject.substring(0, 25) }}...
                    </span>
                    <span v-else>
                        {{ notificationTemplate.subject }}
                    </span>
                </table-data>
                <table-data>
                    <StatusLabel :status="notificationTemplate.unsubscribe ? 'Yes' : 'No'" />
                </table-data>
                <table-data>
                    <StatusLabel :status="notificationTemplate.bypass ? 'Yes' : 'No'" />
                </table-data>
                <table-data>
                    <StatusLabel :status="notificationTemplate.status ? 'Enabled' : 'Disabled'" />
                </table-data>
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(notificationTemplate.created_at)
                                .setZone(business_settings.timezone)
                                .toFormat(
                                    business_settings.date_format.format_js
                                )
                        "
                    />
                </table-data>
                <table-data>
                    <DateValue
                        :date="
                            DateTime.fromISO(notificationTemplate.updated_at).toRelative()
                        "
                    />
                </table-data>
                <table-data class="justify-end flex">
                    <Dropdown
                        align="right"
                        width="48"
                        :top="index > notificationTemplates.data.length - 3"
                        :content-classes="['bg-white']"
                    >
                        <template #trigger>
                            <button class="text-dark text-lg">
                                <ActionsIcon />
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                as="button"
                                @click="showEditModal(notificationTemplate)"
                            >
                                <EditIcon
                                    class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                />
                                <span> Edit </span>
                            </DropdownLink>
                            <!-- <DropdownLink
                                as="button"
                                @click="confirmDeletion(notificationTemplate.id)"
                            >
                                <span class="text-danger-500 flex items-center">
                                    <DeleteIcon
                                        class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                                    />
                                    <span> Delete </span>
                                </span>
                            </DropdownLink> -->
                        </template>
                    </Dropdown>
                </table-data>
            </tr>
        </template>

        <template #pagination>
            <pagination
                :links="notificationTemplates.links"
                :to="notificationTemplates.to"
                :from="notificationTemplates.from"
                :total="notificationTemplates.total"
                @pp_changed="setPerPage"
            />
        </template>
    </data-table-layout>

    <!-- Edit Notification Template Modal -->
    <SideModal :show="modal" @close="modal = false">
        <template #title>
            Edit Notification Template
        </template>
        <template #close>
            <CloseModal @click="modal = false" />
        </template>

        <template #content>
            <Form
                :form="editForm"
                modal
            />
        </template>
        <template #footer>
            <ButtonLink
                styling="default"
                size="default"
                @click="showPreview"
                class="mr-2"
                :disabled="preivewProcessing"
            >
                <span>Preview</span>
            </ButtonLink>

            <ButtonLink
                :disabled="editForm.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="saveForm"
            >
                <span>Save changes</span>
            </ButtonLink>
        </template>
    </SideModal>

    <PreviewNotificationTemplate
        :show="preview"
        :notificationDetails="editForm"
        :previewHtml="previewHtml"
        @close="preview = false"
    />
</template>
