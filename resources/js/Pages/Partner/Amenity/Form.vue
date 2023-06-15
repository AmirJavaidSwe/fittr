<script setup>
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import Checkbox from "@/Components/Checkbox.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="icon" value="Icon" />
                <TextInput
                    id="icon"
                    v-model="form.icon"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.icon" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="contents" value="Contents" />
                <TextInput
                    id="icon"
                    v-model="form.contents"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError :message="form.errors.contents" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="ordering" value="Ordering" />
                <TextInput
                    id="icon"
                    v-model="form.ordering"
                    type="number"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.ordering" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="status" value="Status" />
                <Checkbox
                    id="status"
                    v-model="form.status"
                    :checked="form.status === 1"
                    class="mt-1 w-4"
                />
                <InputError :message="form.errors.status" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <ButtonLink
                styling="secondary"
                size="default"
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </ButtonLink>
        </template>
    </FormSection>
</template>
