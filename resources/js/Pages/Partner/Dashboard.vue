<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            Partner Dashboard
        </template>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <Welcome />
        </div>
    </AppLayout>
</template>
