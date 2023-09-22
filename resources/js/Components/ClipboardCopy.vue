<script setup>
import { computed, ref } from "vue";
import { faClipboard, faClipboardCheck } from '@fortawesome/free-solid-svg-icons';
import ButtonLink from '@/Components/ButtonLink.vue';

const props = defineProps({
    text: {
        type: String,
        default: null,
        required: false
    }
});
const copyInput = ref(null);
const copied = ref(false);
const shown = ref(false);
const copy = () => {
    copied.value = true;
    shown.value = false;
    copyInput.value.select();
    copied.value = document.execCommand('copy');
    setTimeout(() => {
        copied.value = false;
    }, 2000);
}
const styling = computed(() => {
    return copied.value ? 'primary' : 'default';
});
</script>

<template>
    <ButtonLink type="button" size="small" class="text-xl" :styling="styling" @click="copy" v-tooltip="{content:'Copy to Clipboard', triggers: [], shown: shown}">
            <font-awesome-icon :icon="copied ? faClipboardCheck : faClipboard" @mouseenter.stop="shown = true" @mouseleave="shown = false" />
    </ButtonLink>
    <input ref="copyInput" type="text" class="fixed" readonly :value="props.text">
</template>
