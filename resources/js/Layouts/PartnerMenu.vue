<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faHome, faGaugeHigh, faGears } from '@fortawesome/free-solid-svg-icons';

const active_route = reactive({
    name: route().current(),
});

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div class="hidden flex-shrink-0 p-4 w-56 bg-gray-100 overflow-y-auto md:block space-y-4">
    <NavLink :href="route('partner.dashboard')" :active="active_route.name == 'partner.dashboard'">
      <font-awesome-icon :icon="faHome" />
      <div>Dashboard</div>
    </NavLink>
    <NavLink :href="route('partner.pricing.index')" :active="active_route.name == 'partner.pricing.index'">
      <font-awesome-icon :icon="faGaugeHigh" />
      <div>Pricing</div>
    </NavLink>
    <NavLink :href="route('partner.settings.index')" :active="active_route.name == 'partner.settings.index'">
      <font-awesome-icon :icon="faGears" />
      <div>Settings</div>
    </NavLink>
  </div>
</template>