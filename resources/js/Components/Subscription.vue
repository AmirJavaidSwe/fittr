<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import DialogModal from '@/Components/DialogModal.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

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
const form = useForm({});
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
        Subscribed on: {{ DateTime.fromISO(subscription.updated_at) }} ({{ DateTime.fromISO(subscription.updated_at).toRelative() }})
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
        <ButtonLink 
            styling="danger"
            size="small"
            @click="confirmCancellation"
            >
            Cancel subscription
        </ButtonLink>
    </div>

    <DialogModal :show="confirmingCancellation" @close="closeModal">
        <template #title>
            Cancel subscription
        </template>

        <template #content>
            Are you sure you want to terminate your subscription?
        </template>

        <template #footer>
            <ButtonLink 
                styling="default"
                size="small"
                @click="closeModal">
                Keep
            </ButtonLink>
            <ButtonLink
                styling="danger"
                size="small"
                class="ml-3"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                @click="cancelSubscription"
            >
                Confirm
            </ButtonLink>
        </template>
    </DialogModal>
</div>
</template>