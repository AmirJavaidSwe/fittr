<script setup>
import ButtonLink from '@/Components/ButtonLink.vue';
import AvatarValue from '@/Components/DataTable/AvatarValue.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SideModal from '@/Components/SideModal.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { onUpdated } from 'vue';

const props = defineProps({
    show: {
        required: true,
        type: Boolean,
        default: false,
    },
    notificationDetails: {
        required: true,
        type: Object,
    },
    previewHtml: {
        required: true,
        type: String,
    },
});

defineEmits(['close']);

const testForm = useForm({
    title: "",
    key: "",
    sg_template_name: "",
    sg_template_id: "",
    placeholders: [],
    mail_driver: "",
    from_name: "",
    from_email: "",
    subject: "",
    content: "",
    content_plain: "",
    content_sms: "",
    unsubscribe: false,
    bypass: false,
    status: true,
    notes: "",
    recipient_email: '',
});

onUpdated(() => {
    testForm.title = props.notificationDetails?.title;
    testForm.key = props.notificationDetails?.key;
    testForm.sg_template_name = props.notificationDetails?.sg_template_name;
    testForm.sg_template_id = props.notificationDetails?.sg_template_id;
    testForm.placeholders = props.notificationDetails?.placeholders;
    testForm.mail_driver = props.notificationDetails?.mail_driver;
    testForm.from_name = props.notificationDetails?.from_name;
    testForm.from_email = props.notificationDetails?.from_email;
    testForm.subject = props.notificationDetails?.subject;
    testForm.content = props.notificationDetails?.content;
    testForm.content_plain = props.notificationDetails?.content_plain;
    testForm.content_sms = props.notificationDetails?.content_sms;
    testForm.unsubscribe = props.notificationDetails?.unsubscribe;
    testForm.bypass = props.notificationDetails?.bypass;
    testForm.status = props.notificationDetails?.status;
    testForm.notes = props.notificationDetails?.notes;
});

const handleTest = () => {
    testForm.post(route('partner.notification-templates.test'), {
        onSuccess: () => {
            testForm.recipient_email = '';
        }
    });
}

</script>
<template>
    <SideModal :show="show" @close="$emit('close')">
        <template #title>Preview Notification Template</template>
        <template #content>
            <div class="mb-4">
                <InputLabel for="">Recipient Email:</InputLabel>
                <TextInput
                    id="recipient_email"
                    v-model="testForm.recipient_email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <InputError :message="testForm.errors?.recipient_email" />
            </div>

            <div class="mb-4">
                <InputLabel>Subject:</InputLabel>
                <div>{{ notificationDetails.subject }}</div>
            </div>

            <div class="mb-4">
                <InputLabel>Content:</InputLabel>
                <iframe
                    :src="'data:text/html;charset=utf-8,'+encodeURIComponent(previewHtml)"
                    class="w-full h-80 mt-2"
                ></iframe>
            </div>

            <div class="flex justify-end">
                <ButtonLink
                    styling="default"
                    size="default"
                    @click="$emit('close')"
                    class="mr-2"
                >
                    <span>Close</span>
                </ButtonLink>

                <ButtonLink
                    :disabled="testForm.processing"
                    styling="secondary"
                    size="default"
                    type="submit"
                    @click="handleTest"
                >
                    <span>Test</span>
                </ButtonLink>
            </div>
        </template>
    </SideModal>
</template>