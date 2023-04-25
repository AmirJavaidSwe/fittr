
<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex justify-center mr-5">
        <fieldset
            class="grid grid-cols-2 gap-x-1 rounded p-1 text-center text-xs font-semibold leading-5 ring-1 ring-inset ring-gray-200">
            <legend class="sr-only">Types</legend>

            <!-- Checked: "bg-indigo-600 text-white", Not Checked: "text-gray-500" -->
            <Link class="cursor-pointer rounded py-1 px-2.5">
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     class="w-5 h-5 inline mr-1">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121
                                     7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25
                                     0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0
                                     2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5
                                     15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0
                                     2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0
                                     2.25h.008v.008H16.5V15z" />
                </svg>
                <span>Imports</span>
            </Link>

            <!-- Checked: "bg-indigo-600 text-white", Not Checked: "text-gray-500" -->
            <Link class="cursor-pointer rounded py-1 px-2.5 bg-indigo-600 text-white"
                  :href="route('partner.exports.index')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5 inline mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75
                                     7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621
                                     0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                </svg>
                <span>Exports</span>
            </Link>
        </fieldset>
    </div>


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
