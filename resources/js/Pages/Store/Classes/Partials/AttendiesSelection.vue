<script setup>
import { ref, watch } from "vue";

const props = defineProps(["user", "current_class", "is_family_booking"]);
const emit = defineEmits(["isFamilyBooking"]);

const selectedValue = ref(props.is_family_booking);

watch(
    () => props.is_family_booking,
    (newVal) => {
        selectedValue.value = newVal;
    }
);

watch(selectedValue, (newVal) => {
    emit("isFamilyBooking", { is_family_booking: parseInt(newVal) });
});
</script>
<template>
    <div class="text-right" v-if="props.user?.family?.length">
        <div
            class="relative inline-flex text-right items-center"
            v-if="!props.current_class.is_booked"
        >
            Booking for
            <select
                class="ml-5 border-0 focus:border-0 cursor-pointer mr-0 pr-7 text-primary-500"
                v-model="selectedValue"
            >
                <option value="1">Only Myself</option>
                <option value="2">Me and Others</option>
            </select>
        </div>
        <br />
        <small>
            <template v-if="selectedValue == 2">
                <span
                    class="cursor-pointer text-primary-500 pr-7"
                    @click="
                        $emit('isFamilyBooking', {
                            is_family_booking: parseInt(selectedValue),
                        })
                    "
                >
                    Change
                </span>
            </template>
        </small>
    </div>
</template>
