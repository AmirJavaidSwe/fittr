<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
  faHome,
  faBookOpen,
  faUsers,
  faUserTie,
  faWandMagicSparkles,
  faRepeat,
  faGears
} from '@fortawesome/free-solid-svg-icons';

const active_route = reactive({
    name: route().current(),
});

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div class="flex-shrink-0 p-4 bg-gray-100 overflow-y-auto space-y-4">
    <NavLink :href="route('partner.dashboard')" :active="active_route.name == 'partner.dashboard'">
      <font-awesome-icon :icon="faHome" />
      <div>Dashboard</div>
    </NavLink>
    <NavLink :href="route('partner.classes.index')" :active="active_route.name == 'partner.class-lesson.index'">
      <font-awesome-icon :icon="faBookOpen" />
      <div>Classes</div>
    </NavLink>
    <NavLink :href="route('partner.members.index')" :active="active_route.name == 'partner.members.index'">
      <font-awesome-icon :icon="faUsers" />
      <div>Members</div>
    </NavLink>
    <NavLink :href="route('partner.instructors.index')" :active="active_route.name == 'partner.instructors.index'">
      <font-awesome-icon :icon="faUserTie" />
      <div>Instructors</div>
    </NavLink>
    <NavLink :href="route('partner.exports.index')" :active="active_route.name == 'partner.exports.index'">
      <font-awesome-icon :icon="faRepeat" />
      <div>Data Center</div>
    </NavLink>
    <NavLink :href="route('partner.settings.index')" :active="active_route.name == 'partner.settings.index'">
      <font-awesome-icon :icon="faGears" />
      <div>Settings</div>
    </NavLink>
  </div>
</template>