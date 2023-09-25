<script setup>
import { ref, watch, computed, defineAsyncComponent } from "vue";
import { router, Link, usePage } from "@inertiajs/vue3";
import AppHead from "@/Layouts/AppHead.vue";
import LogoLetter from "@/Components/LogoLetter.vue";
import LogoSign from "@/Components/LogoSign.vue";
import LogoFull from "@/Components/LogoFull.vue";
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
import { useSwal } from "@/Composables/swal";
import { useWindowSize } from '@/Composables/window_size';
import PasswordCreationModal from "./PasswordCreationModal.vue";

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

const { screen } = useWindowSize();

// Sidebar Collapse
const sidebarCollapsed = ref(screen.value == 'sm' || screen.value == 'mobile');
const sidebarClasses = computed(() => ({
    'w-12': sidebarCollapsed.value,
    'w-48': !sidebarCollapsed.value,
}))

watch(screen, (newVal, oldVal) => {
    sidebarCollapsed.value = (newVal == 'sm' || newVal == 'mobile') ? true : false
})

// Back function
let back = function (e) {
    e.preventDefault();
    window.history.back();
};

const flash = computed(() => usePage().props.flash);
const errors = computed(() => usePage().props.errors);

const { toast } = useSwal({flash, errors});
</script>

<template>
    <AppHead :title="$page.props.page_title">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
    </AppHead>

    <Banner />
    <!-- <FlashMessage
        :flash="$page.props.flash"
        :errors="$page.props.errors"
    /> -->

    <div class="bg-gray-50 h-screen flex relative">
        <!-- Sidebar -->
        <aside class="bg-primary-500 duration-500 h-full overflow-x-hidden overflow-y-auto transition-all relative p-2 flex-shrink-0 no-scrollbar" :class="sidebarClasses">
                <Link :href="route($page.props.user.dashboard_route)" class="flex items-center gap-2 md:py-6 py-4">
                    <div class="w-8 h-8">
                        <LogoSign class=" w-8 h-8 text-white " />
                    </div>
                    <LogoLetter class="flex-shrink-0 h-8 text-white w-auto"  />
                </Link>

            <MainMenu class="space-y-8" :collapsed="sidebarCollapsed" />
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col flex-grow overflow-auto min-h-full h-full relative">
            <!-- Page Heading / Menu item name-->
            <header class="bg-primary-500 md:bg-white flex items-center justify-between md:px-6 md:py-6 md:text-md p-4 shadow text-sm w-full gap-4">
                <div class="flex items-center">
                    <!-- Menu toggle -->
                    <button
                        type="button"
                        class="inline-flex items-center md:text-gray-700 mr-4 text-white transition"
                        @click="sidebarCollapsed = !sidebarCollapsed"
                    >
                        <font-awesome-icon :icon="faBars" class="w-6 h-6" />
                    </button>
                    <div class="max-w-7xl mx-auto">
                        <h2 class="font-semibold text-base md:text-xl xl:text-2xl 2xl:text-3xl text-white md:text-gray-800 leading-4">
                            <div v-if="headerIsArray" class="flex flex-wrap gap-2 items-center">
                                <Link
                                    href="#"
                                    @click="back"
                                    class="bg-primary-100 rounded-md items-center justify-center text-lg text-primary-500 mr-1 w-8 h-8 lg:w-11 lg:h-11 lg:text-xl hidden md:flex"
                                >
                                    <font-awesome-icon :icon="faChevronLeft"/>
                                </Link>
                                <div v-for="item in header">
                                    <Link
                                        v-if="item.link"
                                        :href="item.link"
                                        class="text-white md:text-primary-500 md:hover:text-primary-900"
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
                    <div v-if="$page.props.user" class="relative flex-shrink-0">
                        <Dropdown
                            align="right"
                            width="48"
                            :content-classes="['bg-white']"
                        >
                            <template #trigger>
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="flex items-center justify-center">
                                    <div class="hidden md:block pr-4 text-right">
                                        <div class="font-bold text-white md:text-dark md:text-sm xl:text-md">
                                            {{$page.props.user.name}}
                                        </div>
                                        <div class="text-gray-400 hidden md:block md:text-sm xl:text-md">
                                            {{$page.props.user.email}}
                                        </div>
                                    </div>
                                    <Avatar :initials="$page.props.user.initials" :image-url="$page.props.user.profile_photo_url" size="medium" />
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
            <div class="flex-grow">
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
    <PasswordCreationModal />
</template>
