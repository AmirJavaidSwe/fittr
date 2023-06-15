<script setup>
import { reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
  faBookOpen,
  faUserTie,
  faDoorOpen,
  faTicketSimple,
  faTicket,
} from '@fortawesome/free-solid-svg-icons';

const active_route = reactive({
    name: route().current(),
});

const subdomain = ref(usePage().props.business_seetings.subdomain);

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div class="overflow-y-auto">
    <NavLink :href="route('ss.classes.index', {subdomain})" :active="active_route.name == 'ss.classes.index'" class="text-lg">
      <font-awesome-icon :icon="faBookOpen" />
      <div>Classes</div>
    </NavLink>
    <NavLink :href="route('ss.instructors.index', {subdomain})" :active="active_route.name == 'ss.instructors.index'" class="text-lg">
      <font-awesome-icon :icon="faUserTie" />
      <div>Instructors</div>
    </NavLink>
    <NavLink :href="route('ss.locations.index', {subdomain})" :active="active_route.name == 'ss.locations.index'" class="text-lg">
      <font-awesome-icon :icon="faDoorOpen" />
      <div>Locations</div>
    </NavLink>
    <NavLink :href="route('ss.memberships.index', {subdomain})" :active="active_route.name == 'ss.memberships.index'" class="text-lg">
      <font-awesome-icon :icon="faTicketSimple" />
      <div>Memberships</div>
    </NavLink>
  </div>
</template>