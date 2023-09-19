<script setup>
import Section from "@/Components/Section.vue";
import { ref, computed } from "vue";
import DialogModal from "@/Components/DialogModal.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import WaiverAcceptCheck from "@/Icons/WaiverAcceptCheck.vue";
import WaiverNotAcceptedCheck from "@/Icons/WaiverNotAcceptedCheck.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import WaiverForm from "./WaiverForm.vue";
import SideModal from "@/Components/SideModal.vue";

const props = defineProps({
    business_settings: Object,
    waivers: {
        type: Object,
        required: true,
    },
    user_waivers: {
        type: Object,
        required: false,
    },
});
const user = ref(usePage().props.user);

const form = useForm({
    checkWaiver: false,
    waiver: "",
});
const closeModal = () => {
    form.checkWaiver = false;
};
const submit = () => {
    form.transform((data) => ({
        ...data,
        waiver_id: form.waiver !== null ? form.waiver.id : null,
        waiver_data:
            props.form !== null
                ? {
                      data: waiverFormData.waiverQandA,
                      signature: waiverFormData.sign ?? null,
                  }
                : {},
    })).post(
        route("ss.store.waiver-verification", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
        }
    );
    form.checkWaiver = false;
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

const hasAcceptedWaiver = computed(() => {
    return (
        props.user_waivers &&
        props.user_waivers.find((item) => item.waiver_id === form.waiver.id) !==
            undefined
    );
});

const hasNotAcceptedWaiver = computed(() => {
    return !hasAcceptedWaiver.value;
});
const signed_waiver_ids = ref([]);
const user_waiver_ids = ref([]);

const alreadySigned = (id) => {
    return signed_waiver_ids.value.length &&
        signed_waiver_ids.value.includes(id)
        ? true
        : false;
};

const waiverForm = useForm({
    waiverQandA: [],
    sign: props?.user_waivers?.length ? props?.user_waivers?.signature : null,
});

const waiver = ref(null);
const waiverQandData = (id) => {
    waiver.value = props.waivers.find((item) => item.id == id);
    waiverForm.waiverQandA = [];
    if (waiver.value) {
        for (let i = 0; i < waiver.value?.questions.length; i++) {
            waiverForm.waiverQandA.push({
                question: waiver.value?.questions[i].question,
                type: waiver.value?.questions[i].selectedQuestionType,
                answer: null,
            });
        }
        showWaiver();
    }
};
const showWaiversSideModal = ref(false);
const showWaiver = () => {
    showWaiversSideModal.value = true;
};

const sumbitWaiver = () => {
    waiverForm
        .transform((data) => ({
            data: data.waiverQandA,
            signature: data.sign,
            waiver_id: waiver !== null ? waiver.value?.id : null,
            family_member_id: null,
        }))
        .post(
            route("ss.store.waiver-verification", {
                subdomain: props.business_settings.subdomain,
            }),
            {
                preserveScroll: true,
                onSuccess: (res) => {
                    signed_waiver_ids.value.push(waiver.value.id);
                    user_waiver_ids.value.push(res.props.extra.user_waiver_id);
                    waiverForm.reset().clearErrors();
                    waiver.value = null;
                    showWaiversSideModal.value = false;
                },
            }
        );
};
</script>

<template>
    <div class="mt-8 flex justify-center">
        <div class="bg-white rounded-lg shadow-md p-6 w-1/2">
            <p class="text-3xl mb-4">
                Welcome <span class="font-bold">{{ user?.name }}!</span>
            </p>
            <p class="text-xl mb-12">
                You just need to check out the following important documents in
                order to complete registration.
            </p>
            <div
                class="flex items-center mt-4"
                v-for="waiver in props.waivers"
                :key="waiver.id"
            >
                <WaiverAcceptCheck v-if="alreadySigned(waiver.id)" />
                <WaiverNotAcceptedCheck v-else />
                <div>
                    <span class="block text-xl ml-4">{{ waiver.title }}</span>
                    <span class="block text-sm ml-4"
                        >Please read and sign the document</span
                    >
                </div>
                <ButtonLink
                    v-if="!alreadySigned(waiver.id)"
                    styling="secondary"
                    size="default"
                    class="mt-4 ml-auto"
                    @click="waiverQandData(waiver?.id)"
                >
                    View
                </ButtonLink>
            </div>
        </div>
    </div>
    <div class="flex justify-center space-x-4">
        <ButtonLink
            v-if="signed_waiver_ids.length == props.waivers.length"
            styling="secondary"
            size="default"
            class="mt-4"
            :href="
                route('ss.member.dashboard', props.business_settings.subdomain)
            "
        >
            Go To Dashboard
        </ButtonLink>
    </div>

    <!-- Waiver Modal -->
    <SideModal
        :show="showWaiversSideModal"
        @close="showWaiversSideModal = false"
    >
        <template #title>
            <div class="w-full">
                You need to sign "{{ waiver?.title }}" once in
                order to complete registration.
            </div>
        </template>
        <template #close>
            <div class="w-20 text-right">
                <CloseModal @click="showWaiversSideModal = false" />
            </div>
        </template>

        <template #content>
            <WaiverForm
                :form="waiverForm"
                :submitted="sumbitWaiver"
                :waiver="waiver"
                modal
            />
        </template>
    </SideModal>
    <!--
    <DialogModal :show="form.checkWaiver" @close="closeModal">
        <template #title>
            <div class="relative">
                <button
                    class="absolute top-0 right-0 mt-2 mr-2 text-white hover:text-gray-300"
                    @click="closeModal"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
                <div
                    class="w-[600px] h-[122px] p-4 gap-2 bg-[#42705F] text-2xl text-center text-white flex justify-center items-center"
                >
                    {{ form.waiver?.title }}
                </div>
            </div>
        </template>

        <template #content>
            <WaiverForm
                :form="waiverFormData"
                :create-form="form"
                :submitted="submit"
                :waiver="form.waiver"
            />
        </template>
    </DialogModal> -->
</template>
