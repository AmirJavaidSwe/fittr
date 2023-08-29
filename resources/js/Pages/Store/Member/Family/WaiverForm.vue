<script setup>
import { computed, watch } from "vue";
import "@vuepic/vue-datepicker/dist/main.css";
import "@vueform/multiselect/themes/tailwind.css";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Textarea from "@/Components/Textarea.vue";
import Radio from "@/Components/Radio.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    waiver: Object | null,
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form v-if="waiver !== null">
            <div class="" v-if="waiver.title">
                <InputLabel value="Title" />
                <p>{{ waiver.title }}</p>
            </div>
            <div class="" v-if="waiver.description">
                <InputLabel value="Description" />
                <p>{{ waiver.description }}</p>
            </div>
            <div class="w-full flex items-center">
                <div class="w-full">
                    <div class="bg-blue-100">
                        <label for="question" class="block font-medium p-2"
                            >Questions</label
                        >
                    </div>
                </div>
                <div class="w-10"></div>
            </div>
            <div
                v-for="(question, index) in waiver.questions"
                class="items-center w-full"
                :key="index"
            >
                <InputLabel :value="question.question" />
                <template v-if="question.selectedQuestionType === 'Yes/No'">
                    <div class="flex items-center mb-2 mt-2">
                        <Radio
                            v-model="question.selectedAnswer"
                            :value="'Yes'"
                            class="mr-2"
                            id="yes"
                        />
                        <InputLabel :value="'Yes'" for="yes" class="mr-6" />
                        <Radio
                            v-model="question.selectedAnswer"
                            :value="'No'"
                            class="mr-2"
                            id="no"
                        />
                        <InputLabel :value="'No'" for="no" />
                    </div>
                </template>
                <template v-else-if="question.selectedQuestionType === 'Checkbox'">
                    <Checkbox
                        v-model="question.selectedAnswers"
                        :options="question.options"
                    />
                </template>
                <template v-else-if="question.selectedQuestionType === 'Free Text'">
                    <Textarea
                        v-model="question.answerText"
                        placeholder="Enter your answer here..."
                        class="w-full"
                    />
                </template>
            </div>


        </template>
    </FormSection>
</template>