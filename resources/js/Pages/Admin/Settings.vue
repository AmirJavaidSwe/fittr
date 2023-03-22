<script setup>
import { ref } from 'vue';
import Tabs from '@/Components/Tabs.vue';
import Tab from '@/Components/Tab.vue';
import Packages from '@/Pages/Admin/Packages.vue';
import Admins from '@/Pages/Admin/Admins.vue';
import ButtonLink from '@/Components/ButtonLink.vue';

const active_tab_index = ref(0);
const switchTab = (val) => {
    active_tab_index.value = val;
};
</script>

<template>
    <Tabs 
        :tabs="['Packages', 'Admin users', 'Other Tab1', 'Other Tab2']"
        :active="active_tab_index"
        @tab-changed="switchTab"
        >
        <Tab :on="active_tab_index" :index="0">
            <ButtonLink :href="route('admin.packages.create')" type="primary">Add new</ButtonLink>
            <Packages :packages="$page.props.packages" />
        </Tab>
        <Tab :on="active_tab_index" :index="1">
            <Admins :admins="$page.props.admins" />
        </Tab>
        <Tab :on="active_tab_index" :index="2">
            <div>Other Tab1 content</div>
        </Tab>
        <Tab :on="active_tab_index" :index="3">
            <div>Other Tab2 content</div>
        </Tab>
    </Tabs>
</template>
