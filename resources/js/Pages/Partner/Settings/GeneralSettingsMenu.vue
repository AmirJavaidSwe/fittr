
<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <ul class="font-medium text-base">
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.general-details')"
                :class="$page.component.endsWith('BusinessDetails') ? 'font-bold bg-gray-200' : ''">Business Details</Link>
        </li>
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.general-address')"
                :class="$page.component.endsWith('LegalAddress')  ? 'font-bold bg-gray-200' : ''">Legal Address</Link>
        </li>
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.general-formats')" 
                :class="$page.component.endsWith('Formats')  ? 'font-bold bg-gray-200' : ''">Formats</Link>
        </li>
    </ul>
</template>