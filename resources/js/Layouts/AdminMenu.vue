<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import { faHome, faGaugeHigh, faUserTie, faServer, faGears, faCoffee} from '@fortawesome/free-solid-svg-icons';

const active_route = reactive({
    name: route().current(),
});

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div class="hidden flex-shrink-0 p-4 w-56 bg-gray-100 overflow-y-auto md:block space-y-4">
    <NavLink :href="route('admin.dashboard')" :active="active_route.name == 'admin.dashboard'" v-can="{ module: 'dashboard', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faHome" />
      <div>Dashboard</div>
    </NavLink>
    <NavLink :href="route('admin.partners.performance.index')" :active="active_route.name == 'admin.partners.performance.index'" v-can="{ module: 'partners-performance', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faGaugeHigh" />
      <div>Partner performance</div>
    </NavLink>
    <NavLink :href="route('admin.partners.index')" :active="active_route.name == 'admin.partners.index'" v-can="{ module: 'partner-management', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faUserTie" />
      <div>Partner management</div>
    </NavLink>
    <NavLink :href="route('admin.instances.index')" :active="active_route.name == 'admin.instances.index'" v-can="{ module: 'aws-instances', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faServer" />
      <div>AWS Instances</div>
    </NavLink>
    <NavLink :href="route('admin.settings')" :active="active_route.name == 'admin.settings'" v-can="{ module: 'settings', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faGears" />
      <div>Settings</div>
    </NavLink>
    <NavLink :href="route(`${$page.props.user.source}.roles.index`)" :active="active_route.name == `${$page.props.user.source}.roles.index`" v-can="{ module: 'roles', roles: $page.props.user.user_roles, permission: 'viewAny', 'user': $page.props.user }">
      <font-awesome-icon :icon="faGears" />
      <div>Roles</div>
    </NavLink>
    <NavLink :href="route('admin.demo')" :active="active_route.name == 'admin.demo'">
      <font-awesome-icon :icon="faCoffee" />
      <div>Typography</div>
    </NavLink>
  </div>
</template>