<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCamera } from "@fortawesome/free-solid-svg-icons"



</script>

<template>
   <div class="text-2xl font-extrabold">Account </div>
        <div class="flex flex-row justify-between bg-slate-200 pt-7 pb-3 p-2 pr-4 pl-4 my-4 border-b-2 border-gray-300">
            <div class="grow">
                <ul class="flex gap-10 ">
                    <li><a href="#" class="active">Profile</a></li>
                    <li><a href="#">Booking History</a></li>
                    <li><a href="#">Credits</a></li>
                    <li><a href="#">Classes</a></li>
                    <li><a href="#">Subscriptions</a></li>
                    <li><a href="#">Documents</a></li>
                    <li><a href="#">Family</a></li>
                    <li><a href="#">Payment Methods</a></li>
                    <li><a href="#">Notes</a></li>
                </ul>
            </div>
            <div class="grow">
                <ul class="flex justify-end">
                    <li><a href="#">Log Out</a></li>
                </ul>
            </div>
        </div>
        <div class="w-full visible  sm:hidden md:hidden lg:hidden xxlg:hidden">
                <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
                <div class="mt-2">
                <select id="country" name="country" autocomplete="country-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6">
                    <option>United States</option>
                    <option>Canada</option>
                    <option>Mexico</option>
                </select>
                </div>
            </div>
        <div class="flex flex-col md:flex-row justify-between">
            <div class="grow border-r-2 border-zinc-300 p-4 basis-1/4">
                <div class="m-auto block text-center">
                    <div class="rounded-full w-24 h-24 bg-sky-500/100 text-center m-auto relative">
                        <font-awesome-icon :icon="faEyeSlash" style="color: #b0b2b5"/>
                        <span class="shadow-2xl w-8 h-8 block bg-white rounded-full absolute top-[56px] right-[-8px]">
                            <font-awesome-icon :icon="faCamera" style="color: #5667DD" class="pt-2"/>
                        </span>
                    </div>
                    <h4 class="text-3xl font-extrabold mt-5 mb-5">Phil Parker</h4>
                    <button class="rounded-full bg-[#f0f0f0] p-3">Save Changes</button>
                </div>
                <div class="flex justify-center w-100 pt-10">
                    <div class="border-r-2 border-zinc-300 block text-center pl-10 pr-10">
                        <h4 class="text-4xl font-semibold">21</h4>
                        <h5>Classes</h5>
                    </div>
                    <div class="border-r-2 border-zinc-300 block text-center pl-10 pr-10">
                        <h4 class="text-4xl font-semibold">238</h4>
                        <h5>Check-ins</h5>
                    </div>
                    <div class="border-r-2 border-zinc-300 block text-center pl-10 pr-10">
                        <h4 class="text-4xl font-semibold">10</h4>
                        <h5>Credits</h5>
                    </div>
                </div>
                <div class="block w-100 pt-16 text-center">
                    <h4 class="font-extrabold text-3xl text-center pb-5">Contact Information</h4>
                    <p class="text-2xl"><span>Email:</span><span>Phil@studio6ix.co</span></p> 
                    <p class="text-2xl"><span>Phone:</span><span>07971583863</span></p>    
                </div>
            </div>
            <div class="grow p-10 basis-1/2">
                <div class="font-boldtext-3xl text-lg mb-3 font-extrabold">Personal Details</div>
                <hr class="mt-5 mb-7">
                    <label class="block mt-4 mb-4">
                        <span class="text-gray-700 font-semibold">Name</span>
                         <input type="text" class="mt-1 block w-full rounded-lg bg-slate-200" placeholder="Name">
                    </label>
                    <label class="block mt-4 mb-4">
                        <span class="text-gray-700 font-semibold">Email</span>
                         <input type="text" class="mt-1 block w-full rounded-lg bg-slate-200" placeholder="Email">
                    </label>
                    <label class="block mt-4 mb-4">
                        <span class="text-gray-700 font-semibold">Phone</span>
                         <input type="text" class="mt-1 block w-full rounded-lg bg-slate-200" placeholder="Phone">
                    </label>
                    <hr class="mt-9  mb-7">
                    <div class="font-boldtext-3xl text-lg mb-3 font-extrabold">Address /  Contact</div>
                    <label class="block mt-4 mb-4">
                        <span class="text-gray-700 font-semibold">Address Line 1</span>
                         <input type="text" class="mt-1 block w-full rounded-lg bg-slate-200" placeholder="6 Denmark Road">
                    </label>
                    <label class="block mt-4 mb-4">
                        <span class="text-gray-700 font-semibold">Address Line 2</span>
                         <input type="text" class="mt-1 block w-full rounded-lg bg-slate-200" placeholder="Address Line 2">
                    </label>
            </div>
        </div>
</template>
