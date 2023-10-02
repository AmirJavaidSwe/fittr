<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { WaiverHelpers } from "./WaiverHelpers/Index.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import RichTextInput from "@/Components/RichTextInput.vue";
import { faPlus, faGripVertical } from "@fortawesome/free-solid-svg-icons";
import DeleteIcon from "@/Icons/Delete.vue";
import Switcher from "@/Components/Switcher.vue";
import draggable from 'vuedraggable';

const props = defineProps({
    editWaiver: Object,
});

const form = useForm({
    title: props.editWaiver.title,
    description: props.editWaiver.description,
    show_at: props.editWaiver.show_at,
    is_active: props.editWaiver.is_active ? true : false,
    is_signature_needed:
        props.editWaiver.is_active && props.editWaiver.is_signature_needed
            ? true
            : false,
    sign_again:
        props.editWaiver.is_active &&
        props.editWaiver.is_signature_needed &&
        props.editWaiver.sign_again
            ? true
            : false,
});

const helpers = WaiverHelpers();
const itemDeleting = ref(true);
const itemIdDeleting = ref(null);

const waiverShowPlaces = helpers.getShowPlaces();

const update = () => {
    form.transform((data) => ({
        ...data,
        questions: questionsData.value,
    })).put(route("partner.waivers.update", props.editWaiver.id), {
        preserveScroll: true,
    });
};

const questionsData = ref(props.editWaiver.questions);

const addQuestion = () => {
    questionsData.value.push({
        question: "",
        selectedQuestionType: null,
    });
};

const removeLine = (index) => {
    questionsData.value.splice(index, 1);
};

const questionTypes = helpers.questionTypes();
const showConfitmationModal = ref(false);
const closeConfitmationModal = (param) => {
    if (param === false) {
        form.sign_again = false;
    }
    showConfitmationModal.value = false;
};

watch(
    () => form.sign_again,
    (newVal, oldVal) => {
        if (newVal !== oldVal && newVal === true) {
            showConfitmationModal.value = true;
        }
    }
);
</script>

<template>
    <!-- ... other template code ... -->
    <FormSection @submitted="update" class="w-full lg:w-1/2">
        <template #title> Waiver Information </template>
        <template #description> Edit Waiver Information </template>
        <template #form>
            <div class="space-y-4">
                <div>
                    <InputLabel for="title" value="Title" />
                    <TextInput
                        id="title"
                        v-model="form.title"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="off"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                <div>
                    <InputLabel for="show_at" value="Show At" />
                    <Multiselect
                        autocomplete="off"
                        class="w-full"
                        mode="single"
                        v-model="form.show_at"
                        :options="waiverShowPlaces"
                    />
                    <InputError :message="form.errors.show_at" class="mt-2" />
                </div>
                <div>
                    <InputLabel for="description" value="Contents" />
                    <RichTextInput v-model="form.description" />
                    <InputError
                        :message="form.errors.description"
                        class="mt-2"
                    />
                </div>

                <div class="flex items-center">
                    <div class="flex-grow">
                        <div class="bg-blue-100">
                            <label for="question" class="block font-medium p-2">Question</label>
                        </div>
                    </div>
                    <div class="w-40 mr-1">
                        <div class="bg-green-100">
                            <label for="questionType" class="block font-medium p-2">Question Type</label>
                        </div>
                    </div>
                    <div class="w-10"></div>
                </div>

                <transition-group>
                    <draggable
                        item-key="name"
                        key="dragggable"
                        :list="questionsData"
                        :animation="300"
                        ghostClass="opacity-50"
                        @start="drag = true"
                        @end="drag = false"
                        >
                        <template #item="{ element }">
                            <div :key="element.question" class="flex items-center gap-1 mb-2">
                                <font-awesome-icon class="cursor-move p-1" :icon="faGripVertical" />
                                <div class="flex-grow">
                                    <TextInput
                                        v-model="element.question"
                                        type="text"
                                        class="w-full"
                                        placeholder="Add Question here"
                                    />
                                </div>
                                <div class="w-40">
                                    <Multiselect
                                        class="w-full"
                                        mode="single"
                                        v-model="element.selectedQuestionType"
                                        :options="questionTypes"
                                        placeholder="Select"
                                    />
                                </div>
                                <ButtonLink 
                                        @click="removeLine(element.question)"
                                        styling="transparent"
                                        size="small"
                                        class=""
                                        type="button"
                                        >
                                    <DeleteIcon class="w-4 h-4 text-danger-500"/>
                                </ButtonLink>
                            </div>
                        </template>
                    </draggable>
                </transition-group>

                <ButtonLink 
                    @click="addQuestion"
                    styling="primary"
                    size="small"
                    class=""
                    type="button"
                    >
                    <font-awesome-icon class="mr-2" :icon="faPlus" />
                    Add Question
                </ButtonLink>
                
                <div>
                    <Switcher
                        v-model="form.is_active"
                        title="Active"
                        description=""
                        :show-labels="['No', 'Yes']"
                    />
                </div>
                <div v-if="form.is_active">
                    <Switcher
                        v-model="form.is_signature_needed"
                        title="Signature Needed?"
                        description=""
                        :show-labels="['No', 'Yes']"
                    />
                </div>

                <div v-if="form.is_signature_needed">
                    <Switcher
                        v-model="form.sign_again"
                        title="Require existing users to sign this?"
                        description=""
                        :show-labels="['No', 'Yes']"
                    />
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Update
            </ActionMessage>
            <ButtonLink
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Update
            </ButtonLink>
        </template>
    </FormSection>
    <template>
        <!-- Confirmation Modal -->
        <ConfirmationModal
            :show="showConfitmationModal"
            @close="closeConfitmationModal(false)"
        >
            <template #title> Are you sure you want to do this? </template>

            <template #content>
                This will delete all previously signed waivers from all users?
            </template>

            <template #footer>
                <ButtonLink
                    size="default"
                    styling="default"
                    @click="closeConfitmationModal(false)"
                >
                    Cancel
                </ButtonLink>

                <ButtonLink
                    size="default"
                    styling="danger"
                    class="ml-3"
                    @click="closeConfitmationModal(true)"
                >
                    Yes, Do it.
                </ButtonLink>
            </template>
        </ConfirmationModal>
    </template>
</template>
