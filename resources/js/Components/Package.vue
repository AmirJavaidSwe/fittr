<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/inertia-vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import ButtonLink from '@/Components/ButtonLink.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Radio from '@/Components/Radio.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import DialogModal from '@/Components/DialogModal.vue';
import { faPencil } from '@fortawesome/free-solid-svg-icons';
 
dayjs.extend(relativeTime);
const props = defineProps({
    pack: Object,
    has_subscription: Boolean,
    admin: {
        type: Boolean,
        default: false
    }
});
const route_name = usePage().props.value.route_name;
const can_edit = computed(() => {
  return props.admin && route_name != 'admin.packages.edit';
})
const edit_link = route().has('admin.packages.edit') ? route('admin.packages.edit', {id: props.pack.id}) : '#';
const show_link = route().has('admin.packages.show') ? route('admin.packages.show', {id: props.pack.id}) : '#';
const confirmingSubscription = ref(false);
const confirmSubscription = () => {
    confirmingSubscription.value = true;
};
const closeModal = () => {
    confirmingSubscription.value = false;
};
const form = useForm({
    period: 'monthly',
    // period: 'annually',
});
const confirmSubscribe = () => {
    console.log(form.period);
    form.post(route('partner.subscriptions.store', {id: props.pack.id}), {
        preserveScroll: false,
        onSuccess: () => {
            closeModal();
        },
        onError: errors => {
            // console.log(errors)
        },
    });
};
</script>

<template>

<div class="bg-gray-200 p-2 rounded-md shadow-sm w-72 space-y-2">
    <div>
        <div class="font-bold text-2xl flex justify-between items-start">
            <Link :href="show_link" v-if="route_name != 'admin.packages.show' && can_edit" class="hover:text-blue-900 transition">
                {{pack.title}}
            </Link>
            <span v-else>
                {{pack.title}}
            </span>
            <Link :href="edit_link" v-if="can_edit" class="text-base text-gray-500 hover:text-gray-900 transition">
                <span v-if="pack.is_private" class="font-normal p-2 text-xs">private</span>
                <font-awesome-icon :icon="faPencil" />
            </Link>
            <span v-if="pack.subscribed" class="bg-green-300 text-xs font-medium px-2.5 py-0.5 rounded">Current plan</span>
        </div>
        <div class="mb-4 text-sm">{{pack.description}}</div>
    </div>
    <div v-if="admin" class="border-b border-gray-100 flex justify-between">
        <span>Status</span>
        <span class="font-bold px-2" :class="pack.status ? 'bg-green-300' : 'bg-gray-400'">{{pack.status ? 'ON' : 'OFF'}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Transaction %</span>
        <span class="font-bold">{{pack.tx_percent}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Transaction - fixed fee</span>
        <span class="font-bold">{{pack.tx_fixed_fee}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Monthly subscription price<br>Billed Annually</span>
        <span class="font-bold">{{pack.fee_annually}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Monthly subscription price<br>Billed Monthly</span>
        <span class="font-bold">{{pack.fee_monthly}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Monthly fee per site >1</span>
        <span class="font-bold">{{pack.monthly_fee_sites}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Admin Users</span>
        <span class="font-bold">{{pack.admin_users == 0 ? 'Unlimited' : pack.admin_users}}</span>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
        <span>Max sites</span>
        <span class="font-bold">{{pack.max_sites == 0 ? 'Unlimited' : pack.max_sites}}</span>
    </div>

    <div class="mt-8 text-sm">
        <div v-if="admin">
                Updated:<br>
                {{new Date(pack.updated_at).toLocaleString() }} ({{ dayjs(pack.updated_at).fromNow()}})
                <div v-if="!pack.is_free">
                    Yearly cost on monthly cycle: <span class="font-bold">{{ pack.monthly_price_year}}</span><br>
                    Yearly cost on annual cycle: <span class="font-bold">{{ pack.annual_price_year}}</span><br>
                    12 months savings (annual vs monthly): <span class="font-bold">{{ pack.annual_savings}}</span>
                </div>
        </div>
        <div v-else>
            <ButtonLink 
                v-if="pack.subscribed"
                :href="route('partner.subscriptions.index')"
                type="secondary"
                >
                Manage
            </ButtonLink>
            <PrimaryButton 
                v-if="!pack.subscribed"
                @click="confirmSubscription"
                >
                Subscribe
            </PrimaryButton>

            <DialogModal :show="confirmingSubscription" @close="closeModal">
                <template #title>
                    New package subscription
                </template>

                <template #content>
                    Are you sure you want to subscribe to <b>{{pack.title}} package</b>?
                    <div v-if="has_subscription">You have active subscription. Subscribing to new package will cancel current subscription.</div>

                    <div class="mt-4" v-if="!pack.is_free">
                        <InputLabel value="Select billing cycle below" />
                        <div class="mt-2 flex gap-4">
                            <label for="month" class="flex items-center">
                                <Radio v-model="form.period" name="period" value="monthly" id="month" />
                                <span class="ml-2 text-sm text-gray-600">{{ pack.fee_monthly }} billed every month</span>
                            </label>
                        </div>
                        <div class="mt-2 flex gap-4">
                            <label for="year" class="flex items-center">
                                <Radio v-model="form.period"  name="period"  value="annually" id="year" />
                                <span class="ml-2 text-sm text-gray-600">{{ pack.annual_price_year }} billed every year, {{ pack.fee_annually }} per month. Save {{ pack.annual_savings }} per year vs monthly</span>
                            </label>
                        </div>
                    </div>
                    <InputError :message="form.errors.period" class="mt-2" />
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="confirmSubscribe"
                    >
                        Confirm
                    </PrimaryButton>
                </template>
            </DialogModal>
        </div>
    </div>

</div>
</template>