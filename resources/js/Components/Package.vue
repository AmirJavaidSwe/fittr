<script setup>
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { Link, usePage } from '@inertiajs/inertia-vue3';
 
dayjs.extend(relativeTime);
const props = defineProps({
    pack: Object,
});
const route_name = usePage().props.value.route_name;
</script>

<template>

<div class="bg-gray-200 p-2 rounded-md shadow-sm w-72 space-y-2">
    <div>
        <div class="font-bold text-2xl flex justify-between">
            <Link :href="route('admin.packages.show', {id: pack.id})" v-if="route_name != 'admin.packages.show'" class="hover:text-blue-900 transition">
                {{pack.title}}
            </Link>
            <span v-else>
                {{pack.title}}
            </span>
            <Link :href="route('admin.packages.edit', {id: pack.id})" v-if="route_name != 'admin.packages.edit'" class="text-base text-gray-500 hover:text-gray-900 transition">
                <font-awesome-icon icon="fa-solid fa-pencil" />
            </Link>
        </div>
        <div class="mb-4 text-sm">{{pack.description}}</div>
    </div>
    <div class="border-b border-gray-100 flex justify-between">
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

    <div>
        <div class="mt-8 text-sm">
            Updated:<br>
            {{new Date(pack.updated_at).toLocaleString() }} ({{ dayjs(pack.updated_at).fromNow()}})
        </div>
    </div>
</div>
</template>