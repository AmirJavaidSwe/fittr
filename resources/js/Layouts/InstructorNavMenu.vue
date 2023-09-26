<script setup>
import { router, Link, usePage } from '@inertiajs/vue3';
</script>
<template>
    <div class="flex justify-center my-7">
        <ul class="flex flex-wrap  md:space-x-0 flex-col md:flex-row gap-x-8">
            <li class="relative">
                <Link href="#" class="nav-link">ALL</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">BARRE</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">BREATHWORK</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">CIRCUITS</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">CYCLE</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">FIT</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">Outdoors</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">Strength</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">Stretch & Mobility</Link>
                <div class="nav-line"></div>
            </li>
            <li class="relative">
                <Link href="#" class="nav-link">Yoga</Link>
                <div class="nav-line"></div>
            </li>
        </ul>
    </div>
</template>
<style>
.nav-link {
    text-decoration: none;
    color: #333;
    padding-bottom: 8px;
    transition: color 0.3s;
}

.nav-link:hover {
    color: #6B705C;
}

.nav-line {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0;
    height: 2px;
    background-color: transparent;
    transition: width 0.3s, background-color 0.3s, font-weight 0.3s;
}

.nav-link:hover + .nav-line {
    width: 100%;
    background-color: #6B705C;
    font-weight: bold;
    line-height: 6px;
}
</style>
