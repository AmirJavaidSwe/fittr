<script setup>
import Section from '@/Components/Section.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
const props = defineProps({
    locations: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Section bg="bg-transparent">
        <div class="text-xl mb-4">
            Our Facilities
        </div>
        <div class="flex flex-wrap gap-4 flex-1">
            <div v-for="item in locations" :key="item.id" class="bg-white p-4 rounded shadow-md flex-grow">
                <div class="font-bold mb-2">{{item.title}}</div>
                <div>desc: {{item.brief}}</div>
                <div v-for="image in item.images">
                    <img :src="image.url" :alt="image.original_filename" class="w-36">
                </div>
                <div>address_line_1: {{item.address_line_1}}</div>
                <div>address_line_2: {{item.address_line_2}}</div>
                <div>city: {{item.city}}</div>
                <div>postcode: {{item.postcode}}</div>
                <div>phone: {{item.tel}}</div>

                <div v-if="item.studios.length" class="font-bold mb-2">Studios under this location:</div>
                <div v-for="studio in item.studios" :key="studio.id" class="bg-blue-50 p-1 my-4">
                    <div class="font-semibold mb-1 text-lg">{{studio.title}}</div>
                    <div>{{studio.brief}}</div>
                </div>

                <div v-if="item.amenities.length" class="font-bold mb-2">Amenities</div>
                <div v-for="amenity in item.amenities" :key="amenity.id" class="bg-gray-50 p-1">
                    <div class="font-semibold mb-1 text-lg">{{amenity.title}}</div>
                    <div>{{amenity.contents}}</div>
                </div>
            </div>
        </div>

    </Section>
</template>
