<script setup>
import { Link } from "@inertiajs/vue3";
import AsideTabs from "@/Components/AsideTabs.vue";
</script>

<template>
    <AsideTabs>
        <template #tabsList>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark" :href="route('partner.settings.service-store-general')"
                    :class="$page.component.endsWith('ServiceStoreGeneral')
                            ? 'active'
                            : ''
                        ">General</Link>
            </li>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark" :href="route('partner.settings.service-store-header')"
                    :class="$page.component.endsWith('ServiceStoreHeader')
                            ? 'active'
                            : ''
                        ">Header & Footer</Link>
            </li>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark" :href="route('partner.settings.service-store-seo')"
                    :class="$page.component.endsWith('ServiceStoreSeo') ? 'active' : ''
                        ">SEO</Link>
            </li>
            <li class="py-1">
                <Link class="p-2 block hover:underline transition hover:bg-mainBg hover:text-dark" :href="route('partner.settings.service-store-code')"
                    :class="$page.component.endsWith('ServiceStoreCode') ? 'active' : ''
                        ">Custom code</Link>
            </li>
        </template>
    </AsideTabs>
</template>
