<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCamera, faUser } from "@fortawesome/free-solid-svg-icons"
    


</script>

<template>
   <div class="text-2xl font-extrabold pl-4 pr-4 flex justify-between">
        <span>Account</span>
        <span class="md:hidden lg:hidden xxlg:hidden"><a href="#">Log Out</a></span>
    </div>
        <div class=" flex-row justify-between bg-slate-200 pt-7 pb-3 p-2 pr-4 pl-4 my-4 border-b-2 border-gray-300 hidden md:flex">
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
        <div class="w-full visible md:hidden lg:hidden xxlg:hidden pl-3 pr-3 md:pl-0,pr-0">
                <div class="mt-2">
                <select id="country" name="country" autocomplete="country-name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-full sm:text-sm sm:leading-6">
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
           
        <div class="flex flex-col md:flex-row justify-between">
            <div class="grow border-r-2 border-zinc-300 p-4 basis-1/4">
                <div class="m-auto block text-center">
                    
                    <div class="rounded-full w-24 h-24 bg-slate-300 text-center m-auto relative pt-5">
                        <input type="file" name="file" id="file" class="hidden" />
                        <img :src="image" style="width:100%;" alt="">
                        <font-awesome-icon :icon="faUser" style="color: #ffffff"  class="text-5xl"/>
                        <span class="shadow-2xl w-8 h-8 block bg-white rounded-full absolute top-[56px] right-[-8px]">
                           <label for="file" class="cursor-pointer"><font-awesome-icon :icon="faCamera" style="color: #5667DD" class="pt-2"/></label>
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
