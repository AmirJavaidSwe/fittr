<script setup>
import { reactive } from "vue";
import { router } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faHome,
    faBookOpen,
    faUsers,
    faUserTie,
    faWandMagicSparkles,
    faRepeat,
    faGears,
} from "@fortawesome/free-solid-svg-icons";

const active_route = reactive({
    name: route().current(),
});

router.on("navigate", (event) => {
    active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
    <div class="flex-grow p-4 bg-primary-500 overflow-y-auto space-y-4">
        <NavLink
            :href="route('partner.dashboard')"
            :active="active_route.name == 'partner.dashboard'"
            v-can="{
                module: 'dashboard',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
        >
            <font-awesome-icon :icon="faHome" />
            <div>Dashboard</div>
        </NavLink>
        <NavLink
            :href="route('partner.classes.index')"
            :active="active_route.name == 'partner.classes.index'"
            v-can="{
                module: 'classes',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
        >
            <font-awesome-icon :icon="faBookOpen" />
            <div>Classes</div>
        </NavLink>
        <NavLink
            :href="route('partner.members.index')"
            :active="active_route.name == 'partner.members.index'"
            v-can="{
                module: 'members',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
        >
            <font-awesome-icon :icon="faUsers" />
            <div>Members</div>
        </NavLink>
        <NavLink
            :href="route('partner.instructors.index')"
            :active="active_route.name == 'partner.instructors.index'"
            v-can="{
                module: 'instructors',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
        >
            <font-awesome-icon :icon="faUserTie" />
            <div>Instructors</div>
        </NavLink>
        <NavLink
            :href="route('partner.exports.index')"
            :active="active_route.name == 'partner.exports.index'"
        >
            <font-awesome-icon :icon="faRepeat" />
            <div>Data Center</div>
        </NavLink>
        <NavLink
            :href="route('partner.settings.index')"
            :active="active_route.name == 'partner.settings.index'"
            v-can="{
                module: 'partner-settings',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
        >
            <font-awesome-icon :icon="faGears" />
            <div>Settings</div>
        </NavLink>
    </div>
</template>
