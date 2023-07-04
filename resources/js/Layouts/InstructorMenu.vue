<script setup>
import { reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHome } from '@fortawesome/free-solid-svg-icons';

const user = usePage().props.user;
const subdomain = ref(usePage().props.business_seetings.subdomain);

const active_route = reactive({
    name: route().current(),
});

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div v-if="user" class="hidden flex-shrink-0 p-4 w-56 bg-dark overflow-y-auto md:block space-y-4">
    <NavLink :href="route('ss.instructor.dashboard', {subdomain})" :active="active_route.name == 'ss.instructor.dashboard'">
      <font-awesome-icon :icon="faHome" class="w-4" />
      <div>Dashboard</div>
    </NavLink>
  </div>
</template>