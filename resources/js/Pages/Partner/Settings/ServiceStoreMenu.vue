<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <ul class="tabs-wrapper">
        <li>
            <Link
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-general')"
                :class="
                    $page.component.endsWith('ServiceStoreGeneral')
                        ? 'active'
                        : ''
                "
                >General</Link
            >
        </li>
        <li>
            <Link
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-header')"
                :class="
                    $page.component.endsWith('ServiceStoreHeader')
                        ? 'active'
                        : ''
                "
                >Header & Footer</Link
            >
        </li>
        <li>
            <Link
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-seo')"
                :class="
                    $page.component.endsWith('ServiceStoreSeo') ? 'active' : ''
                "
                >SEO</Link
            >
        </li>
        <li>
            <Link
                class="p-2 block hover:underline transition"
                :href="route('partner.settings.service-store-code')"
                :class="
                    $page.component.endsWith('ServiceStoreCode') ? 'active' : ''
                "
                >Custom code</Link
            >
        </li>
    </ul>
</template>
