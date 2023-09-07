<script setup>
import Section from "@/Components/Section.vue";
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
import WaiverForm from "../WaiverForm.vue";

const props = defineProps({
    business_settings: Object,
    form_data: Object,
    page_title: String,
    submit_to_route: String,
    waiver: {
        type: Object,
        required: true,
    },
});
const form = useForm({
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        request_data: props.form_data,
        returnTo: 'ss.classes.index',
        subdomain: props.business_settings.subdomain,
        waiver_id: props.waiver !== null ? props.waiver.id : null,
        waiver_data:
            props.waiver !== null
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
};

const answerError = computed(() => {
    return props?.form?.errors?.answer_error ? true : false;
});

const signatureError = computed(() => {
    return {
        error: props?.form?.errors?.signature_error ? true : false,
        msg:
            props?.form?.errors?.signature_error ||
            props?.form?.errors?.signature_error,
    };
});

const onSignatureUpdate = ($event) => {
    form.signature = $event;
};

const waiverFormData = useForm({
    waiverQandA: [],
    sign: props?.user_waiver?.length ? props?.user_waiver?.signature : null,
});

const waiverQandData = () => {
    waiverFormData.waiverQandA = [];
    if (props.waiver) {
        for (let i = 0; i < props.waiver.questions.length; i++) {
            waiverFormData.waiverQandA.push({
                question: props.waiver.questions[i].question,
                type: props.waiver.questions[i].selectedQuestionType,
                answer: null,
            });
        }
    }
};

waiverQandData();
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">Waiver Verification</div>
        <div class="w-1/2">
            <WaiverForm :form="waiverFormData" :create-form="form" :submitted="submit" :waiver="props.waiver" />
        </div>
    </Section>
</template>
