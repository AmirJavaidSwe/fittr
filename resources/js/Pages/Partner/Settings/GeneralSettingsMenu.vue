<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div v-if="false">
        <ul class="tabs-wrapper hidden">
            <li>
                <Link
                    :href="route('partner.settings.general-details')"
                    :class="
                        $page.component.endsWith('BusinessDetails') ? 'active' : ''
                    "
                    >Business Details</Link
                >
            </li>
            <li>
                <Link
                    :href="route('partner.settings.general-address')"
                    :class="
                        $page.component.endsWith('LegalAddress') ? 'active' : ''
                    "
                    >Legal Address</Link
                >
            </li>
            <li>
                <Link
                    :href="route('partner.settings.general-formats')"
                    :class="$page.component.endsWith('Formats') ? 'active' : ''"
                    >Formats</Link
                >
            </li>
        </ul>
    </div>
</template>
