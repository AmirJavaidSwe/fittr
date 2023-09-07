<script setup>
import { ref, computed } from "vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Textarea from "@/Components/Textarea.vue";
import Radio from "@/Components/Radio.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SignaturePad from "@/Components/SignaturePad.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";



const signaturePad = ref(null);

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    waiver: Object,
    createForm: {
        type: Object,
        default: {}
    },
    editForm: {
        type: Object,
        default: {}
    },
});

const onSignatureUpdate = ($event) => {
    props.form.sign = $event;
};

const answerError = computed(() => {
    return (props?.createForm?.errors?.answer_error || props?.editForm?.errors?.answer_error) ? true : false
})

const signatureError = computed(() => {
    return {
        error: (props?.createForm?.errors?.signature_error || props?.editForm?.errors?.signature_error) ? true : false,
        msg: (props?.createForm?.errors?.signature_error || props?.editForm?.errors?.signature_error)
    }
})

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
            <div
                v-for="(obj, index) in form.waiverQandA"
                class="items-center w-full"
                :key="index"
            >
                <template v-if="obj.type === 'Yes/No'">
                    <div class="flex w-full justify-between mb-2 mt-10">
                        <InputLabel :value="obj.question" />
                        <div class="inline-flex mr-5">
                            <Radio
                                v-model="form.waiverQandA[index].answer"
                                :value="'Yes'"
                                class="mr-2"
                                :id="'yes--' + index"
                            />
                            <InputLabel
                                :value="'Yes'"
                                :for="'yes--' + index"
                                class="mr-6"
                                :name="obj.question"
                            />
                            <Radio
                                v-model="form.waiverQandA[index].answer"
                                :value="'No'"
                                class="mr-2"
                                :id="'no--' + index"
                                :name="obj.question"
                            />
                            <InputLabel :value="'No'" :for="'no--' + index" />
                        </div>
                    </div>
                    <InputError v-if="answerError && !form.waiverQandA[index].answer" :message="'Required'" class="" />
                </template>
                <template v-else-if="obj.type === 'Checkbox'">
                    <div class="flex justify-between items-center mb-2 mt-10">
                        <InputLabel :value="obj.question" :for="obj.question" />
                        <Checkbox
                            :id="obj.question"
                            class="mr-5"
                            :checked="form.waiverQandA[index].answer === true"
                            v-model="form.waiverQandA[index].answer"
                        />
                    </div>
                    <InputError v-if="answerError && !form.waiverQandA[index].answer" :message="'Required'" class="" />
                </template>
                <template v-else-if="obj.type === 'Free Text'">
                    <div class="mb-2 mt-10">
                        <div class="block items-center mb-2 mt-2">
                            <InputLabel :value="obj.question" />
                        </div>
                        <div class="block items-center mb-2 mt-2">
                            <Textarea
                                v-model="form.waiverQandA[index].answer"
                                placeholder="Enter your answer here..."
                                class="w-full"
                                rows="5"
                                cols="10"
                            />
                        </div>
                        <InputError v-if="answerError && !form.waiverQandA[index].answer" :message="'Required'" class="" />
                    </div>
                </template>
            </div>
            <div v-if="waiver.is_signature_needed">
                <div class="w-full flex items-center">
                    <InputLabel :value="'Signature'" />
                </div>
                <div class="w-full mt-12 mb-2">
                    <SignaturePad @on-signature-update="onSignatureUpdate" :sign="form.sign" />
                    <InputError v-if="signatureError.error" :message="signatureError.msg" class="mb-10" />
                </div>
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mt-10 mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                :class="{ 'opacity-25': form.processing } + 'mt-10'"
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
            >
                <span>Save</span>
            </ButtonLink>
        </template>
    </FormSection>
</template>
