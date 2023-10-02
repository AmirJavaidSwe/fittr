<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    classtypes: {
        type: Object,
        required: true,
    },
});
const emit = defineEmits(["filter"]);
const filter = (param) => {
    emit("filter", param);
}
</script>

<template>
    <div class="flex justify-center my-7">
        <div class="overflow-x-auto">
            <ul class="flex flex-nowrap space-x-8">
                <li class="relative mb-4">
                    <Link href="javascript:;" class="nav-link whitespace-nowrap" @click="filter(false)"
                        >ALL</Link
                    >
                    <div class="nav-line"></div>
                </li>
                <template v-for="(item, index) in classtypes" :key="index">
                    <li class="relative mb-4">
                        <Link href="javascript:;" class="nav-link whitespace-nowrap" @click="filter(item.id)"
                            >{{ item.title }}</Link
                        >
                        <div class="nav-line"></div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>
