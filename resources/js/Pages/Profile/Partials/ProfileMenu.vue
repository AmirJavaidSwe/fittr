<script setup></script>
<template>
    <div class="text-2xl font-extrabold flex justify-between">
        <span>Account</span>
        <span class="md:hidden lg:hidden xxlg:hidden"><a href="#">Log Out</a></span>
    </div>


    <div class="bg-mainbg text-sm font-medium text-center text-gray-500 border-b border-gray-200">
        <div class="flex justify-between">
            <ul class="flex flex-grow overflow-x-scroll">
                <li class="mr-2 -mb-px">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Profile</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 text-primary-500 border-b-2 border-primary-500 rounded-t-lg active hover:text-primary-500 hover:border-primary-500"
                        aria-current="page">Booking History</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Credits</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Classes</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Subscriptions</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Documents</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Family</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Payment
                        Methods</a>
                </li>
                <li class="mr-2">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Notes</a>
                </li>
            </ul>
            <ul class="flex items-center">
                <li class="">
                    <a href="#"
                        class="inline-block p-6 border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500">Log
                        Out</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="w-full visible md:hidden lg:hidden xxlg:hidden pl-3 pr-3 md:pl-0,pr-0">
        <div class="mt-2">
            <select id="country" name="country" autocomplete="country-name"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-full sm:text-sm sm:leading-6">
                <option>Profile</option>
                <option>Booking History</option>
                <option>Credits</option>
                <option>Classes</option>
                <option>Subscriptions</option>
                <option>Documents</option>
                <option>Family</option>
                <option>Payment Methods</option>
                <option>Notes</option>
            </select>
        </div>
    </div>
</template>
