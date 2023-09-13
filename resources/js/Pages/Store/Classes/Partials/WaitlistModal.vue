<script setup>
import ButtonLink from "@/Components/ButtonLink.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, onBeforeMount, onMounted, ref, watch } from "vue";
import { useSwal } from "@/Composables/swal";
import Modal from "@/Components/Modal.vue";
import CloseModal from "@/Components/CloseModal.vue";
import CardBasic from "@/Components/CardBasic.vue";
import { storeClassHelper } from "./../Helpers/storeClassHelper.js";

const emit = defineEmits(["hide", "hideBoth", "disableButton", "enableButton"]);

const show = ref(false);

const user = ref(usePage().props.user);

const swal = useSwal();

const helper = storeClassHelper();

const disableButton = ref(false)

const props = defineProps({
    modal: {
        type: Boolean,
        required: false,
        default: false,
    },
    classDetail: Object,
    addRemoveUsersToWaitlist: Number,
    businessSettings: Object,
});

const waitlistForm = useForm({ class_id: "", data: null });

const waitlistIds = ref([]);

const addRemove = (isParent, id) => {
    waitlistIds.value = helper.addRemove(isParent, id, waitlistIds.value);
    disableButton.value = true
};

const storeWaitlist = () => {
    emit('disableButton')
    waitlistForm
    .transform((data) => ({
        ...data,
        members: waitlistIds.value,
        }))
        .post(
            route("ss.member.bookings.add-to-waitlist", {
                subdomain: props.businessSettings.subdomain,
            }),
            {
                onSuccess: (res) => {
                    emit('enableButton')
                    emit("hideBoth");
                },
            }
        );
};
const userHasFamily = computed(() => {
    return user.value?.family?.length ? true : false;
});
const processWaitlist = () => {
    waitlistForm.class_id = props.classDetail.id;

    if (props.addRemoveUsersToWaitlist === 1) {
        swal.messageBox({
            title: "This class is full",
            text: "You can still book to waitlist. We will inform you if space becomes available.",
        }).then((result) => {
            storeWaitlist();
        })
    } else if (props.addRemoveUsersToWaitlist == 2) {
        if (userHasFamily.value) {
            show.value = true;
        } else {
            storeWaitlist();
        }
    } else if (props.addRemoveUsersToWaitlist == 3) {
        removeFromWaitlist()
    }
};

onMounted(() => {
    processWaitlist();
});

const removeFromWaitlist = () => {
    emit('disableButton')
    waitlistForm.class_id = props.classDetail.id;

    waitlistForm.post(
        route("ss.member.bookings.remove-from-waitlist", {
            subdomain: props.businessSettings.subdomain,
        }),
        {
            onSuccess: (res) => {
                emit('enableButton')
                emit("hideBoth");
            },
        }
    );
};

const userAlreadyInWaitlist = helper.alreadyInWaitlist(
    true,
    user.value.id,
    props.classDetail,
    user.value
);

if(userAlreadyInWaitlist) {
    waitlistIds.value.push({
        id: user.value?.id,
        is_parent: true,
    });
}

const userAlreadyBooked = helper.alreadyBooked(
    true,
    user.value.id,
    props.classDetail,
    user.value
);

const familyMembersAlreadyInWaitlist = user.value.family.map((familyMember) => {

    const exists = helper.alreadyInWaitlist(
        false,
        familyMember.id,
        props.classDetail,
        user.value
    )
    if(exists) {
        waitlistIds.value.push({
            id: familyMember.id,
            is_parent: false,
        });
    }
    return exists
});

const familyMembersAlreadyBooked = user.value.family.map((familyMember) =>
    helper.alreadyBooked(false, familyMember.id, props.classDetail, user.value)
);

const userCheckboxCheckedIfWaitlist = computed(() => userAlreadyInWaitlist);

const userCheckboxCheckedIfBooked = computed(() => userAlreadyBooked);

