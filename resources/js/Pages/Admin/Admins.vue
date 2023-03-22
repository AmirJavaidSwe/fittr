<script setup>
import SectionTitle from '@/Components/SectionTitle.vue';
import dayjs from 'dayjs';
const props = defineProps({
    admins: Array,
});

</script>
<template>
    <SectionTitle>
        <template #title>
            Admins
        </template>
    </SectionTitle>
    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
                <tr>
                    <th class="px-6 py-3 border-b cursor-pointer">ID</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Name</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Email</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Avatar</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Date created</th>
                    <!-- actions col -->
                    <th class="border-b"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="admin in admins" :key="admin.id" class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">{{admin.id}}</td>
                    <td class="px-6 py-4">{{admin.name}}</td>
                    <td class="px-6 py-4">{{admin.email}}</td>
                    <td class="px-6 py-4"><img :src="admin.profile_photo_url" :alt="admin.name" class="rounded-full h-8 w-8 object-cover"></td>
                    <td class="px-6 py-4">{{dayjs(admin.created_at)}}</td>
                    <td class="px-6 py-4"></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>