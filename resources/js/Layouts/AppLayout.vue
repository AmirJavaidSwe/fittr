<script setup>
import { ref, computed, defineAsyncComponent } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";
import AppHead from "@/Layouts/AppHead.vue";
import LogoLetter from "@/Components/LogoLetter.vue";
import Banner from "@/Components/Banner.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import {
    faBars,
    faChevronLeft,
    faChevronDown,
    faHeart,
    faTimes,
} from "@fortawesome/free-solid-svg-icons";
import Notifications from "@/Icons/Notifications.vue";
import Messages from "@/Icons/Messages.vue";
import Avatar from "@/Components/Avatar.vue";

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};

const toggleMenu = (v) => {
    showingNavigationDropdown.value = v;
};

const MainMenu = defineAsyncComponent(() => {
    const is_partner = usePage().props.user.is_partner;
    return is_partner ? import("./PartnerMenu.vue") : import("./AdminMenu.vue");
});

const header = computed(() => {
    return usePage().props.header;
});
const headerIsArray = computed(() => {
    return Array.isArray(header.value);
});

// Sidebar Collapse
const sidebarCollapsed = ref(false);

// Back function
let back = function (e) {
    e.preventDefault();
    window.history.back();
};
</script>

<template>
    <div>
        <AppHead :title="$page.props.page_title">
            <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
            <link rel="manifest" href="/site.webmanifest">
        </AppHead>

        <Banner />
        <FlashMessage
            :flash="$page.props.flash"
            :errors="$page.props.errors"
        />

        <div class="min-h-screen bg-gray-50 overflow-hidden">
            <!-- Desktop, flex -->
            <div class="h-screen">
                <div class="flex h-full relative">
                    <!-- Sidebar -->
                    <div
                        class="transition-all duration-500 fixed md:relative z-[99] top-0 left-0 h-full hidden md:block"
                        :class="{
                            'md:w-0': sidebarCollapsed,
                            'md:w-56 xl:w-[220px] 2xl:w-[260px]':
                                !sidebarCollapsed,
                            '-translate-x-full md:-translate-x-56 xl:-translate-x-[220px] 2xl:-translate-x-[260px]':
                                sidebarCollapsed,
                            'translate-x-0': !sidebarCollapsed,
                        }"
                    >
                        <div class="w-full md:w-56 xl:w-[220px] 2xl:w-[260px] h-full bg-primary-500">
                            <div class="flex items-center flex-shrink-0">
                                <!-- Logo -->
                                <div class="relative w-full px-6 py-4 xl:py-6 2xl:py-8">
                                    <Link :href="route($page.props.user.dashboard_route)" class="mt-1">
                                        <LogoLetter
                                            class="w-auto h-10"
                                            fill="#fff"
                                        />
                                    </Link>

                                    <button
                                        type="button"
                                        class="border-0 p-0 bg-transparent text-white absolute top-2.5 right-2.5 md:hidden"
                                        @click="
                                            sidebarCollapsed = !sidebarCollapsed
                                        "
                                    >
                                        <font-awesome-icon
                                            :icon="faTimes"
                                            class="w-6 h-6 transition-all duration-100"
                                            :class="{
                                                'opacity-0': sidebarCollapsed,
                                            }"
                                        />
                                    </button>
                                </div>
                            </div>

                            <!-- Menu -->
                            <MainMenu class="block" />
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="flex flex-col flex-1 overflow-hidden md:relative md:z-10 h-full md:h-auto">
                        <!-- Page Heading / Menu item name-->
                        <header class="bg-primary-500 md:bg-white flex items-center justify-between md:px-6 md:py-6 md:text-md p-4 shadow text-sm w-full gap-4 relative">
                            <div class="flex items-center">
                                <!-- Menu toggle -->
                                <button
                                    type="button"
                                    class="text-white md:text-dark mr-4 hidden md:block"
                                    @click="
                                        sidebarCollapsed = !sidebarCollapsed
                                    "
                                >
                                    <font-awesome-icon :icon="faBars" class="w-6 h-6 2xl:w-7 2xl:h-7" />
                                </button>
                                <!-- Mobile toggle, visible md and smaller -->
                                <Dropdown
                                    align="left"
                                    width="56"
                                    :content-classes="['bg-gray-100', 'p-1']"
                                    @toggled="toggleMenu"
                                >
                                    <template #trigger>
                                        <button
                                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition text-white"
                                        >
                                            <font-awesome-icon
                                                :icon="faBars"
                                                class="mr-3 w-6 h-6 2xl:w-7 2xl:h-7"
                                            />
                                        </button>
                                    </template>
                                    <template #content>
                                        <MainMenu class="hidden:md" />
                                    </template>
                                </Dropdown>
                                <div class="max-w-7xl mx-auto">
                                    <h2 class="font-semibold text-base md:text-xl xl:text-2xl 2xl:text-3xl text-white md:text-gray-800 leading-tight">
                                        <div v-if="headerIsArray" class="flex flex-wrap gap-2 items-center">
                                            <Link
                                                href="#"
                                                @click="back"
                                                class="headerIcon"
                                            >
                                                <font-awesome-icon :icon="faChevronLeft"/>
                                            </Link>
                                            <div v-for="item in header">
                                                <Link
                                                    v-if="item.link"
                                                    :href="item.link"
                                                    class="text-blue-600"
                                                    >{{ item.title }}</Link
                                                >
                                                <span v-else>
                                                    {{ item.title }}
                                                </span>
                                            </div>
                                        </div>
                                        <span v-else>{{ header }}</span>
                                    </h2>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <!-- Setting Links -->
                                <ul class="gap-5 pr-6 py-2 mr-5 border-r-2 border-gray-200 hidden md:flex">
                                    <li>
                                        <a href="#" class="text-white md:text-dark" >
                                            <Notifications />
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-white md:text-dark" >
                                            <Messages />
                                        </a>
                                    </li>
                                </ul>

                                <!-- Settings Dropdown -->
                                <div class="relative flex-shrink-0">
                                    <Dropdown
                                        align="right"
                                        width="48"
                                        :content-classes="['bg-white']"
                                    >
                                        <template #trigger>
                                            <!-- REDO THIS -->
                                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex items-center justify-center">
                                                <div class="pr-4 text-right">
                                                    <div class="font-bold text-white md:text-dark md:text-sm xl:text-md">
                                                        {{$page.props.user.name}}
                                                    </div>
                                                    <div class="text-gray-400 hidden md:block md:text-sm xl:text-md">
                                                        {{$page.props.user.email}}
                                                    </div>
                                                </div>
                                                <Avatar :title="$page.props.user.name"/>
                                                <!-- <img class="w-8 h-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" /> -->
                                            </div>

                                            <span v-else class="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm lg:text-md leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                                >
                                                    {{ $page.props.user.name }}
                                                    <font-awesome-icon :icon="faChevronDown" size="xs" class="ml-2" />
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class="px-4 py-2 border-b bg-gray-50 md:hidden">
                                                <div class="font-bold">
                                                    {{ $page.props.user.name }}
                                                </div>
                                                <div class="text-gray-400">
                                                    {{ $page.props.user.email }}
                                                </div>
                                            </div>
                                            <!-- Account Management -->

                                            <DropdownLink :href="route('profile.show')">
                                                Profile
                                            </DropdownLink>

                                            <!-- <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                                API Tokens
                                            </DropdownLink> -->

                                            <DropdownLink v-if="$page.props.user.is_partner" :href="route('partner.subscriptions.index')">
                                                Billing
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.user.is_partner" :href="route('partner.contact.index')">
                                                Contact support
                                            </DropdownLink>

                                            <div class="border-t border-gray-100"/>

                                            <!-- Authentication -->
                                            <form @submit.prevent="logout">
                                                <DropdownLink as="button">
                                                    Log Out
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </header>
                        <!-- Page Content -->
                        <div class="md:flex-1 overflow-y-auto bg-mainBg h-full md:h-auto">
                            <main class="py-8 px-4 sm:px-6">
                                <slot />
                            </main>
                        </div>
                        <!-- footer -->
                        <footer class="bg-gray-100 h-applayout_footer flex items-center justify-center text-center text-gray-400 text-xs">
                            MADE WITH
                            <font-awesome-icon :icon="faHeart" class="mx-2" />
                            IN LONDON
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
