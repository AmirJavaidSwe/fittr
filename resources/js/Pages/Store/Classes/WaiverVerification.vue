<script setup>
import Section from "@/Components/Section.vue";
import { ref, computed } from "vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextArea from "@/Components/TextArea.vue";
import Radio from "@/Components/Radio.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SignaturePad from "@/Components/SignaturePad.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import DialogModal from "@/Components/DialogModal.vue";
import WaiverAcceptCheck from "@/Icons/WaiverAcceptCheck.vue";
import WaiverNotAcceptedCheck from "@/Icons/WaiverNotAcceptedCheck.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import WaiverForm from "../WaiverForm.vue";

const props = defineProps({
    business_settings: Object,
    form_data: Object,
    page_title: String,
    submit_to_route: String,
    waivers: {
        type: Object,
        required: true,
    },
    user:{
        type:Object,
        required:true,
    },
    user_waivers:{
        type:Object,
        required:true,
    }
});
const form = useForm({
    waiver:'',
    checkWaiver:'',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        request_data: props.form_data,
        returnTo: 'ss.classes.index',
        subdomain: props.business_settings.subdomain,
        waiver_id: form.waiver !== null ? form.waiver.id : null,
        waiver_data:
            form.waiver !== null
                ? {
                    data: waiverFormData.waiverQandA,
                    signature: waiverFormData.sign ?? null,
                }
                : {},
    })).post(
        route(`${props.submit_to_route}`, {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
        }
    );
    form.checkWaiver = false;
};

// const answerError = computed(() => {
//     return props?.form?.errors?.answer_error ? true : false;
// });

// const signatureError = computed(() => {
//     return {
//         error: props?.form?.errors?.signature_error ? true : false,
//         msg:
//             props?.form?.errors?.signature_error ||
//             props?.form?.errors?.signature_error,
//     };
// });

// const onSignatureUpdate = ($event) => {
//     form.signature = $event;
// };

const waiverFormData = useForm({
    waiverQandA: [],
    sign: props?.user_waiver?.length ? props?.user_waiver?.signature : null,
});

const waiverQandData = (id) => {
    form.checkWaiver = true;
    form.waiver = props.waivers.find((item) => item.id == id);
    waiverFormData.waiverQandA = [];
    if (form.waiver) {
        for (let i = 0; i < form.waiver.questions.length; i++) {
            waiverFormData.waiverQandA.push({
                question: form.waiver.questions[i].question,
                type: form.waiver.questions[i].selectedQuestionType,
                answer: null,
            });
        }
    }
};
const closeModal = () => {
    form.checkWaiver = false;
}


</script>

<template>
        <div class="text-3xl mb-4 text-center">Waiver Verification</div>
        <Section bg="bg-transparent" class="flex flex-col justify-center items-center text-center bg-transparent ">
            <div class="bg-white rounded-lg shadow-md p-6 w-1/2">
                <p class="text-3xl mb-4">Welcome <span class="font-bold">{{ user?.name }}!</span></p>
                <p class="text-2xl mb-12">You just need to check out the following important documents in order to Book Classes.</p>
                <div class="flex items-center mt-4" v-for="waiver in props.waivers" :key="waiver.id">

                    <WaiverAcceptCheck  v-if="props?.user_waivers?.find((item) => item.waiver_id === waiver.id) !==
                        undefined ? true : false " />
                    <WaiverNotAcceptedCheck v-if="props?.user_waivers?.find((item) => item.waiver_id === waiver.id) ==
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
            </div>
            <div class="flex justify-center space-x-4">
                <ButtonLink
                  styling="secondary"
                  size="default"
                  class="mt-4"
                  href="/gotodashboard"
                >
                  Book Class
                </ButtonLink>
                <ButtonLink
                  styling="secondary"
                  size="default"
                  class="mt-4"
                  :href="route('login')"
                >
                  Go To Back
                </ButtonLink>
            </div>
        </Section>
        <!-- <div class="w-1/2">
            <WaiverForm :form="waiverFormData" :create-form="form" :submitted="submit" :waiver="props.waiver" />
        </div> -->
        <DialogModal :show="form.checkWaiver" @close="closeModal">
            <template #title>
                <div class="relative">
                    <button class="absolute top-0 right-0 mt-2 mr-2 text-white hover:text-gray-300" @click="closeModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="w-[600px] h-[122px] p-4 gap-2 bg-[#42705F] text-2xl text-center text-white flex justify-center items-center">
                        {{ form.waiver?.title }}
                    </div>
                </div>
            </template>

            <template #content>
                <WaiverForm :form="waiverFormData" :create-form="form" :submitted="submit" :waiver="form.waiver"  />
            </template>
        </DialogModal>
</template>
