<script setup>
import CloseModal from '@/Components/CloseModal.vue';
import SideModal from '@/Components/SideModal.vue';
import { ref } from 'vue';
import { onUpdated } from 'vue';
import DataTableLayout from "@/Components/DataTable/Layout.vue";
import TableHead from "@/Components/DataTable/TableHead.vue";
import TableData from "@/Components/DataTable/TableData.vue";
import { watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import { useForm } from '@inertiajs/vue3';
import ButtonLink from '@/Components/ButtonLink.vue';
import { useSwal } from '@/Composables/swal';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import RichTextInput from '@/Components/RichTextInput.vue';
import Multiselect from '@vueform/multiselect';
import axios from 'axios';

const props = defineProps({
    show: {
        required: true,
        type: Boolean,
        default: false,
    },
    classDetails: {
        required: true,
        type: Object,
    },
    business_settings: {
        required: true,
        type: Object,
    },
});

const emit = defineEmits(['close']);

const swal = useSwal();

const steps = ref(['participants', 'email-class', 'message-preview']);
const currentStep = ref(steps.value[0]);
const previewHtml = ref('');

const handleNext = async () => {
    const nextStepIndex = steps.value.indexOf(currentStep.value) + 1;
    if(nextStepIndex >= steps.value.length) return;

    if(steps.value[nextStepIndex] == 'email-class') {
        if (!form.ids?.length) {
            swal.toast({
                icon: 'error',
                title: 'Error!',
                text: 'Please select participants to proceed.',
            });
            return;
        }
        currentStep.value = steps.value[nextStepIndex];
    } else if(steps.value[nextStepIndex] == 'message-preview') {
        processing.value = true;
        const res = await axios.post(route('partner.notification-templates.preview', {content: form.content}))
            .catch(console.error);
        previewHtml.value = res.data;
        processing.value = false;
        currentStep.value = steps.value[nextStepIndex];
    } else {
        currentStep.value = steps.value[nextStepIndex];
    }
};

const handleBack = () => {
    const previousStepIndex = steps.value.indexOf(currentStep.value) - 1;
    if(previousStepIndex < 0) return;
    currentStep.value = steps.value[previousStepIndex];
};

const handleFinish = () => {
    if (!form.ids?.length) {
        swal.toast({
            icon: 'error',
            title: 'Error!',
            text: 'Please select participants to proceed.',
        });
        return;
    }

    form.post(route('partner.classes.email-class', props.classDetails?.id), {
        onSuccess: () => {
            participants.value = [];
            currentStep.value = steps.value[0];
            previewHtml.value = '';
            form.reset();
            classId.value = null;
            emit('close');
        }
    });
}

const classId = ref(null);
const participants = ref([]);
const processing = ref(false);

const getData = async () => {
    participants.value = [];
    processing.value = true;
    let res = await axios.get(route('partner.classes.participants', {class: props.classDetails?.id})).catch(console.error);

    participants.value = res.data.participants;

    let template = res.data?.template;

    form.template_id = template?.id;
    form.placeholders = template?.placeholders;
    form.subject = template?.subject;
    form.content = template?.content;
    form.content_plain = template?.content_plain;
    processing.value = false;
};

onUpdated(() => {
    if(classId.value != props.classDetails?.id) {
        participants.value = [];
        currentStep.value = steps.value[0];
        previewHtml.value = '';
        form.reset();
        classId.value = props.classDetails?.id;
    }
})

watch(classId, getData);

const form = useForm({
    ids: [],
    template_id: null,
    placeholders: [],
    subject: '',
    content: '',
    content_plain: '',
});

const selectAllCheckboxes = () => {
    const ids = participants.value.map(item => item.user?.id);
    form.ids = form.ids.length ? [] : ids;
};

</script>
<template>
    <SideModal :show="show" @close="$emit('close')">
        <template #title>Email class participants</template>
        <template #close>
            <CloseModal @click="$emit('close')" />
        </template>
        <template #content>
            <div v-if="currentStep == 'participants'">
                <div class="flex flex-col">
                    <div class="flex flex-col mb-4">
                        <div class="flex mr-4 font-bold">Class Title</div>
                        <div class="flex flex-grow mr-4">{{ classDetails.title }}</div>
                    </div>

                    <div class="flex mb-4">
                        <div class="flex mr-4 font-bold">Participants</div>
                    </div>
                    <div class="overflow-y-scroll h-80">
                        <data-table-layout
                            :disableButton="true"
                        >
                            <template #tableHead>
                                <table-head>
                                    <template #checkbox>
                                        <Checkbox
                                            :checked="form.ids.length > 0"
                                            class="p-2.5"
                                            @click="selectAllCheckboxes"
                                        />
                                    </template>
                                </table-head>
                                <table-head
                                    title="Name"
                                />
                                <table-head
                                    title="Email"
                                />
                            </template>
                            <template #tableData>
                                <tr
                                    v-for="(participant, index) in participants"
                                    :key="participant?.user?.id"
                                >
                                    <table-data>
                                        <div>
                                            <label class="flex items-center">
                                                <Checkbox
                                                    v-model:checked="form.ids"
                                                    :checked="form.ids.includes(participant?.user?.id)"
                                                    :value="participant?.user?.id"
                                                />
                                            </label>
                                        </div>
                                    </table-data>
                                    <table-data>
                                        {{ participant?.user?.name }}
                                    </table-data>
                                    <table-data>
                                        {{ participant?.user?.email }}
                                    </table-data>
                                </tr>
                            </template>
                        </data-table-layout>
                    </div>
                </div>
            </div>
            <div v-if="currentStep == 'email-class'">
                <div class="my-3" v-if="form.placeholders?.length">
                    <InputLabel value="Placeholders used" />
                    <div class="bg-gray-700 text-white p-2 rounded-lg mt-2">{{ form.placeholders.join(', ') }}</div>
                </div>
                <div class="my-3">
                    <InputLabel for="subject" value="Subject" />
                    <TextInput
                        id="title"
                        v-model="form.subject"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.subject" class="mt-2" />
                </div>

                <div class="my-3">
                    <InputLabel for="content" value="Content" />
                    <RichTextInput v-model="form.content" @setPlainText="data => (form.content_plain = data)" />
                    <InputError :message="form.errors.content" class="mt-2" />
                </div>
            </div>

            <div v-if="currentStep == 'message-preview'">
                <div class="mb-4">
                    <InputLabel>Subject:</InputLabel>
                    <div>{{ form.subject }}</div>
                </div>

                <div class="mb-4">
                    <InputLabel>Content:</InputLabel>
                    <iframe
                        :src="'data:text/html;charset=utf-8,'+encodeURIComponent(previewHtml)"
                        class="w-full h-80 mt-2"
                    ></iframe>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                 <ButtonLink
                    v-if="currentStep != steps[0]"
                    class="mr-3"
                    :disabled="form.processing"
                    styling="default"
                    size="default"
                    type="button"
                    @click="handleBack"
                >
                    Back
                </ButtonLink>

                <ButtonLink
                    v-if="currentStep == steps[steps.length-1]"
                    :disabled="form.processing"
                    styling="secondary"
                    size="default"
                    type="button"
                    @click="handleFinish"
                >
                    Finish
                </ButtonLink>
                <ButtonLink
                    v-else
                    :disabled="form.processing || processing"
                    styling="secondary"
                    size="default"
                    type="button"
                    @click="handleNext"
                >
                    Next
                </ButtonLink>
            </div>
        </template>
    </SideModal>
</template>
