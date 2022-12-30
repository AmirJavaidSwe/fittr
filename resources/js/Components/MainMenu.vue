
<script setup>
import { reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3'
import NavLink from '@/Components/NavLink.vue';

const menu_items = usePage().props.value.menu;
const active_route = reactive({
    name: route().current(),
});

Inertia.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div>
    <NavLink v-for="menu_item in menu_items" :key="menu_item.route_name" :href="route(menu_item.route_name)" :active="menu_item.route_name == active_route.name">
      <font-awesome-icon v-if="menu_item.icon" :icon="menu_item.icon" />
      <div>{{menu_item.title}}</div>
    </NavLink>
  </div>
</template>