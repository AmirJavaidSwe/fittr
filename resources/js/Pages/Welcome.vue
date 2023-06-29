
<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LogoLetter from '@/Components/LogoLetter.vue';
import GoogleIcon from '@/Icons/Google.vue';
import Banner from '@/Components/Banner.vue';
import Authpagesimages from "@/Components/Authpagesimages.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Welcome" />
    <Banner />

    <div class="min-h-screen py-30">
      <div class="mx-auto">
        <div class="flex flex-col lg:flex-row  rounded-xl mx-auto min-h-screen">
            <div class="w-full lg:w-1/2 py-16 px-12 bg-mainBg min-h-screen">
                <div class="flex justify-center pt-8 sm:pt-0">
                    <LogoLetter fill="#000000" class="w-48 h-48" />
                </div>
            
            <form action="#" class="w-64 xs:w-full block mx-auto max-[500px]:w-full">
                <div class="h-px mt-4">
                    <div class="flex rounded-md shadow-sm" role="group">
                        <Link :href="route('login')" class="justify-center w-2/4 inline-flex items-center disabled:opacity-25 transition font-semibold bg-secondary-500 hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-secondary-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Log in</Link>
                        <Link v-if="canRegister" :href="route('register')" class="justify-center w-2/4 ml-4 text-sm text-gray-700 dark:text-gray-500 border inline-flex items-center disabled:opacity-25 transition font-semibold hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Register</Link>
                    </div>
                        <div class="flex items-center mt-4 mb-4">
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                            <div class="flex-grow-0 mx-5 text font-bold">Or</div>
                            <div class="flex-grow bg bg-gray-400 h-px"></div>
                        </div>
                        <a :href="route('auth.google')" class="bg-transparent border border-grey flex font-medium p-4 rounded-md text-center w-full">
                            
                            <div class="flex-grow -ml-6"><GoogleIcon class="inline-block"/> Continue with Google</div>
                        </a>
                       
                    </div>
            </form>
          </div>
          
           <Authpagesimages>
                <template #imagetext>
                    <p class="text-primary-500">Lorem ipsum dolor sit amet consectetur. Adipiscing risus dignissim volutpat ut integer malesuada varius fringilla. Id lacus vel lectus viverra id feugiat. Et id sed vel tincidunt amet volutpat vulputate aliquet vitae. Faucibus adipiscing in dui arcu duis. Senectus semper donec dui sit eget ut facilisi ut.</p>
                </template>
           </Authpagesimages>
          
        </div>
      </div>
    </div>





    <!-- <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center pt-8 sm:pt-0">
                <LogoLetter fill="#000000" class="w-48 h-48" />
            </div>
            <div v-if="canLogin" class="flex-wrap justify-center px-6 py-4 gap-6">
                <Link v-if="$page.props.user" :href="route($page.props.user.dashboard_route)" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</Link>

                <template v-else>
                    <div>
                        <Link :href="route('login')" class="inline-flex items-center disabled:opacity-25 transition font-semibold bg-secondary-500 hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-secondary-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Log in</Link>
                        <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 dark:text-gray-500 border inline-flex items-center disabled:opacity-25 transition font-semibold hover:bg-secondary-100 focus:bg-secondary-100 active:bg-secondary-600 text-dark active:text-white rounded-md border border-500 hover:bg-secondary-100 hover:text-secondary-500 hover:shadow-md hover:shadow-secondary-200 focus:shadow-md focus:text-secondary-500 focus:shadow-secondary-100 active:shadow-none px-4 h-11 text-sm">Register</Link>
                    </div>
                    
                    <div class="h-px mt-4">
                        <div class="flex items-center">
                            <div class="flex-grow bg bg-gray-300 h-px"></div>
                            <div class="flex-grow-0 mx-5 text">Or</div>
                            <div class="flex-grow bg bg-gray-300 h-px"></div>
                        </div>
                        <a :href="route('auth.google')" class="bg-transparent border flex font-medium p-4 rounded text-center w-full">
                            <GoogleIcon />
                            <div class="flex-grow -ml-6">Continue with Google</div>
                        </a>
                       
                    </div>
                    
                   
                </template>
            </div>
        </div>
    </div> -->
</template>
