<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    package: Object,
});

const packageBeingDeleted = ref(null);
const confirmDeletion = () => {
    packageBeingDeleted.value = true;
};

const form = useForm({
    id: props.package.id,
    status: props.package.status,
    is_private: props.package.is_private,
    title: props.package.title,
    description: props.package.description,
    tx_percent: props.package.tx_percent,
    tx_fixed_fee: props.package.tx_fixed_fee,
    fee_annually: props.package.fee_annually,
    fee_monthly: props.package.fee_monthly,
    monthly_fee_sites: props.package.monthly_fee_sites,
    admin_users: props.package.admin_users,
    max_sites: props.package.max_sites,
});

const update = () => {
    form.put(route('admin.packages.update', {id: form.id}), {
        preserveScroll: true,
    });
};

const deletePackage = () => {
    form.delete(route('admin.packages.destroy', {id: form.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (packageBeingDeleted.value = null),
    });
};
</script>

<template>
    <FormSection @submitted="update">
        <template #title>
            Package Information
        </template>

        <template #description>
            <div>Update package configuration.</div>
            <button class="cursor-pointer mt-6 text-sm text-red-500" @click="confirmDeletion">
                Delete
            </button>
        </template>

        <template #form>
            <!-- Status -->
            <div class="col-span-6 sm:col-span-4">
                <label class="flex items-center">
                    <span class="mr-4 text-sm text-gray-700">Status</span>
                    <Checkbox v-model:checked="form.status" :value="form.status ? '1' : '0'" />
                    <span class="ml-4 text-sm text-gray-700" v-text="form.status ? 'On' : 'Off'"></span>
                </label>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Private -->
            <div class="col-span-6 sm:col-span-4">
                <label class="flex items-center">
                    <span class="mr-4 text-sm text-gray-700">Private</span>
                    <Checkbox v-model:checked="form.is_private" :value="form.is_private ? '1' : '0'" />
                    <span class="ml-4 text-sm text-gray-700" v-text="form.is_private ? 'Yes' : 'No'"></span>
                </label>
                <InputError :message="form.errors.status" class="mt-2" />
            </div>

            <!-- Title -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="description" value="Description" />
                <TextInput
                    id="description"
                    v-model="form.description"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <!-- Transaction % -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tx_percent" value="Transaction %" />
                <TextInput
                    id="tx_percent"
                    v-model="form.tx_percent"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.tx_percent" class="mt-2" />
            </div>

            <!-- Transaction - fixed fee -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="tx_fixed_fee" value="Transaction - fixed fee" />
                <TextInput
                    id="tx_percent"
                    v-model="form.tx_fixed_fee"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.tx_fixed_fee" class="mt-2" />
            </div>

            <!-- Monthly subscription price, Billed Annually -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="fee_annually" value="Monthly subscription price (Billed Annually)" />
                <TextInput
                    id="tx_percent"
                    v-model="form.fee_annually"
                    type="number"
                    min="0"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.fee_annually" class="mt-2" />
            </div>

            <!-- Monthly subscription price, Billed Monthly -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="fee_monthly" value="Monthly subscription price (Billed Monthly)" />
                <TextInput
                    id="tx_percent"
                    v-model="form.fee_monthly"
                    type="number"
                    min="0"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.fee_monthly" class="mt-2" />
            </div>

            <!-- Monthly fee per site >1 -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="monthly_fee_sites" value="Monthly fee per site >1" />
                <TextInput
                    id="tx_percent"
                    v-model="form.monthly_fee_sites"
                    type="number"
                    min="0"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.monthly_fee_sites" class="mt-2" />
            </div>

            <!-- Admin Users -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="admin_users" value="Admin Users" />
                <TextInput
                    id="tx_percent"
                    v-model="form.admin_users"
                    type="number"
                    min="0"
                    placeholder="Enter 0 for unlimited"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.admin_users" class="mt-2" />
            </div>

            <!-- Max sites -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="max_sites" value="Max sites" />
                <TextInput
                    id="tx_percent"
                    v-model="form.max_sites"
                    type="number"
                    min="0"
                    placeholder="Enter 0 for unlimited"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.max_sites" class="mt-2" />
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
     <!-- Delete Confirmation Modal -->
    <ConfirmationModal :show="packageBeingDeleted != null" @close="packageBeingDeleted = null">
        <template #title>
            Delete package
        </template>

        <template #content>
            Are you sure you would like to delete this package?
        </template>

        <template #footer>
            <SecondaryButton @click="packageBeingDeleted = null">
                Cancel
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="deletePackage"
            >
                Delete
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
