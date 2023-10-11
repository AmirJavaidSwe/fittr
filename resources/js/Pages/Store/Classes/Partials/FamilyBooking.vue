<script setup>
import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import Modal from "@/Components/Modal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import CardBasic from "@/Components/CardBasic.vue";
import Avatar from '@/Components/Avatar.vue';
const props = defineProps(['selected_family_members', 'show_family_booking_modal', 'side_modal_opened', 'user'])
const emit = defineEmits(['closeAddFamilyModal', 'addRemoveFamilyMember'])

const checkSelectedFamilyMembers = (id) => {
    const filtered = props.selected_family_members.family_member.ids.filter((item) => item == id)
    return filtered.length ? true : false
}

</script>
<template>
    <Modal :show="props.show_family_booking_modal" :sideModalOpened="props.side_modal_opened">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center">
                    <div class="text-md mx-auto">Select Attendees</div>
                    <div>
                        <CloseModal @click="$emit('closeAddFamilyModal', false)" v-tooltip="'Cancel and Close'" />
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
                        <input type="checkbox" @change="$emit('addRemoveFamilyMember', true, props.user.id)"
                            :checked="props.selected_family_members.user_id !== null ? true : false">
                    </div>
                </div>
                <hr />
                <template v-for="(familyMember, fmIndex) in user.family" :key="fmIndex">
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
                            <input type="checkbox" @change="$emit('addRemoveFamilyMember', false, familyMember.id)"
                                :checked="checkSelectedFamilyMembers(familyMember.id)">
                        </div>
                    </div>
                    <hr />
                </template>
            </template>
            <template #footer>
                <div class="text-right">
                    <ButtonLink class="mr-2" styling="default" size="default" @click="$emit('closeAddFamilyModal', false)">Cancel &
                        Close</ButtonLink>
                    <ButtonLink styling="secondary" size="default" @click="$emit('closeAddFamilyModal', true)">Confirm & Close
                    </ButtonLink>
                </div>
            </template>

        </CardBasic>
    </Modal>
</template>