const familyMembersCheckboxCheckedIfWaitlist = computed(
    () => familyMembersAlreadyInWaitlist
);

const familyMembersCheckboxCheckedIfBooked = computed(
    () => familyMembersAlreadyBooked
);

console.log(waitlistIds.value)
</script>
<template>
    <Modal :show="show" :sideModalOpened="modal">
        <CardBasic>
            <template #header>
                <div class="flex justify-between items-center mb-5">
                    <div class="text-md mx-auto">
                        Select attendees for waitlist
                    </div>
                    <div>
                        <CloseModal
                            @click="$emit('hide')"
                            v-tooltip="'Cancel and Close'"
                        />
                    </div>
                </div>
                <hr />
            </template>
            <template #default>
                <div class="flex items-center justify-between my-4 mx-4">
                    <div class="flex items-center">
                        <img
                            :src="user.profile_photo_url"
                            :alt="user.name"
                            class="rounded-full h-10 w-10 object-cover"
                        />
                        <div class="pl-2">
                            <div class="block pl-2 font-semibold mb-2">
                                {{ user.name }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex items-center justify-start mr-20">
                        <p class="mr-5 opacity-60 text-sm">
                            <template v-if="userCheckboxCheckedIfWaitlist">
                                Already in waitlist <br />
                                uncheck to remove.
                            </template>
                            <template v-if="userCheckboxCheckedIfBooked">
                                User already booked for this class
                            </template>
                        </p>
                        <input
                            :class="[
                                userCheckboxCheckedIfBooked && 'opacity-70',
                            ]"
                            type="checkbox"
                            @change="addRemove(true, user.id)"
                            :checked="
                                userCheckboxCheckedIfBooked ||
                                userCheckboxCheckedIfWaitlist
                            "
                            :disabled="userCheckboxCheckedIfBooked"
                        />
                    </div>
                </div>
                <hr />
                <template v-for="(familyMember, index) in user.family" :key="index">
                    <div class="flex items-center justify-between my-4 mx-4">
                        <div class="flex items-center">
                            <img
                                :src="familyMember.profile_photo_url"
                                :alt="familyMember.name"
                                class="rounded-full h-10 w-10 object-cover"
                            />
                            <div class="pl-2">
                                <div class="block pl-2 font-semibold mb-2">
                                    {{ familyMember.name }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="inline-flex items-center justify-start mr-20"
                        >
                            <p class="mr-5 opacity-60 text-sm">
                                <template
                                    v-if="
                                        familyMembersCheckboxCheckedIfWaitlist[
                                            index
                                        ]
                                    "
                                >
                                    Already in waitlist <br />
                                    uncheck to remove.
                                </template>
                                <template
                                    v-if="
                                        familyMembersCheckboxCheckedIfBooked[
                                            index
                                        ]
                                    "
                                >
                                    User already booked for this class
                                </template>
                            </p>
                            <input
                                :class="[
                                    familyMembersCheckboxCheckedIfBooked[
                                        index
                                    ] && 'opacity-70',
                                ]"
                                type="checkbox"
                                @change="addRemove(false, familyMember.id)"
                                :checked="
                                    familyMembersCheckboxCheckedIfWaitlist[
                                        index
                                    ] ||
                                    familyMembersCheckboxCheckedIfBooked[index]
                                "
                                :disabled="
                                    familyMembersCheckboxCheckedIfBooked[index]
                                "
                            />
                        </div>
                    </div>
                    <hr />
                </template>
            </template>
            <template #footer>
                <div class="text-right">
                    <ButtonLink
                        class="mr-2"
                        styling="default"
                        size="default"
                        @click="$emit('hide')"
                        >Cancel & Close
                    </ButtonLink>
                    <ButtonLink
                        :disabled="
                            !disableButton || waitlistForm.processing
                        "
                        @click="storeWaitlist"
                        styling="secondary"
                        size="default"
                        >Confirm
                    </ButtonLink>
                </div>
            </template>
        </CardBasic>
    </Modal>
</template>
