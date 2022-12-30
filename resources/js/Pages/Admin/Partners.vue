<script>
import AppLayout from '@/Layouts/AppLayout.vue'; 
export default {
    layout: (h, page) => h(AppLayout, () => page),
}
</script>
<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import Section from '@/Components/Section.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SearchFilter from '@/Components/SearchFilter.vue';
import Pagination from '@/Components/Pagination.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    users: Object,
    search: String,
});

const search_new = ref(props.search);
const per_page = ref(props.users.per_page);

const setPerPage = (value) => {
    Inertia.get(route('admin.partners.index'), {per_page: per_page.value}, { preserveState: true });
};

const resetSearch = () => {
    search_new.value = null;
};
const runSearch = () => {
    let payload = {};
    if(search_new.value){
        payload.search = search_new.value;
    }
    Inertia.get(route('admin.partners.index'), payload, { preserveState: true });
};

watch(search_new, runSearch);

</script>

<template>
    <SectionTitle>
        <template #title>
            All partners
        </template>
    </SectionTitle>

    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="search_new" class="mr-4 w-full max-w-md" @reset="resetSearch">
        <select v-model="per_page" @change="setPerPage" class="form-select rounded-l mr-1">
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
      </search-filter>
      <ButtonLink :href="route('admin.partners.index')" type="primary">Add new</ButtonLink>
    </div>

    <Section>
        <table class="table-auto text-left border-collapse w-full">
            <thead>
                <tr class="border-b">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in props.users.data" :key="user.id" class="border-b">
                    <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.created_at}}</td>
                    <td>
                        <div class="flex gap-4 justify-end">
                            <Link :href="user.url_edit">
                                <font-awesome-icon icon="fa-solid fa-pencil" />
                            </Link>
                            <Link :href="user.url_show">
                                <font-awesome-icon icon="fa-solid fa-chevron-right" />
                            </Link>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </Section>

    <p>Viewing {{users.from}} - {{users.to}} of {{users.total}} results</p>
    <pagination class="mt-6" :links="users.links" />

</template>
