<script setup>
import { reactive } from "vue";
import { router } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import PartnerSidebarDashboardIcon from "@/Icons/PartnerSidebarDashboardIcon.vue";
import PartnerSidebarClassesIcon from "@/Icons/PartnerSidebarClassesIcon.vue";
import PartnerSidebarMemberIcon from "@/Icons/PartnerSidebarMemberIcon.vue";
import PartnerSidebarInstructorIcon from "@/Icons/PartnerSidebarInstructorIcon.vue";
import PartnerSidebarDataCenterIcon from "@/Icons/PartnerSidebarDataCenterIcon.vue";
import PartnerSidebarSettingsIcon from "@/Icons/PartnerSidebarSettingsIcon.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faCashRegister,
    faIdCardClip,
} from "@fortawesome/free-solid-svg-icons";

defineProps({
    collapsed: Boolean,
});

const active_route = reactive({
    name: route().current(),
});

router.on("navigate", (event) => {
    active_route.name = event.detail.page.props.route_name;
});
</script>

<template>
    <div>
        <NavLink
            :href="route('partner.dashboard')"
            :active="active_route.name == 'partner.dashboard'"
            v-can="{
                module: 'dashboard',
                roles: $page.props.user.user_roles,
                permission: 'viewAny',
                user: $page.props.user,
            }"
            v-tooltip.right="{ 
                content: 'Dashboard',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarDashboardIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Dashboard</div>
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
            v-tooltip.right="{ 
                content: 'Classes',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarClassesIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Classes</div>
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
            v-tooltip.right="{ 
                content: 'Members',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarMemberIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Members</div>
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
            v-tooltip.right="{ 
                content: 'Instructors',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarInstructorIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Instructors</div>
        </NavLink>
        <NavLink
            :href="route('partner.orders.index')"
            :active="active_route.name == 'partner.orders.index'"
            v-tooltip.right="{ 
                content: 'Orders',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <font-awesome-icon :icon="faCashRegister" class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Sales/Orders</div>
        </NavLink>
        <NavLink
            :href="route('partner.memberships.index')"
            :active="active_route.name == 'partner.memberships.index'"
            v-tooltip.right="{ 
                content: 'Memberships',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <font-awesome-icon :icon="faIdCardClip" class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Memberships</div>
        </NavLink>
        <NavLink
            :href="route('partner.exports.index')"
            :active="active_route.name == 'partner.exports.index'"
            v-tooltip.right="{ 
                content: 'Data Center',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarDataCenterIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Data Center</div>
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
            v-tooltip.right="{ 
                content: 'Settings',
                distance: 14,
                disabled: !collapsed
            }"
        >
            <PartnerSidebarSettingsIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Settings</div>
        </NavLink>
    </div>
</template>
