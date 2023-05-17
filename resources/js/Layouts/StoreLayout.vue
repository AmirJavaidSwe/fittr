<script setup>
import { ref, computed, defineAsyncComponent } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AppHead from '@/Layouts/AppHead.vue';
import StoreMenu from '@/Layouts/StoreMenu.vue';
import LogoLetter from '@/Components/LogoLetter.vue';
import Banner from '@/Components/Banner.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { faHeart } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    business_seetings: {
        type: Object,
        required: true,
    },
});

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const toggleMenu = (v) => {
    showingNavigationDropdown.value = v;
};

const LoggedinMenu = defineAsyncComponent(() => {
    // const user = usePage().props.user;
    // if(!user) {
    //     return import('./MemberMenu.vue');
    // }

    //TODO: return either member or instructor menu

    // MemberMenu checks is user exist
    return import('./MemberMenu.vue');
});

const header = computed(() => {
  return usePage().props.header;
});

const logo_image_url = computed(() => {
    return props.business_seetings.logo ? usePage().props.asset_url + props.business_seetings.logo : null;
});

const favicon_type = ref('image/x-icon');
const favicon_image_url = computed(() => {
    if(!props.business_seetings.favicon){
        return null;
    }
    switch (true) {
        case props.business_seetings.favicon.endsWith('.png'):
            favicon_type.value="image/png";
            break;
        case props.business_seetings.favicon.endsWith('.svg'):
            favicon_type.value="image/svg+xml";
            break;
    }
    return usePage().props.asset_url + props.business_seetings.favicon;    
});

const headerIsArray = computed(() => {
  return Array.isArray(header.value);
})
</script>

<template>
    <div>
        <AppHead 
            :title="$page.props.page_title"
            :favicon_type="favicon_type"
            :favicon_image_url="favicon_image_url">
        </AppHead>

        <Banner />
        <FlashMessage :flash="$page.props.flash" :errors="$page.props.errors" />

        <div class="min-h-screen bg-gray-50">
            <!-- Desktop, flex -->
            <div class="md:flex md:flex-col md:h-screen">
                <!-- Navbar (Logo, navigation, drop/mobile navigation) -->
                <div class="md:flex md:flex-shrink-0">
                    <div class="flex items-center justify-between md:flex-shrink-0 bg-green-100 w-full">
                        <!-- Logo -->
                        <Link :href="business_seetings.logo_url ?? '/'" class="">
                            <div v-if="business_seetings.logo" class="h-20 w-56">
                                <img :src="logo_image_url" :alt="business_seetings.business_name" class="h-full">
                            </div>
                            <div v-else class="px-6 py-4 font-bold">
                                {{business_seetings.business_name ?? 'LOGO'}}
                            </div>
                        </Link>
                        <StoreMenu class="hidden md:flex gap-2" />
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
                                <StoreMenu class="bg-white space-y-4" />
                            </template>
                        </Dropdown>
                        <!-- Settings Dropdown -->
                        <div v-if="$page.props.user" class="relative flex-shrink-0">
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
                <!-- Body -->
                <div class="md:flex md:flex-grow md:overflow-hidden">
                    <!-- Menu -->
                    <LoggedinMenu />
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
