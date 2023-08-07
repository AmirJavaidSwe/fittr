<script setup>
import { reactive, ref } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import ButtonLink from "@/Components/ButtonLink.vue";

const active_route = reactive({
    name: route().current(),
});

const subdomain = ref(usePage().props.business_settings.subdomain);
const styleLink = [
  'p-4',
  'md:p-2',
  'hover:bg-white/25',
  'rounded-lg',
  'transition',
  'block',
  'space-x-4',
  'uppercase',
  'md:capitalize',
  'text-center',
];

router.on('navigate', (event) => {
  active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
  <div>
    <Link :href="route('ss.classes.index', {subdomain})" :class="styleLink">
        <span :class="{'text-black/50 md:text-white': active_route.name == 'ss.classes.index'}">Classes</span>
    </Link>
    <Link :href="route('ss.instructors.index', {subdomain})" :class="styleLink">
        <span :class="{'text-black/50 md:text-white': active_route.name == 'ss.instructors.index'}">Instructors</span>
    </Link>
    <Link :href="route('ss.locations.index', {subdomain})" :class="styleLink">
        <span :class="{'text-black/50 md:text-white': active_route.name == 'ss.locations.index'}">Locations</span>
    </Link>
    <Link :href="route('ss.memberships.index', {subdomain})" :class="styleLink">
        <span :class="{'text-black/50 md:text-white': active_route.name == 'ss.memberships.index'}">Memberships</span>
    </Link>

    <ButtonLink :href="route('login')" styling="secondary" size="default" class="inline-flex md:hidden my-4">Login</ButtonLink>
    <ButtonLink :href="route('register')" styling="secondary" size="default" class="inline-flex md:hidden my-4">Sign up</ButtonLink>
  </div>
</template>