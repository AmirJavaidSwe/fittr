<script setup>
import { ref, watch } from "vue";
import CardBasic from "@/Components/CardBasic.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Search from "@/Components/DataTable/Search.vue";
import { useForm } from "@inertiajs/vue3";
import CloseModal from "@/Components/CloseModal.vue";
import SideModal from "@/Components/SideModal.vue";
import Form from "./Form.vue";
import WaiverForm from "./WaiverForm.vue";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import DateValue from "@/Components/DataTable/DateValue.vue";
import { DateTime } from "luxon";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const props = defineProps({
    business_settings: Object,
    page_title: String,
    family_members: Object,
    waiver: Object | null,
});


const showCreateModal = ref(false);
const showEditModal = ref(false);
const showWaiverModal = ref(false);
const closeCreateModal = () => {
    showCreateModal.value = false;
    CreateForm.reset().clearErrors();
};

const closeEditModal = () => {
    showEditModal.value = false;
    EditForm.reset().clearErrors();
};

const closeWaiverModal = () => {
    showWaiverModal.value = false;
    WaiverFormData.reset().clearErrors();
};
const CreateForm = useForm({
    name: null,
    date_of_birth: null,
    profile_photo: null,
});
const EditForm = useForm({
    id: null,
    name: null,
    date_of_birth: null,
    profile_photo: null,
    profile_photo_path: null,
    profile_photo_url: null,
    has_image: false,
});
const WaiverFormData = useForm({
    title: null,
    description: null,
    sign_needed: null,
    questionType: null,
});

const storeFamilyMember = () => {
    // check if waiver is not null then show waiver modal
    if (props.waiver !== null) {
        showWaiverModal.value = true;
    }
    return;
    CreateForm.post(
        route("ss.member.family.store", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                CreateForm.reset().clearErrors();
                showCreateModal.value = false;
            },
        }
    );
};

const removeFile = (param) => {
    if (param == "CreateForm") {
        CreateForm.profile_photo = null;
    } else if (param == "EditForm") {
        EditForm.profile_photo_path = null;
        EditForm.has_image = false;
    }
};

const editMember = (member) => {
    EditForm.reset().clearErrors();
    EditForm.id = member.id;
    EditForm.name = member.name;
    EditForm.date_of_birth = member.date_of_birth;
    EditForm.profile_photo = member.profile_photo;
    EditForm.profile_photo_path = member.profile_photo_path;
    EditForm.profile_photo_url =
        member.profile_photo_path !== null ? member.profile_photo_url : null;
    // EditForm.has_image = member.profile_photo_path !== null ? true : false;
    EditForm.has_image = !!member.profile_photo_path;
    showEditModal.value = true;
};

const updateMember = () => {
    EditForm.transform((data) => ({
        ...data,
        _method: "put",
    })).post(
        route("ss.member.family.update", {family: EditForm.id, subdomain: props.business_settings.subdomain}),
        {
            preserveScroll: true,
            onSuccess: () => {
                EditForm.reset().clearErrors();
                showEditModal.value = false
            },
        }
    );
};

//delete confirmation modal:
const itemDeleting = ref(false);
const itemIdDeleting = ref(null);
const confirmDeletion = (id) => {
    itemIdDeleting.value = id;
    itemDeleting.value = true;
};

const deleteItem = () => {
    EditForm.delete(
        route("ss.member.family.destroy", {family: itemIdDeleting.value, subdomain: props.business_settings.subdomain}),
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
</script>

<template>
    <div
        class="text-right"
    >
        <ButtonLink class="text-right"
            styling="secondary"
            size="default"
            @click="showCreateModal = true"
        >
            Add New Member
            <font-awesome-icon class="ml-2" :icon="faPlus" />
        </ButtonLink>
    </div>
    <div class="mt-[75px]">
        <template v-for="(familyMember, index) in family_members">
            <CardBasic class="mb-4">
                <template #default>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <img
                                :src="familyMember.profile_photo_url"
                                :alt="familyMember.name"
                                class="rounded-full h-20 w-20 object-cover"
                            />
                            <div class="pl-4">
                                <div class="block pl-6 font-semibold mb-2">
                                    {{ familyMember.name }}
                                </div>
                                <div class="block pl-4">
                                    <DateValue
                                        :show-calender-icon="false"
                                        :date="DateTime.fromISO(familyMember.date_of_birth).toFormat(business_settings.date_format.format_js)"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex items-center justify-start mr-20">
                            <EditIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-5 cursor-pointer text-blue"
                                v-tooltip="'Edit'"
                                @click.prevent="editMember(familyMember)"
                            />
                            <DeleteIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2 cursor-pointer text-red-900"
                                v-tooltip="'Delete'"
                                @click.prevent="confirmDeletion(familyMember.id)"
                            />
                        </div>
                    </div>
                </template>
            </CardBasic>
        </template>
    </div>

    <!-- Create Modal -->
    <SideModal :show="showCreateModal" @close="closeCreateModal">
        <template #title> Add New Family Member </template>
        <template #close>
            <CloseModal @click="closeCreateModal" />
        </template>

        <template #content>
            <Form
                :form="CreateForm"
                :business_settings="business_settings"
                :submitted="storeFamilyMember"
                @removeFile="removeFile('CreateForm')"
                modal
            />
        </template>
    </SideModal>
    <!-- Edit Modal -->
    <SideModal :show="showEditModal" @close="closeEditModal">
        <template #title> Edit Family Member </template>
        <template #close>
            <CloseModal @click="closeEditModal" />
        </template>

        <template #content>
            <Form
                :form="EditForm"
                :business_settings="business_settings"
                :submitted="updateMember"
                @removeFile="removeFile('EditForm')"
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
                :class="{ 'opacity-25': EditForm.processing }"
                :disabled="EditForm.processing"
                @click="deleteItem"
            >
                Delete
            </ButtonLink>
        </template>
    </ConfirmationModal>

    <!-- Waiver Modal -->
    <SideModal :show="showWaiverModal" @close="closeWaiverModal">
        <template #title> Waiver Details </template>
        <template #close>
            <CloseModal @click="closeWaiverModal" />
        </template>

        <template #content>
            <WaiverForm
                :form="WaiverFormData"
                :submitted="storeFamilyMember"
                :waiver="props.waiver"
                modal
            />
        </template>
    </SideModal>
</template>
