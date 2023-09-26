<script setup>
import { ref, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Form from "./Form.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import SideModal from "@/Components/SideModal.vue";
import WaiverAcceptCheck from "@/Icons/WaiverAcceptCheck.vue";
import WaiverNotAcceptedCheck from "@/Icons/WaiverNotAcceptedCheck.vue";
import WaiverListing from "./WaiverListing.vue";
import WaiverForm from "../../WaiverForm.vue";
import { DateTime } from "luxon";
import DateValue from "@/Components/DataTable/DateValue.vue";
import CardBasic from "@/Components/CardBasic.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { faUserCircle } from "@fortawesome/free-regular-svg-icons";
import EditIcon from "@/Icons/Edit.vue";
import DeleteIcon from "@/Icons/Delete.vue";

const props = defineProps({
    business_settings: Object,
    page_title: String,
    family_members: Object,
    waivers: Object,
});

const user = ref(usePage().props.user);

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

const user_waiver_ids = ref([]);
const signed_waiver_ids = ref([]);

const CreateForm = useForm({
    first_name: null,
    last_name: null,
    date_of_birth: null,
    profile_photo: null,
});
const EditForm = useForm({
    id: null,
    first_name: null,
    last_name: null,
    date_of_birth: null,
    profile_photo: null,
    profile_photo_path: null,
    profile_photo_url: null,
    has_image: false,
});

const storeFamilyMember = () => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    CreateForm.transform((data) => ({
        ...data,
        user_waiver_ids: user_waiver_ids.value,
        signed_waiver_ids: signed_waiver_ids.value
    })).post(
        route("ss.member.family.store", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                CreateForm.reset().clearErrors();
                showCreateModal.value = false;
                user_waiver_ids.value = [];
                signed_waiver_ids.value = [];
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

const showCreateFamily = () => {
    showCreateModal.value = true;
    CreateForm.reset().clearErrors();
};
const editMember = (member) => {
    EditForm.reset().clearErrors();
    EditForm.id = member.id;
    EditForm.first_name = member.first_name;
    EditForm.last_name = member.last_name;
    EditForm.date_of_birth = member.date_of_birth;
    EditForm.profile_photo = member.profile_photo;
    EditForm.profile_photo_path = member.profile_photo_path;
    EditForm.profile_photo_url =
        member.profile_photo_path !== null ? member.profile_photo_url : null;
    EditForm.has_image = !!member.profile_photo_path;
    signed_waiver_ids.value = member.user_waivers.length ? member.user_waivers.map((waiver) => waiver.waiver_id) : [];
    user_waiver_ids.value = member.user_waivers.length ? member.user_waivers.map((waiver) => waiver.id) : [];
    showEditModal.value = true;
};

const updateMember = () => {
    if (
        props.waivers.length &&
        user_waiver_ids.value.length !== props.waivers.length
    ) {
        showWaiverModal.value = true;
        return;
    }
    EditForm.transform((data) => ({
        ...data,
        _method: "put",
    })).post(
        route("ss.member.family.update", {
            family: EditForm.id,
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                showEditModal.value = false;
                showWaiverModal.value = false;
                user_waiver_ids.value = [];
                signed_waiver_ids.value = [];
                EditForm.reset().clearErrors();
            },
        }
    );
};

const storeOrUpdateFamilyMember = () => {
    if (EditForm.id && showEditModal.value == true) {
        updateMember();
    } else {
        storeFamilyMember();
    }
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
        route("ss.member.family.destroy", {
            family: itemIdDeleting.value,
            subdomain: props.business_settings.subdomain,
        }),
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

const updateUserWaiverIds = ($event) => {
    signed_waiver_ids.value.push($event.waiver_id);
    user_waiver_ids.value.push($event.user_waiver_id);
};
</script>

<template>
    <div class="text-right">
        <ButtonLink
            class="text-right"
            styling="secondary"
            size="default"
            @click="showCreateFamily"
        >
            Add New Member
            <font-awesome-icon class="ml-2" :icon="faPlus" />
        </ButtonLink>
    </div>
    <div class="flex flex-wrap gap-4">
        <template v-for="(familyMember, index) in family_members" :key="index">
            <CardBasic width="lg" class="mb-4">
                <template #default>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-grow items-center">
                            <img
                                v-if="profile_photo_url"
                                :src="familyMember.profile_photo_url"
                                :alt="familyMember.full_name"
                                class="rounded-full h-20 w-20 object-cover"
                            />
                            <font-awesome-icon v-else :icon="faUserCircle"  />
                            <div class="pl-4">
                                <div class="block pl-6 font-semibold mb-2">
                                    {{ familyMember.full_name }}
                                </div>
                                <div class="block pl-4">
                                    <DateValue
                                        :show-calender-icon="false"
                                        :date="
                                            DateTime.fromISO(
                                                familyMember.date_of_birth
                                            ).toFormat(
                                                business_settings.date_format
                                                    .format_js
                                            )
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex">
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

    <WaiverListing
        :edit_form="EditForm"
        :business_settings="business_settings"
        :show="showWaiverModal"
        :waivers="props.waivers"
        :user="user"
        :user_waiver_ids="user_waiver_ids"
        :signed_waiver_ids="signed_waiver_ids"
        @updateUserWaiverIds="updateUserWaiverIds"
        @close="showWaiverModal = false"
    />
</template>
