
<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <ul class="font-medium text-base">
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-general')"
                :class="$page.component.endsWith('ServiceStoreGeneral') ? 'font-bold bg-gray-200' : ''">General</Link>
        </li>
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-header')"
                :class="$page.component.endsWith('ServiceStoreHeader')  ? 'font-bold bg-gray-200' : ''">Header & Footer</Link>
        </li>
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-seo')" 
                :class="$page.component.endsWith('ServiceStoreSeo')  ? 'font-bold bg-gray-200' : ''">SEO</Link>
        </li>
    </ul>
</template>