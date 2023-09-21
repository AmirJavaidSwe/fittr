<script setup>
import { ref, watch } from "vue";
import WaiverAcceptCheck from "@/Icons/WaiverAcceptCheck.vue";
import WaiverNotAcceptedCheck from "@/Icons/WaiverNotAcceptedCheck.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import CloseModal from "@/Components/CloseModal.vue";
import SideModal from "@/Components/SideModal.vue";
import WaiverForm from "../../WaiverForm.vue";

const props = defineProps([
    "business_settings",
    "user",
    "waivers",
    "show",
    "edit_form",
    "user_waiver_ids",
    "signed_waiver_ids",
]);
const emit = defineEmits(["close", "updateUserWaiverIds"]);
const showWaiversSideModal = ref(false);
const waiver = ref(null);

const waiverForm = useForm({
    waiverQandA: [],
    sign: null,
});

const showWaiver = () => {
    showWaiversSideModal.value = true;
};

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

const sumbitWaiver = () => {
    waiverForm
        .transform((data) => ({
            data: data.waiverQandA,
            signature: data.sign,
            waiver_id: waiver !== null ? waiver.value?.id : null,
            family_member_id: props.edit_form.id ? props.edit_form.id : null,
        }))
        .post(
            route("ss.member.store.family.waiver", {
                subdomain: props.business_settings.subdomain,
            }),
            {
                preserveScroll: true,
                onSuccess: (res) => {
                    emit("updateUserWaiverIds", {
                        waiver_id: waiver.value.id,
                        user_waiver_id: res.props.extra.user_waiver_id,
                    });
                    waiverForm.reset().clearErrors();
                    waiver.value = null;
                    showWaiversSideModal.value = false;
                },
            }
        );
};

const alreadySigned = (id) => {
    return props.signed_waiver_ids.length &&
        props.signed_waiver_ids.includes(id)
        ? true
        : false;
};

const closedShowWaiver = () => {
    showWaiversSideModal.value = false;
    waiverForm.reset().clearErrors();
};
</script>
<template>
    <SideModal :show="props.show" @close="$emit('close')">
        <template #title>
            <div class="w-full mb-6">
                <p class="text-xl">
                    You just need to check out the following important documents
                    in order to add Family Member.
                </p>
            </div>
        </template>
        <template #close>
            <div class="w-20 text-right">
                <CloseModal @click="$emit('close')" />
            </div>
        </template>

        <template #content>
            <span class="block text-md mb-4"
                >Please view and sign the document</span
            >
            <template v-for="waiver in props.waivers" :key="waiver.id">
                <div class="flex items-center mb-8">
                    <WaiverAcceptCheck v-if="alreadySigned(waiver.id)" />
                    <WaiverNotAcceptedCheck v-else />
                    <div>
                        <span class="block text-xl ml-4">{{
                            waiver.title
                        }}</span>
                    </div>
                    <ButtonLink
                        v-if="!alreadySigned(waiver.id)"
                        styling="secondary"
                        size="default"
                        class="ml-auto"
                        @click="waiverQandData(waiver?.id)"
                    >
                        View
                    </ButtonLink>
                </div>
            </template>
            <div
                class="flex justify-center space-x-4"
                v-if="props.signed_waiver_ids.length == props.waivers.length"
            >
                <ButtonLink
                    styling="secondary"
                    size="default"
                    class="mt-4"
                    @click="$emit('close')"
                >
                    <template v-if="!props.edit_form.id">
                        Go Back and Click Create
                    </template>
                    <template v-else> Go Back and Click Save </template>
                </ButtonLink>
            </div>
        </template>
    </SideModal>

    <!-- Waiver Modal -->
    <SideModal
        :show="showWaiversSideModal"
        @close="showWaiversSideModal = false"
    >
        <template #title>
            <div class="w-full">
                You need to sign "{{ waiver?.title }}" once before adding family
                members
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
</template>
