<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue'; 
dayjs.extend(relativeTime);
const props = defineProps({
    subscription: Object,
});
const confirmingCancellation = ref(false);
const confirmCancellation = () => {
    confirmingCancellation.value = true;
};
const closeModal = () => {
    confirmingCancellation.value = false;
};
const form = useForm();
const cancelSubscription = () => {
    form.put(route('partner.subscriptions.cancel', {id: props.subscription.id}), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
<div class="shadow-sm">
    <div class="p-4 text-sm bg-gray-200">
        <b>{{subscription.package_title}}</b><br>
        Subscribed on: {{new Date(subscription.updated_at).toLocaleString() }} ({{ dayjs(subscription.updated_at).fromNow()}})
    </div>
    <div class="p-4 bg-white space-y-2">
        <div class="border-b border-gray-100 flex justify-between">
            <span>Transaction %</span>
            <span class="font-bold">{{subscription.tx_percent}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Transaction - fixed fee</span>
            <span class="font-bold">{{subscription.tx_fixed_fee}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Billing period</span>
            <span class="font-bold">{{subscription.cycle}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Subscription price</span>
            <span class="font-bold">{{subscription.fee}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Monthly fee per site >1</span>
            <span class="font-bold">{{subscription.monthly_fee_sites}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Admin Users</span>
            <span class="font-bold">{{subscription.admin_users == 0 ? 'Unlimited' : subscription.admin_users}}</span>
        </div>
        <div class="border-b border-gray-100 flex justify-between">
            <span>Max sites</span>
            <span class="font-bold">{{subscription.max_sites == 0 ? 'Unlimited' : subscription.max_sites}}</span>
        </div>
    </div>
    <div class="p-4 bg-gray-300 flex items-center justify-end">
        <DangerButton @click="confirmCancellation">
            Cancel subscription
        </DangerButton>
    </div>

    <DialogModal :show="confirmingCancellation" @close="closeModal">
        <template #title>
            Cancel subscription
        </template>

        <template #content>
            Are you sure you want to cancel your subscription?
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                No
            </SecondaryButton>
            <DangerButton
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="cancelSubscription"
            >
                Confirm
            </DangerButton>
        </template>
    </DialogModal>
</div>
</template>