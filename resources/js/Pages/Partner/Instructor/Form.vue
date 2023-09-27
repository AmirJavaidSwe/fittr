<script setup>
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import ImageCropper from "@/Components/ImageCropper.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    isEdit: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.first_name" class="mt-2" />
            </div>

            <div>
                <InputLabel for="last_name" value="Last Name" />
                <TextInput
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.last_name" class="mt-2" />
            </div>

            <div>
                <InputLabel for="phone" value="Phone" />
                <TextInput
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.phone" class="mt-2" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="icon"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <template v-if="isEdit">
                <div class="text-lg font-semibold">Profile</div>

                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="image" value="Photo" />
                    <ImageCropper v-model="form.profile_image" />
                    <InputError :message="form.errors?.profile_image" class="mt-2" />
                </div>

                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="description" value="Description" />
                    <textarea v-model="form.profile_description" class="input-field w-full mt-2" id="description" rows="6"></textarea>
                    <InputError :message="form.errors.profile_description" class="mt-2" />
                </div>
            </template>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                styling="secondary"
                size="default"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>
