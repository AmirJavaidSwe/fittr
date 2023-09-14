<script setup>
import { ref, watch, computed } from "vue";
import CardBasic from "@/Components/CardBasic.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CloseModal from "@/Components/CloseModal.vue";
import SideModal from "@/Components/SideModal.vue";
import WaiverAcceptCheck from "@/Icons/WaiverAcceptCheck.vue";
import WaiverNotAcceptedCheck from "@/Icons/WaiverNotAcceptedCheck.vue";
import Form from "./Form.vue";
import WaiverForm from "../../WaiverForm.vue";
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
    waivers: Object,
    user_waivers: Object|null,
    user:Object,
});

const waiverFormData = useForm({
    waiverQandA: [],
    sign: props?.user_waivers?.length ? props?.user_waivers?.signature : null,
});
const showWaiver = () => {
    showWaivers.value = true;
}
const closedShowWaiver = () => {
    showWaivers.value = false;

    waiverFormData.reset().clearErrors();
}
const waiverQandData = (id) => {
    waiver.value = props.waivers.find((item) => item.id == id);
    waiverFormData.waiverQandA = []
    if (waiver) {
        for (let i = 0; i < waiver.value?.questions.length; i++) {
            waiverFormData.waiverQandA.push({
                question: waiver.value?.questions[i].question,
                type: waiver.value?.questions[i].selectedQuestionType,
                answer: null,
            });
        }
    }
    showWaiver();
};



const showCreateModal = ref(false);
const showEditModal = ref(false);
const showWaiverModal = ref(false);
const showWaivers = ref(false);
let waiver = ref('');


const closeCreateModal = () => {
    showCreateModal.value = false;
    CreateForm.reset().clearErrors();
};

const closeEditModal = () => {
    showEditModal.value = false;
    showWaiverModal.value = true;
    EditForm.reset().clearErrors();
};

const closeWaiverModal = () => {
    showWaiverModal.value = false;
    waiverFormData.reset().clearErrors();
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


const storeFamilyMember = () => {
    if (props.waiver !== null) {
        if(!showWaiverModal.value) {
            showWaiverModal.value = true;
            return;
        }
    }
    CreateForm.transform((data) => ({
        ...data,
        waiver_id: waiver !== null ? waiver.value?.id : null,
        waiver_data: waiver !== null  ? {
            data: waiverFormData.waiverQandA,
            signature: waiverFormData.sign ?? null,
        } : {},
    })).post(
        route("ss.member.family.store", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                showCreateModal.value = false;
                showWaiverModal.value = false;
                waiverFormData.reset().clearErrors();
                CreateForm.reset().clearErrors();
                closedShowWaiver();
                waiverQandData(waiver.value?.id);
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
    waiverFormData.reset().clearErrors();
}
const editMember = (member) => {
    EditForm.reset().clearErrors();
    EditForm.id = member.id;
    EditForm.name = member.name;
    EditForm.date_of_birth = member.date_of_birth;
    EditForm.profile_photo = member.profile_photo;
    EditForm.profile_photo_path = member.profile_photo_path;
    EditForm.profile_photo_url =
    member.profile_photo_path !== null ? member.profile_photo_url : null;
    EditForm.has_image = !!member.profile_photo_path;
    showEditModal.value = true;
    waiverFormData.reset().clearErrors();
    waiverQandData();
};

const checkForWaiverValidation = computed(() => {
    if ((props.waiver !== null && props.waiver.sign_again && !props?.user_waiver?.length) || (props.waiver !== null && props.waiver.sign_again && props?.user_waiver?.length) && (!props.user_waiver.includes(EditForm.id))) {
        return true
    }
    return false
})

const updateMember = () => {
    if (checkForWaiverValidation.value && !showWaiverModal.value) {
        showWaiverModal.value = true;
        return;
    }
    EditForm.transform((data) => ({
        ...data,
        _method: "put",
        waiver_id: checkForWaiverValidation.value ? props.waiver.id : null,
        waiver_data: props.waiver !== null ? {
            data: waiverFormData.waiverQandA,
            signature: waiverFormData.sign,
        } : {},
    })).post(
        route("ss.member.family.update", {
            family: EditForm.id,
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                EditForm.reset().clearErrors();
                showEditModal.value = false;
                showWaiverModal.value = false;
                waiverFormData.reset().clearErrors();
            },
        }
        );
    };

    const storeOrUpdateFamilyMember = () => {
        if(EditForm.id && showEditModal.value == true) {
            updateMember()
        } else {
            storeFamilyMember()
        }
    }


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
                    waiverFormData.reset().clearErrors();
                },
            }
            );
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
    <div class="mt-[75px]">
        <template v-for="(familyMember, index) in family_members" :key="index">
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
                        <div
                            class="inline-flex items-center justify-start mr-20"
                        >
                            <EditIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-5 cursor-pointer text-blue"
                                v-tooltip="'Edit'"
                                @click.prevent="editMember(familyMember)"
                            />
                            <DeleteIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2 cursor-pointer text-red-900"
                                v-tooltip="'Delete'"
                                @click.prevent="
                                    confirmDeletion(familyMember.id)
                                "
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
    <SideModal :show="showWaivers" @close="showWaiver">
        <template #title>
            <div class="w-full">
                You need to sign "{{waiver?.title}}" once before adding
                family members
            </div>
        </template>
        <template #close>
            <div class="w-20 text-right">
                <CloseModal @click="closedShowWaiver" />
            </div>
        </template>

        <template #content>
            <WaiverForm
                :form="waiverFormData"
                :create-form="CreateForm"
                :edit-form="EditForm"
                :submitted="storeOrUpdateFamilyMember"
                :waiver="waiver"
                modal
            />
        </template>
    </SideModal>
    <SideModal :show="showWaiverModal" @close="closeWaiverModal">
        <template #title>
            <div class="w-full mb-16">
                <p class="text-xl mb-2">Welcome {{ props?.user?.name }}!<span class="font-bold">!</span></p>
                <p class="text-xl mb-4">You just need to check out the following important documents in order to add Family Member.</p>
            </div>
        </template>
        <template #close>
            <div class="w-20 text-right">
                <CloseModal @click="closeWaiverModal" />
            </div>
        </template>

        <template #content>
            <div class="flex items-center mt-2"  v-for="waiver in props.waivers" :key="waiver.id">
                <WaiverAcceptCheck   v-if="props?.user_waivers?.find((item) => item.waiver_id === waiver.id) !==
                    undefined ? true : false "/>
                <WaiverNotAcceptedCheck  v-if="props?.user_waivers?.find((item) => item.waiver_id === waiver.id) ==
                    undefined ? true : false "/>
                <span class="text-xl ml-4">{{ waiver.title }} Read and signed document</span>
                <!-- <button class="ml-auto bg-[#E8A838] hover:bg-[#E8A838] text-white px-4 py-2 rounded focus:outline-none focus:ring focus:ring-[#F0F0F0]">view</button> -->
                <ButtonLink
                styling="secondary"
                size="default"
                class="mt-4 ml-auto"
                @click="waiverQandData(waiver?.id)"
                >
                View
               </ButtonLink>
            </div>
            <div class="flex justify-center space-x-4">
                <ButtonLink
                  styling="secondary"
                  size="default"
                  class="mt-4"
                  @click="closeWaiverModal"
                >
                  Go To Back
                </ButtonLink>
            </div>
        </template>
    </SideModal>
</template>
