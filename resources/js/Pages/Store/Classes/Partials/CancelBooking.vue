<script setup>
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Modal from "@/Components/Modal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import CardBasic from "@/Components/CardBasic.vue";


const props = defineProps(['show_cancel_booking_modal', 'booking_form', 'side_modal_opened', 'user', 'current_class', 'ids_cancellation'])
const emit = defineEmits(['hideCancelBookingModal', 'addRemoveMemberForCancellation', 'cancelBookingAll'])

</script>
<template>

<Modal :show="props.show_cancel_booking_modal" :sideModalOpened="props.side_modal_opened">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-md mx-auto">Select Attendees for Cancellation</div>
                    <div>
                        <CloseModal @click="$emit('hideCancelBookingModal', false)" v-tooltip="'Cancel and Close'" />
                    </div>
                </div>
            </template>
            <template #default>
                <div class="flex items-center justify-between my-4 mx-4">
                    <div class="flex items-center">
                        <img :src="props.user.profile_photo_url" :alt="props.user.name" class="rounded-full h-10 w-10 object-cover" />
                        <div class="pl-2">
                            <div class="block pl-2 font-semibold mb-2">
                                {{ props.user.name }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex items-center justify-start mr-20">
                        <input type="checkbox" @change="$emit('addRemoveMemberForCancellation', true, props.user.id)">
                    </div>
                </div>
                <hr />
                <template v-for="(familyMember, fmIndex2) in props.user.family" :key="fmIndex2">
                    <div class="flex items-center justify-between my-4 mx-4">
                        <div class="flex items-center">
                            <img :src="familyMember.profile_photo_url" :alt="familyMember.name"
                                class="rounded-full h-10 w-10 object-cover" />
                            <div class="pl-2">
                                <div class="block pl-2 font-semibold mb-2">
                                    {{ familyMember.name }}
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex items-center justify-start mr-20">
                            <input type="checkbox" @change="$emit('addRemoveMemberForCancellation', false, familyMember.id)">
                        </div>
                    </div>
                    <hr />
                </template>
            </template>
            <template #footer>
                <div class="text-right">
                    <ButtonLink class="mr-2" styling="default" size="default" @click="$emit('hideCancelBookingModal', false)">Cancel &
                        Close</ButtonLink>
                    <ButtonLink :disabled="props.ids_cancellation == null || props.booking_form.processing" styling="secondary" size="default" @click="$emit('cancelBookingAll')">Confirm & Cancel
                    </ButtonLink>
                </div>
            </template>

        </CardBasic>
    </Modal>
</template>