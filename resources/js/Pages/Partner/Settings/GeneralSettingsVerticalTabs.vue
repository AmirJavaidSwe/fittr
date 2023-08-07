<script setup>
import { Link } from "@inertiajs/vue3";
import AsideTabs from "@/Components/AsideTabs.vue";
</script>

<template>
    <AsideTabs>
        <template #tabsList>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark"
                    :href="route('partner.settings.general-details')"
                    :class="
                        $page.component.endsWith('BusinessDetails') ? 'active' : ''
                    "
                    >Business Details</Link
                >
                <!-- <a class="p-2 block hover:underline transition active" href="#">Business Details</a> -->
            </li>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark"
                :href="route('partner.settings.general-address')"
                :class="
                    $page.component.endsWith('LegalAddress') ? 'active' : ''
                "
                >Legal Address</Link
            >
            </li>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark"
                :href="route('partner.settings.general-formats')"
                :class="$page.component.endsWith('Formats') ? 'active' : ''"
                >Formats</Link
            >
            </li>

        </template>
    </AsideTabs>
</template>
