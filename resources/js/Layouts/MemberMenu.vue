<script setup>
import { reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHome, faFileInvoiceDollar, faGaugeHigh, faGears, faReceipt } from '@fortawesome/free-solid-svg-icons';

const user = usePage().props.user;
const subdomain = ref(usePage().props.business_settings.subdomain);

const active_route = reactive({
    name: route().current(),
});

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div v-if="user" class="hidden flex-shrink-0 p-4 w-56 bg-dark overflow-y-auto md:block space-y-4">
    <!-- <NavLink :href="route('member.dashboard')" :active="active_route.name == 'member.dashboard'"> -->
    <NavLink :href="route('ss.member.dashboard', {subdomain})" :active="active_route.name == 'ss.member.dashboard'">
      <font-awesome-icon :icon="faHome" class="w-4" />
      <div>Dashboard</div>
    </NavLink>
    <NavLink href="#">
      <font-awesome-icon :icon="faReceipt" class="w-4" />
      <div>My memberships</div>
    </NavLink>
    <NavLink href="#">
      <font-awesome-icon :icon="faFileInvoiceDollar" class="w-4" />
      <div>Orders history</div>
    </NavLink>
    <NavLink :href="route('ss.member.bookings.index', {subdomain: usePage().props?.business_settings?.subdomain})">
      <font-awesome-icon :icon="faGears" class="w-4" />
      <div>My bookings</div>
    </NavLink>
    <NavLink href="#">
      <font-awesome-icon :icon="faGaugeHigh" class="w-4" />
      <div>Training history</div>
    </NavLink>
  </div>
</template>