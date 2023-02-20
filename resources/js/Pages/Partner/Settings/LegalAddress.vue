<script setup>
import { useForm, usePage } from '@inertiajs/inertia-vue3';
import GeneralSettingsMenu from '@/Pages/Partner/Settings/GeneralSettingsMenu.vue';

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// const props = defineProps({
//     partner: Object,
// });

const partner = usePage().props.value.user;

const form = useForm({
    id: partner.id,
    name: partner.name,
    email: partner.email,
});

const updateProfileInformation = () => {
    form.put(route('admin.partners.update', {id: form.id}), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true
    });
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #description>
            <GeneralSettingsMenu />
        </template>


        <template #form>
             <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
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

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
