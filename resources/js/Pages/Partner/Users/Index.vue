<script setup>
import { watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
// import Section from '@/Components/Section.vue';
import SectionTitle from "@/Components/SectionTitle.vue";
import SearchFilter from "@/Components/SearchFilter.vue";
import Pagination from "@/Components/Pagination.vue";
import ButtonLink from "@/Components/ButtonLink.vue";
import { DateTime } from "luxon";
import { faPencil, faChevronRight } from "@fortawesome/free-solid-svg-icons";
const props = defineProps({
    users: Object,
});
</script>
<template>
    <SectionTitle>
        <template #title> All Users </template>
    </SectionTitle>
    <div class="flex items-center justify-between mb-6">
        <ButtonLink :href="route(`partner.users.create`)" type="primary">Add new</ButtonLink>
    </div>

    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
            <tr>
                <th class="px-6 py-3 border-b cursor-pointer">ID</th>
                <th class="px-6 py-3 border-b cursor-pointer">Name</th>
                <th class="px-6 py-3 border-b cursor-pointer">Email</th>
                <th class="px-6 py-3 border-b cursor-pointer">Is Super Admin</th>
                <th class="px-6 py-3 border-b cursor-pointer">Roles</th>
                <th class="px-6 py-3 border-b cursor-pointer">Avatar</th>
                <th class="px-6 py-3 border-b cursor-pointer">Date created</th>
                <!-- actions col -->
                <th class="border-b"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="user in props.users" :key="user.id" class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                <td class="px-6 py-4">{{user.id}}</td>
                <td class="px-6 py-4">{{user.name}}</td>
                <td class="px-6 py-4">{{user.email}}</td>
                <td class="px-6 py-4 text-center">
                    <div class="ml-4 w-16 rounded my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 dark:bg-gray-700 dark:text-gray-300">
                        {{ user.is_super ? 'Yes' : 'No' }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <template v-if="!user.is_super">
                        <template v-for="(role, role_i) in user.user_roles" :key="role_i">
                            <div class="block my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ role }}</div>
                        </template>
                    </template>
                </td>
                <td class="px-6 py-4">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-8 w-8 object-cover" />
                </td>
                <td class="px-6 py-4">{{DateTime.fromISO(user.created_at)}}</td>
                <td class="px-6 py-4">
                    <div class="flex gap-4 justify-end">
                        <Link :href="route(`partner.users.edit`,user.id)">
                            <font-awesome-icon :icon="faPencil" />
                        </Link>
                        <!-- <Link :href="role.url_show">
                        <font-awesome-icon :icon="faChevronRight" />
                        </Link> -->
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
