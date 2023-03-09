<script setup>
import { watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
// import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { faPencil, faChevronRight} from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    users: Object,
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
    if(form.order_by == col){
        form.order_dir = form.order_dir == 'asc' ? 'desc' : 'asc';
    }
    form.order_by = col;
    runSearch();
};

const runSearch = () => {
    form.get(route('admin.partners.index'), {
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
        <template #title>
            All partners
        </template>
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
      <ButtonLink :href="route('admin.partners.index')" type="primary">Add new</ButtonLink>
    </div>

    <div class="relative overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
        <table class="table-auto text-left border-collapse w-full">
            <thead class="uppercase bg-gray-100 text-sm whitespace-nowrap">
                <tr>
                    <th @click="setOrdering('id')" class="px-6 py-3 border-b cursor-pointer" :class="form.order_by == 'id' ? 'border-indigo-500' : ''">ID</th>
                    <th @click="setOrdering('name')" class="px-6 py-3 border-b cursor-pointer" :class="form.order_by == 'name' ? 'border-indigo-500' : ''">Name</th>
                    <th @click="setOrdering('email')" class="px-6 py-3 border-b cursor-pointer" :class="form.order_by == 'email' ? 'border-indigo-500' : ''">Email</th>
                    <th @click="setOrdering('created_at')" class="px-6 py-3 border-b cursor-pointer" :class="form.order_by == 'created_at' ? 'border-indigo-500' : ''">Date created</th>
                    <th class="border-b"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in props.users.data" :key="user.id" class="border-b whitespace-nowrap bg-white hover:bg-gray-50">
                    <td class="px-6 py-4">{{user.id}}</td>
                    <td class="px-6 py-4">{{user.name}}</td>
                    <td class="px-6 py-4">{{user.email}}</td>
                    <td class="px-6 py-4">{{user.created_at}}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-4 justify-end">
                            <Link :href="user.url_edit">
                                <font-awesome-icon :icon="faPencil" />
                            </Link>
                            <Link :href="user.url_show">
                                <font-awesome-icon :icon="faChevronRight" />
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>Viewing {{users.from}} - {{users.to}} of {{users.total}} results</p>
    <pagination class="mt-6" :links="users.links" />

</template>
