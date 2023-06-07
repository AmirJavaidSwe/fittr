<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:modelValue"]);
const props = defineProps(["modelValue", "value"]);

const proxyChecked = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit("update:modelValue", val);
    },
});
</script>

<template>
    <input
        v-model="proxyChecked"
        type="radio"
        :value="value"
        class="input-radio"
    />
</template>
