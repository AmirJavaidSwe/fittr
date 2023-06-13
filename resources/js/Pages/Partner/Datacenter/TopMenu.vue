<script setup>
import { Link } from "@inertiajs/vue3";
import CalendarIcon from "@/Icons/Calendar.vue";
import ExportIcon from "@/Icons/Export.vue";
import { faLink } from "@fortawesome/free-solid-svg-icons";
</script>

<template>
    <ul class="tabs-wrapper">
        <li>
            <Link href="#">
                <CalendarIcon />
                Imports
            </Link>
        </li>
        <li>
            <Link class="active" :href="route('partner.exports.index')">
                <ExportIcon />
                Exports
            </Link>
        </li>
    </ul>

    <!--    <ul class="font-medium text-base">-->
    <!--        <li>-->
    <!--            Imports-->
    <!--            &lt;!&ndash; <Link-->
    <!--                class="p-2 block hover:underline transition"-->
    <!--                :href="route('partner.imports.index')"-->
    <!--                :class="$page.component.endsWith('Import') ? 'font-bold bg-gray-200' : ''">Imports</Link> &ndash;&gt;-->
    <!--        </li>-->
    <!--        <li>-->
    <!--            <Link-->
    <!--                class="p-2 block hover:underline transition"-->
    <!--                :href="route('partner.exports.index')"-->
    <!--                :class="$page.component.endsWith('Export')  ? 'font-bold bg-gray-200' : ''">Exports</Link>-->
    <!--        </li>-->
    <!--    </ul>-->
</template>
