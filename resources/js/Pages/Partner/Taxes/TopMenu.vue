<script setup>
import { Link } from "@inertiajs/vue3";
import CalendarIcon from "@/Icons/Calendar.vue";
import ExportIcon from "@/Icons/Export.vue";
import { faLink } from "@fortawesome/free-solid-svg-icons";
</script>

<template>
    <ul class="tabs-wrapper">
        <li>
            <Link :class="[route().current('partner.taxes.index') && 'active']"  :href="route('partner.taxes.index')">
                Taxes [0]
            </Link>
        </li>
        <li>
            <Link :class="[route().current('partner.charges.index') && 'active']" :href="route('partner.charges.index')">
                Charges [0]
            </Link>
        </li>
    </ul>
</template>
