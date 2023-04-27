<script setup>
import { Link } from '@inertiajs/vue3';
import { DateTime } from "luxon";
import SectionTitle from '@/Components/SectionTitle.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    api_results: Object,
});

</script>
<template>
    <SectionTitle>
        <template #title>
            API results
        </template>
    </SectionTitle>
    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
                <tr>
                    <th class="px-6 py-3 border-b cursor-pointer">Name</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Partner</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Location</th>
                    <th class="px-6 py-3 border-b cursor-pointer">IP</th>
                    <th class="px-6 py-3 border-b cursor-pointer">SSH key</th>
                    <th class="px-6 py-3 border-b cursor-pointer">State</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Date created</th>
                    <!-- actions col -->
                    <th class="border-b"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="instance in api_results.data.instances" :key="instance.name" class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">{{instance.name}}</td>
                    <td class="px-6 py-4">
                        <div v-if="instance.partner_id">
                            <Link :href="route('admin.partners.show', {id: instance.partner_id})">
                                {{instance.partner_name}}<br>
                                {{instance.partner_email}}
                            </Link>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{instance.location?.regionName}}</td>
                    <td class="px-6 py-4">Public: {{instance.publicIpAddress}}<br>Private: {{instance.privateIpAddress}}</td>
                    <td class="px-6 py-4">{{instance.sshKeyName}}</td>
                    <td class="px-6 py-4">{{instance.state?.name}}</td>
                    <td class="px-6 py-4">{{DateTime.fromISO(instance.createdAt)}}</td>
                    <td class="px-6 py-4">
                        <ButtonLink :href="route('admin.instances.show', {name: instance.name})" type="primary">Show</ButtonLink>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>