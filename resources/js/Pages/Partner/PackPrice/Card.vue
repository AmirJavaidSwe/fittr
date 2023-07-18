<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import ButtonLink from '@/Components/ButtonLink.vue';
import { faGear } from '@fortawesome/free-solid-svg-icons';

const props = defineProps({
    price: {
        type: Object,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
});

defineEmits(['toggle', 'edit', 'delete']);

</script>
<template>
    <div class="shadow-md w-80">
        <div 
        class="flex font-medium items-center justify-between space-between"
        :class="{
            'bg-opacity-10 bg-black': !price.is_active,
            'bg-primary-300': price.type == 'one_time',
            'bg-secondary-500': price.type == 'recurring',
            }"
        >
            <div class="p-2 font-bold">{{label}}</div>
            <Dropdown align="right" width="48" :content-classes="['bg-gray-100', 'p-1', 'space-y-4']">
                <template #trigger>
                <font-awesome-icon :icon="faGear" class="cursor-pointer p-2" />
                </template>
                <template #content>
                    <ButtonLink 
                        @click="$emit('toggle')"
                        :styling="price.is_active ? 'default' : 'primary'"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        {{price.is_active ? 'Deactive' : 'Make active'}} 
                    </ButtonLink>
                    <ButtonLink 
                        @click="$emit('edit')"
                        styling="secondary"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        Edit
                    </ButtonLink>
                    <ButtonLink 
                        @click="$emit('delete')"
                        styling="danger"
                        size="small"
                        class="w-full"
                        type="button"
                        >
                        Delete 
                    </ButtonLink>
                </template>
            </Dropdown>
        </div>
        <div class="p-2">
            <!-- <div>ID: {{price.id}}</div> -->
            <div> 
                <span class="font-bold" :title="price.currency.toUpperCase()">{{price.price_formatted}}</span>
                <span v-if="price.interval_count">&nbsp;{{price.interval_human}}</span>
            </div>
            <div>{{'Pack is ' + (price.is_unlimited ? '' : 'not ') + 'unlimited'}}</div>
            <div>{{'Pack is ' + (price.is_fap ? '' : 'not ') + 'fap'}}</div>
            <div>{{'Pack is ' + (price.is_renewable ? '' : 'not ') + 'renewable'}}</div>
            <div>{{'Pack is ' + (price.is_intro ? 'intro' : 'not intro')}}</div>
        </div>
    </div>
</template>