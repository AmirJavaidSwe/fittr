<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { WaiverHelpers } from "./WaiverHelpers/Index.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import Checkbox from "@/Components/Checkbox.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Multiselect from "@vueform/multiselect";
import {
    faPlus,
    faArrowUp,
    faArrowDown,
} from "@fortawesome/free-solid-svg-icons";
import DeleteIcon from "@/Icons/Delete.vue";
import Switcher from "@/Components/Switcher.vue";

const props = defineProps({
    editWaiver: Object,
});


const form = useForm({
    title: props.editWaiver.title,
    description: props.editWaiver.description,
    show_at: props.editWaiver.show_at,
    is_signature_needed: props.editWaiver.is_signature_needed,
    is_active : props.editWaiver.is_active,
    sign_again : props.editWaiver.sign_again,
});

const helpers = WaiverHelpers();

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

const moveDown = (index) => {
    if (index >= 0 && index < questionsData.value.length - 1) {
        const itemToMove = questionsData.value[index];
        questionsData.value.splice(index, 1); // Remove the item from its current position
        questionsData.value.splice(index + 1, 0, itemToMove); // Insert it one index down
    }
};

const moveUp = (index) => {
    if (index > 0 && index < questionsData.value.length) {
        const itemToMove = questionsData.value[index];
        questionsData.value.splice(index, 1); // Remove the item from its current position
        questionsData.value.splice(index - 1, 0, itemToMove); // Insert it one index up
    }
};
</script>

<template>
    <!-- ... other template code ... -->
    <FormSection @submitted="update" class="w-full lg:w-1/2">
        <template #title> Waiver Information </template>
        <template #description> Edit Waiver Information </template>
        <template #form>
            <div class="flex flex-col space-y-4">
                <div class="col-span-6 sm:col-span-4">
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
                <div class="col-span-6 sm:col-span-4">
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
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="description" value="Description" />
                    <TextArea
                        id="description"
                        v-model="form.description"
                        type="text"
                        class="mt-1 block w-full"
                        rows="5"
                        cols="10"
                        autocomplete="description"
                    />
                    <InputError
                        :message="form.errors.description"
                        class="mt-2"
                    />
                </div>

                <div class="w-full flex items-center">
                    <div class="w-96 pr-2">
                        <div class="bg-blue-100">
                            <label for="question" class="block font-medium p-2"
                                >Question</label
                            >
                        </div>
                    </div>
                    <div class="w-40 pr-2">
                        <div class="bg-green-100">
                            <label
                                for="questionType"
                                class="block font-medium p-2"
                                >Question Type</label
                            >
                        </div>
                    </div>
                    <div class="w-10"></div>
                </div>

                <div
                    class="items-center w-full flex"
                    v-for="(options, i) in questionsData"
                    :key="i"
                >
                    <div class="w-4 pr-1" v-if="i != questionsData.length - 1" @click="moveDown(i)">
                        <span  class="cursor-pointer">
                            <font-awesome-icon
                                :icon="faArrowDown"
                                style="color: #333"
                            />
                        </span>
                    </div>
                    <div class="w-4 pr-1" v-if="i != 0" @click="moveUp(i)">
                        <span class="cursor-pointer">
                            <font-awesome-icon
                                :icon="faArrowUp"
                                style="color: #333"
                            />
                        </span>
                    </div>
                    <div class="w-96 pr-2">
                        <TextInput
                            :id="'question--' + i"
                            v-model="questionsData[i].question"
                            type="text"
                            class="mt-1 block w-full"
                            :class="'question--' + i"
                            autocomplete="off"
                            placeholder="Add Question here"
                        />
                    </div>
                    <div class="w-40 pr-2">
                        <Multiselect
                            class="w-full"
                            mode="single"
                            v-model="questionsData[i].selectedQuestionType"
                            :options="questionTypes"
                            placeholder="Choose an option"
                        />
                    </div>
                    <div class="w-10 ml-4">
                        <span
                            class="text-danger-500 flex items-center cursor-pointer"
                            @click="removeLine(i)"
                        >
                            <DeleteIcon
                                class="w-4 lg:w-5 h-4 lg:h-5 mr-0 md:mr-2"
                            />
                        </span>
                    </div>
                </div>

                <div
                    class="flex flex-row items-center justify-start text-blue-800"
                >
                    <font-awesome-icon class="mx-2" :icon="faPlus" />
                    <label
                        class="mr-3 w-full text-blue-800 cursor-pointer"
                        @click="addQuestion"
                    >
                        Add Question
                    </label>
                </div>
                <div class="flex flex-col space-y-2">
                    <Switcher
                        :class="{
                            'border-red-500': form.errors.is_signature_needed,
                        }"
                        v-model="form.is_signature_needed"
                        title="Signature Needed?"
                        :show-labels="['No', 'Yes']"
                    />
                    <InputError :message="form.errors.is_signature_needed" />
                </div>
                <div class="flex flex-col space-y-2">
                    <Switcher
                        :class="{
                            'border-red-500': form.errors.is_active,
                        }"
                        v-model="form.is_active"
                        title="Active?"
                        :show-labels="['No', 'Yes']"
                    />
                    <InputError :message="form.errors.is_active" />
                </div>
                <div class="flex flex-col space-y-2">
                    <Switcher
                        :class="{
                            'border-red-500': form.errors.sign_again,
                        }"
                        v-model="form.sign_again"
                        title="Old users need to sign this?"
                        :show-labels="['No', 'Yes']"
                    />
                    <InputError :message="form.errors.sign_again" />
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
</template>
<style src="@vueform/multiselect/themes/tailwind.css"></style>
