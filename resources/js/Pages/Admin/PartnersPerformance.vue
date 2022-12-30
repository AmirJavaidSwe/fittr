<script>
import AppLayout from '@/Layouts/AppLayout.vue'; 
export default {
    // Using a render function
    layout: (h, page) => h(AppLayout, () => page),
}
</script>
<script setup>
import Welcome from '@/Components/Welcome.vue';
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <Welcome />
    </div>
</template>
