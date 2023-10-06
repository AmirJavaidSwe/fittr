<script setup>

import Form from "./Form.vue";
import { useForm } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import FormSection from "@/Components/FormSection.vue";
import PreviewNotificationTemplate from "./PreviewNotificationTemplate.vue";
import { ref } from "vue";
import axios from "axios";

const updateNotificationTemplate = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('partner.notification-templates.update', props.notificationTemplate), {
        preserveScroll: true,
    });
};

let props = defineProps({
    notificationTemplate: {
        type: Object,
        required: true
    },
    users: Array,
    amenities: Array,
    countries: Array,
    studios: Array,
});

let form = useForm({
    title: props.notificationTemplate?.title,
    key: props.notificationTemplate?.key,
    sg_template_name: props.notificationTemplate?.sg_template_name,
    sg_template_id: props.notificationTemplate?.sg_template_id,
    placeholders: [...props.notificationTemplate?.placeholders],
    mail_driver: props.notificationTemplate?.mail_driver,
    from_name: props.notificationTemplate?.from_name,
    from_email: props.notificationTemplate?.from_email,
    subject: props.notificationTemplate?.subject,
    content: props.notificationTemplate?.content,
    content_plain: props.notificationTemplate?.content_plain,
    content_sms: props.notificationTemplate?.content_sms,
    unsubscribe: props.notificationTemplate?.unsubscribe,
    bypass: props.notificationTemplate?.bypass,
    status: props.notificationTemplate?.status,
    readonly: props.notificationTemplate?.readonly,
    notes: props.notificationTemplate?.notes,
});

const preview = ref(false);
const preivewProcessing = ref(false);
const previewData = ref({});

const showPreview = async () => {
    preivewProcessing.value = true;
    const res = await axios.post(route('partner.notification-templates.preview'), { ...form.data() })
        .catch(console.error);

    previewData.value = res.data;
    preview.value = true;
    preivewProcessing.value = false;
}

</script>

<template>
    <FormSection>
        <template #form>
            <Form
                :form="form"
            />
        </template>
        <template #actions>
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
                :disabled="form.processing"
                styling="secondary"
                size="default"
                type="submit"
                @click="updateNotificationTemplate"
            >
                <span>Save changes</span>
            </ButtonLink>
        </template>
    </FormSection>
    <PreviewNotificationTemplate
        :show="preview"
        :notificationDetails="form"
        :previewData="previewData"
        @close="preview = false"
    />
</template>