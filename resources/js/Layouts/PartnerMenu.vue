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
        >
            <PartnerSidebarInstructorIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Instructors</div>
        </NavLink>
        <NavLink
            :href="route('partner.exports.index')"
            :active="active_route.name == 'partner.exports.index'"
        >
            <PartnerSidebarDataCenterIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Data Center</div>
        </NavLink>
        <NavLink
            :href="route('partner.waivers.index')"
            :active="active_route.name == 'partner.waiver.index'"
        >
            <PartnerSidebarInstructorIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Waivers</div>
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
            <PartnerSidebarSettingsIcon class="w-8 h-8 overflow-visible" />
            <div class="overflow-hidden whitespace-nowrap overflow-ellipsis transition-opacity" :class="{'opacity-0': collapsed}">Settings</div>
        </NavLink>
    </div>
</template>
