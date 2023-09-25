<script setup>
import { useForm } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";

const props = defineProps({
    partner: Object,
});

const form = useForm({
    id: props.partner.id,
    first_name: props.partner.first_name,
    last_name: props.partner.last_name,
    email: props.partner.email,
});

const updateProfileInformation = () => {
    form.put(route("admin.partners.update", { id: form.id }), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title> Profile Information </template>

        <template #description>
            Update partner account's profile information and email address.
        </template>

        <template #form>
            <!-- First Name -->
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>
            <!-- Last Name -->
            <div>
                <InputLabel for="name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                size="default"
                styling="secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>
