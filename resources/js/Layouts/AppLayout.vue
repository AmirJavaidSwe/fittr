<script setup>
import { ref, computed, defineAsyncComponent } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, usePage } from '@inertiajs/inertia-vue3';
import AppHead from '@/Layouts/AppHead.vue';
import LogoLetter from '@/Components/LogoLetter.vue';
import Banner from '@/Components/Banner.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { faHeart } from '@fortawesome/free-solid-svg-icons';

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    Inertia.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    Inertia.post(route('logout'));
};

const toggleMenu = (v) => {
    showingNavigationDropdown.value = v;
};

const MainMenu = defineAsyncComponent(() => {
    const is_partner = usePage().props.value.user.is_partner;
    return is_partner ? import('./PartnerMenu.vue') : import('./AdminMenu.vue');
});

const header = computed(() => {
  return usePage().props.value.header;
})
const headerIsArray = computed(() => {
  return Array.isArray(header.value);
})
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
        <FlashMessage :flash="$page.props.flash" />

        <div class="min-h-screen bg-gray-50">
            <!-- Desktop, flex -->
            <div class="md:flex md:flex-col md:h-screen">
                <div class="md:flex md:flex-shrink-0">
                    <div class="flex items-center justify-between px-6 py-4 md:flex-shrink-0 md:justify-center md:w-56 bg-gray-100">
                        <!-- Logo -->
                        <Link :href="route($page.props.user.dashboard_route)" class="mt-1">
                            <LogoLetter class="w-24 h-8" fill="#f00" />
                        </Link>
                        <!-- Mobile toggle, visible md and smaller -->
                        <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1']" @toggled="toggleMenu">
                            <template #trigger>
                                <button class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                                    <svg
                                        class="h-6 w-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                            </button>
                            </template>
                            <template #content>
                                <MainMenu />
                            </template>
                        </Dropdown>
                    </div>
                    <div class="bg-white flex items-center justify-between md:px-6 md:py-0 md:text-md p-4 shadow text-sm w-full z-0 gap-4">
                        <!-- Page Heading / Menu item name-->
                        <header class="bg-white ">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                    <div v-if="headerIsArray" class="flex flex-wrap gap-2">
                                        <div v-for="item in header">
                                            <Link v-if="item.link" :href="item.link" class="text-blue-600">{{item.title}}</Link>
                                            <span v-else>
                                                {{item.title}}
                                            </span>
                                        </div>
                                    </div>
                                    <span v-else>{{header}}</span>
                                </h2>
                            </div>
                        </header>
                        <!-- Settings Dropdown -->
                        <div class="relative flex-shrink-0">
                            <Dropdown align="right" width="48" :content-classes="['bg-white']">
                                <template #trigger>
                                    <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-gray-100 rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                    </button>

                                    <span v-else class="inline-flex rounded-md">
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                            {{ $page.props.user.name }}

                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="px-4 py-2 border-b bg-gray-50">
                                        <div class="font-bold">{{$page.props.user.name}}</div>
                                        <div class="text-gray-400">{{$page.props.user.email}}</div>
                                    </div>

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

                                    <div class="border-t border-gray-100" />

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
                </div>
                <div class="md:flex md:flex-grow md:overflow-hidden">
                    <!-- Menu -->
                    <MainMenu />
                    <!-- Page Content -->
                    <div class="md:flex-1 overflow-y-auto">
                        <main class="py-8 sm:px-6 lg:px-8 space-y-8">
                            <slot />
                        </main>
                    </div>
                </div>
                <!-- footer -->
                <footer class="bg-gray-100 p-2 text-center text-gray-400 text-xs">MADE WITH <font-awesome-icon :icon="faHeart" class="mx-2" /> IN LONDON</footer>
            </div>
        </div>
    </div>
</template>
