
<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <ul class="font-medium text-base">
        <li>
            Imports
            <!-- <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.imports.index')"
                :class="$page.component.endsWith('Import') ? 'font-bold bg-gray-200' : ''">Imports</Link> -->
        </li>
        <li>
            <Link 
                class="p-2 block hover:underline transition"
                :href="route('partner.exports.index')"
                :class="$page.component.endsWith('Export')  ? 'font-bold bg-gray-200' : ''">Exports</Link>
        </li>
          </ul>
</template>