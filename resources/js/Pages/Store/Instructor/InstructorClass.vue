
<script setup>
import InstructorNavMenu from '@/Layouts/InstructorNavMenu.vue';
import { computed, onBeforeMount, onMounted, ref, reactive } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import '@vueform/multiselect/themes/tailwind.css'
const subdomain = ref(usePage().props.business_settings.subdomain);
const props = defineProps({
    instructor: {
        type: Object,
        required: true,
    },
});


</script>
<template>
    <Section bg="bg-transparent mx-20">
           <div class="mt-6 mb-6 text-center">
            <p class="text-3xl font-bold">Instructors</p>
            <p class="text-lg font-bold">Getting you powered up</p>
          </div>
          <div class="flex justify-center items-center">
            <div class="w-full md:w-[80%] lg:w-[60%] xl:w-[50%]">
              <div class="overflow-hidden">
                <div class="mx-auto md:flex">
                  <div class="flex items-center justify-center">
                    <img src="/images/gymwoman.jpg" alt="Card Image" class=" items-center w-[300px] h-[250px] md:h-auto">
                  </div>
                  <div class="md:w-1/2 px-6 py-4">
                    <div class="text-xl font-semibold mb-2">{{ instructor.full_name }}</div>
                    <div class="text-sm font-semibold mb-2">CYCLE / BARRE</div>
                    <div class="text-gray-600 overflow-y-auto max-h-[400px]">
                      Sophie is our Head of Barre and one of our Rhythm Associate Master Trainers. You can find Sophie on the timetable at Bank, Covent Garden and Digme at home. Sophie’s classes are always full of sparkle, positivity, and endorphin-boosting moves & music! With a professional background in ballet and theatre, Sophie’s passion for movement knows no bounds. She prides herself on creating an environment that is inclusive of all abilities and a safe space for you to find the fun in fitness! However, don’t be fooled by her sweet aesthetic - whether it is Barre or Cycle, Sophie will ALWAYS (safely) push you to new limits. You will leave her classes feeling energized, incredibly proud of yourselves and wanting more! Sophie is not currently on the timetable as she is having a baby due 14th March 23, however she is still very much involved in her role as Head of Barre and looks forward to being back in the studio with us really soon!
                    </div>
                    <div class="mt-4">
                      <!-- <a href="#" class="text-blue-500 hover:underline">Read More</a> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="mb-4">
            <p class="text-2xl font-semibold mb-8">Next sessions with {{ instructor.full_name }}</p>
          </div>
          <div class="border border-t border-gray-200 mb-4"></div>
    </Section>
</template>
