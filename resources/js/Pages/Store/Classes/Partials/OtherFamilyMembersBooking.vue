<script setup>
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Modal from "@/Components/Modal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import CardBasic from "@/Components/CardBasic.vue";
import Avatar from '@/Components/Avatar.vue';
const props = defineProps(['selected_family_members', 'form', 'show_book_for_other_family_members_modal', 'current_class', 'side_modal_opened', 'user', 'other_family_member_booking_ids'])
const emit = defineEmits(['closeBookForOtherFamilyMembersModal', 'addRemoveFamilyMemberForOtherBookings'])

const alreadyBooked = (isParent, id) => {
    if (isParent) {
        const classDetail = props.current_class
        const filtered = classDetail.user_bookings.filter((item) => {
            return item.user_id == id && item.family_member_id == null
        })
        return filtered.length ? true : false
    } else if (!isParent) {
        const classDetail = props.current_class
        const filtered = classDetail.user_bookings.filter((item) => {
            return item.user_id == props.user.id && item.family_member_id == id
        })
        return filtered.length ? true : false
    }
}
</script>
<template>
    <Modal :show="props.show_book_for_other_family_members_modal" :sideModalOpened="props.side_modal_opened">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-md mx-auto">Select Attendees</div>
                    <div>
                        <CloseModal @click="$emit('closeBookForOtherFamilyMembersModal', false)" v-tooltip="'Cancel and Close'" />
                    </div>
                </div>
            </template>
            <template #default>
                <div class="flex items-center justify-between my-4 mx-4">
                    <div class="flex items-center">
                        <Avatar :imageUrl="user.profile_photo_url" size="medium" :useIcon="true" />
                        <div class="pl-2">
                            <div class="block pl-2 font-semibold mb-2">
                                {{ user.full_name }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex items-center justify-start mr-20">
                        <p class="mr-5 opacity-60 text-sm">
                            {{ alreadyBooked(true, props.user.id) ? 'already booked' : '' }}
                        </p>
                        <input type="checkbox" :class="[alreadyBooked(true, props.user.id) && 'opacity-70']" @change="$emit('addRemoveFamilyMemberForOtherBookings', true, props.user.id)"
                            :checked="alreadyBooked(true, props.user.id)" :disabled="alreadyBooked(true, props.user.id)">
                    </div>
                </div>
                <hr />
                <template v-for="(familyMember, fmindex1) in user.family" :key="fmindex1">
                    <div class="flex items-center justify-between my-4 mx-4">
                        <div class="flex items-center">
                            <Avatar :imageUrl="familyMember.profile_photo_url" size="medium" :useIcon="true" />
                            <div class="pl-2">
                                <div class="block pl-2 font-semibold mb-2">
                                    {{ familyMember.full_name }}
                                </div>
                            </div>
                        </div>
                        <div class="inline-flex items-center justify-start mr-20">
                            <p class="mr-5 opacity-60 text-sm">
                                {{ alreadyBooked(false, familyMember.id) ? 'already booked' : '' }}
                            </p>
                            <input type="checkbox" :class="[alreadyBooked(false, familyMember.id) && 'opacity-70']"  @change="$emit('addRemoveFamilyMemberForOtherBookings', false, familyMember.id)"
                            :checked="alreadyBooked(false, familyMember.id)" :disabled="alreadyBooked(false, familyMember.id)">
                        </div>
                    </div>
                    <hr />
                </template>
            </template>
            <template #footer>
                <div class="text-right">
                    <ButtonLink class="mr-2" styling="default" size="default" @click="$emit('closeBookForOtherFamilyMembersModal', false)">Cancel &
                        Close</ButtonLink>
                    <ButtonLink :disabled="props.other_family_member_booking_ids == null || props.form.processing" styling="secondary" size="default" @click="$emit('closeBookForOtherFamilyMembersModal', true)">Confirm & Book
                    </ButtonLink>
                </div>
            </template>

        </CardBasic>
    </Modal>
</template>