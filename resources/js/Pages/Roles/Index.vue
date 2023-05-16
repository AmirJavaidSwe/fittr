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
    roles: Object,
    search: String,
    per_page: Number,
    order_by: String,
    order_dir: String,
});

const form = useForm({
    search: props.search,
    per_page: props.per_page,
    order_by: props.order_by,
    order_dir: props.order_dir,
});

const setOrdering = (col) => {
    //reverse same col order
    if (form.order_by == col) {
        form.order_dir = form.order_dir == "asc" ? "desc" : "asc";
    }
    form.order_by = col;
    runSearch();
};

const runSearch = () => {
    form.get(route(`${usePage().props.user.source}.roles.index`), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

// form.search getter only;
watch(() => form.search, runSearch);
</script>
<template>
    <SectionTitle>
        <template #title> All Roles </template>
    </SectionTitle>
    <div class="flex items-center justify-between mb-6">
        <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="form.search = null">
            <select v-model="form.per_page" @change="runSearch" class="form-select rounded-l mr-1">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </search-filter>
        <ButtonLink v-can="{ module: 'roles', roles: $page.props.user.user_roles, permission: 'create', 'user': $page.props.user }" :href="route(`${$page.props.user.source}.roles.create`)" type="primary">Add new</ButtonLink>
    </div>

    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
                <tr>
                    <th @click="setOrdering('id')" class="px-6 py-3 border-b cursor-pointer"
                        :class="form.order_by == 'id' ? 'border-indigo-500' : ''">
                        ID
                    </th>
                    <th @click="setOrdering('title')" class="px-6 py-3 border-b cursor-pointer"
                        :class="form.order_by == 'title' ? 'border-indigo-500' : ''">
                        Title
                    </th>
                    <th @click="setOrdering('created_at')" class="px-6 py-3 border-b cursor-pointer"
                        :class="form.order_by == 'created_at' ? 'border-indigo-500' : ''">
                        Date created
                    </th>
                    <!-- <th @click="setOrdering('created_by')" class="px-6 py-3 border-b cursor-pointer"
                        :class="form.order_by == 'created_by' ? 'border-indigo-500' : ''">
                        Created by
                    </th> -->
                    <th class="border-b"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="role in props.roles.data" :key="role.id"
                    class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">{{ role.id }}</td>
                    <td class="px-6 py-4">{{ role.title }}</td>
                    <td class="px-6 py-4">{{ DateTime.fromISO(role.created_at).toLocaleString({
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric',
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true,
                    }) }}</td>
                    <!-- <td class="px-6 py-4">{{ role.created_by }}</td> -->
                    <td class="px-6 py-4">
                        <div class="flex gap-4 justify-end">
                            <Link :href="role.url_edit">
                                <font-awesome-icon :icon="faPencil" />
                            </Link>
                            <Link :href="role.url_show" v-can="{ module: 'roles', roles: $page.props.user.user_roles, permission: 'view', 'user': $page.props.user }">
                                <font-awesome-icon :icon="faChevronRight" v-can="{ module: 'roles', roles: $page.props.user.user_roles, permission: 'update', 'user': $page.props.user }" />
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>Viewing {{ roles.from }} - {{ roles.to }} of {{ roles.total }} results</p>
    <pagination class="mt-6" :links="roles.links" />
</template>