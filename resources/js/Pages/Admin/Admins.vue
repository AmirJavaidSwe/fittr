<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import { DateTime } from "luxon";
import { faPencil, faChevronRight } from "@fortawesome/free-solid-svg-icons";
import Avatar from "@/Components/Avatar.vue";
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    admins: Array,
});

</script>
<template>
    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg my-4">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
                <tr>
                    <th class="px-6 py-3 border-b cursor-pointer">ID</th>
                    <th class="px-6 py-3 border-b cursor-pointer">First Name</th>
                    <th class="px-6 py-3 border-b cursor-pointer">Last Name</th>
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
                <tr v-for="admin in admins" :key="admin.id" class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">{{admin.id}}</td>
                    <td class="px-6 py-4">{{admin.first_name}}</td>
                    <td class="px-6 py-4">{{admin.last_name}}</td>
                    <td class="px-6 py-4">{{admin.email}}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="ml-4 w-16 rounded my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 dark:bg-gray-700 dark:text-gray-300">
                            {{ admin.is_super ? 'Yes' : 'No' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <template v-if="!admin.is_super">
                            <template v-for="(role, role_i) in admin.user_roles" :key="role_i">
                                <div class="block my-1 bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">{{ role }}</div>
                            </template>
                        </template>
                    </td>
                    <td class="px-6 py-4">
                        <Avatar :initials="admin.initials" :image-url="admin.profile_photo_url" size="small" />
                    </td>
                    <td class="px-6 py-4">{{DateTime.fromISO(admin.created_at)}}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4 justify-end">
                            <Link :href="'/admin/settings/edit-admin/'+admin.id" v-can="{ module: 'admin-users', roles: $page.props.user.user_roles, permission: 'update', 'user': $page.props.user }">
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
