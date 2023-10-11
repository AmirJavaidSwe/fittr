<script setup>
import { ref } from "vue";
import SelectInput from "@/Components/SelectInput.vue";

const activeTab = ref("tab-1");

const changeTab = (param) => {
    activeTab.value = param;
};
const tabMenu = ref([
    {
        id: 'tab-1',
        name: 'Profile',
    },
    {
        id: 'tab-2',
        name: 'Booking History',
    },
    {
        id: 'tab-3',
        name: 'Credits',
    },
    {
        id: 'tab-4',
        name: 'Classes',
    },
    {
        id: 'tab-5',
        name: 'Subscriptions',
    },
    {
        id: 'tab-6',
        name: 'Documents',
    },
    {
        id: 'tab-7',
        name: 'Family',
    },
    {
        id: 'tab-8',
        name: 'Payment Methods',
    },
    {
        id: 'tab-9',
        name: 'Notes',
    },
])
</script>
<template>
    <div class="text-2xl font-extrabold flex justify-between p-4 pb-0">
        <span>Account</span>
        <!-- <span class="md:hidden"><a href="javascript:;">Log Out</a></span> -->
    </div>

    <div
        class="bg-mainbg text-sm font-medium text-center text-gray-500 border-b border-gray-200 hidden md:block"
    >
        <div class="flex justify-between">
            <ul class="flex flex-wrap">
                <template v-for="(item, index) in tabMenu" :key="index">
                    <li class="mr-2">
                        <a
                            href="javascript:;"
                            @click="changeTab(item.id)"
                            class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                            :class="{
                                'active text-primary-500 border-b-2 border-primary-500':
                                    activeTab == item.id,
                            }"
                            >{{ item.name }}</a
                        >
                    </li>
                </template>
                <!-- <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-2')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-2',
                        }"
                        >Booking History</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-3')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-3',
                        }"
                        >Credits</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-4')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-4',
                        }"
                        >Classes</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-5')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-5',
                        }"
                        >Subscriptions</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-6')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-6',
                        }"
                        >Documents</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-7')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-7',
                        }"
                        >Family</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-8')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-8',
                        }"
                        >Payment Methods</a
                    >
                </li>
                <li class="mr-2">
                    <a
                        href="javascript:;"
                        @click="changeTab('tab-9')"
                        class="inline-block p-4 text-md font-semibold tracking-wider rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        :class="{
                            'active text-primary-500 border-b-2 border-primary-500':
                                activeTab == 'tab-9',
                        }"
                        >Notes</a
                    >
                </li> -->
            </ul>
            <ul class="flex items-center">
                <li class="">
                    <a
                        href="javascript:;"
                        class="inline-block p-4 text-md font-semibold border-b-2 border-transparent rounded-t-lg hover:text-primary-500 hover:border-primary-500"
                        >Log Out</a
                    >
                </li>
            </ul>
        </div>
    </div>
    <div class="w-full md:hidden p-4">
        <SelectInput
            v-model="activeTab"
            id="country_id"
            :options="tabMenu"
            option_value="id"
            option_text="name"
            class="mt-1 block w-full"
        >
        </SelectInput>
        <!-- <select
            id="country"
            name="country"
            autocomplete="country-name"
            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring focus:ring-primary-500"
        >
            <option>Profile</option>
            <option>Booking History</option>
            <option>Credits</option>
            <option>Classes</option>
            <option>Subscriptions</option>
            <option>Documents</option>
            <option>Family</option>
            <option>Payment Methods</option>
            <option>Notes</option>
        </select> -->
    </div>
</template>
