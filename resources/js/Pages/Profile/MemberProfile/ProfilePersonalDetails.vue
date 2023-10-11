<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
const props = defineProps(["business_settings", "user"]);

const form = useForm({
    user_id: props.user.id,
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    // phone: props.user.phone,
});


const saveProfileInfo = () => {
    form.post(
        route("ss.member.profile.store", {
            subdomain: props.business_settings.subdomain,
        }),
        {
            preserveScroll: true,
        }
    );
};
</script>
<template>
    <div class="grow lg:p-10 p-2 basis-1/2">
        <div class="font-boldtext-3xl text-lg mb-3 font-extrabold">
            Personal Details
        </div>
        <hr class="mt-5 mb-7" />
        <div class="mb-5 col-span-6 sm:col-span-12">
            <InputLabel for="forst_name" value="First Name" />
            <TextInput
                id="forst_name"
                v-model="form.first_name"
                type="text"
                class="mt-1 block w-full bg-gray-50"
            />
            <InputError :message="form.errors.first_name" class="mt-2" />
        </div>
        <div class="mb-5 col-span-6 sm:col-span-12">
            <InputLabel for="last_name" value="Last Name" />
            <TextInput
                id="last_name"
                v-model="form.last_name"
                type="text"
                class="mt-1 block w-full bg-gray-50"
            />
            <InputError :message="form.errors.last_name" class="mt-2" />

        </div>
        <div class="mb-5 col-span-6 sm:col-span-12">
            <InputLabel for="email" value="Email" />
            <TextInput
                id="email"
                v-model="form.email"
                type="text"
                class="mt-1 block w-full bg-gray-50"
            />
            <InputError :message="form.errors.email" class="mt-2" />
        </div>
        <!-- <div class="mb-5 col-span-6 sm:col-span-12">
            <InputLabel for="phone" value="Phone" />
            <TextInput
                id="phone"
                v-model="form.phone"
                type="text"
                class="mt-1 block w-full bg-gray-50"
            />
        </div> -->

        <!-- <hr class="mt-9 mb-7" /> -->
        <!-- <div class="font-boldtext-3xl text-lg mb-3 font-extrabold">
            Address / Contact
        </div> -->

        <div
            class="flex items-center justify-end py-3 text-right"
        >
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>
            <ButtonLink @click.prevent="saveProfileInfo"
                styling="secondary"
                size="default"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </div>
    </div>
</template>
