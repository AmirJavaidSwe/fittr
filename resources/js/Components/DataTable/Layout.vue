<script setup>
import { ref } from 'vue';
import { Link } from "@inertiajs/vue3";
import ButtonLink from "@/Components/ButtonLink.vue";
import { faPlus, faEdit, faTrash } from "@fortawesome/free-solid-svg-icons";
import { useSlots } from "vue";

const emit = defineEmits(['bulkEdit', 'bulkDelete'])
const props = defineProps({
    disableButton: {
        type: Boolean,
        default: false,
    },
    disableSearch: {
        type: Boolean,
        default: false,
    },
    buttonTitle: {
        default: "Add data",
        type: String,
        required: false,
    },
    buttonLink: {
        default: "#",
        type: String,
        required: false,
    },
    extraActions: {
        default: false,
        type: [Boolean, Number],
        required: false,
    },
});

const mousedown = ref(false);
const initialTableLeft = ref(0);
const initialGrabPosition = ref(0);
const wrap = ref(null); //dom
const grabTable = (e) => {
    mousedown.value = true;
    initialGrabPosition.value = e.pageX;
    wrap.value.style.cursor = 'grab';
    initialTableLeft.value = wrap.value.scrollLeft;
};
const releaseTable = () => {
    mousedown.value = false;
    wrap.value.style.cursor = 'initial';
    wrap.value.style.userSelect = '';
};
const moveTable = (e) => {
    if(mousedown.value == false) return;
    wrap.value.style.cursor = 'grabbing';
    wrap.value.style.userSelect = 'none';
    wrap.value.scrollLeft = initialTableLeft.value + (initialGrabPosition.value - e.pageX);
};
</script>

<template>
    <div v-if="$slots.search || $slots.button || $slots.extraActions" class="flex flex-col lg:flex-row items-end lg:items-center">
        <slot name="search"></slot>
        <div class="sm:flex sm:items-center">
            <div
                class="fixed bg-white border-t border-t-grey bottom-0 flex flex-wrap gap-2 justify-center left-0 lg:bg-transparent lg:border-none lg:ml-2 lg:py-0 lg:static mt-4 py-5 sm:mt-0 w-full z-10"
            >
                <slot name="button"></slot>
                <ButtonLink
                    styling="secondary"
                    size="default"
                    v-if="!disableButton"
                    :href="buttonLink"
                >
                    {{ buttonTitle }}
                    <font-awesome-icon
                        v-if="buttonTitle.includes('new')"
                        class="ml-2"
                        :icon="faPlus"
                    />
                </ButtonLink>
            </div>
        </div>

        <div class="sm:flex sm:items-center" v-if="extraActions">
            <div
                class="mt-0 absolute top-0 left-0 w-full bg-white py-6 z-10 flex flex-wrap justify-center gap-2"
            >
                <slot name="extraActions"></slot>
                <p class="mt-2">{{ extraActions }} Rows Selected</p>
                <ButtonLink
                    styling="secondary"
                    size="small"
                    @click="$emit('bulkEdit')"
                >
                    <font-awesome-icon class="ml-2" :icon="faEdit" /> &nbsp;
                    Edit
                </ButtonLink>
                <ButtonLink
                    styling="danger"
                    size="small"
                    @click="$emit('bulkDelete')"
                >
                    <font-awesome-icon class="ml-2" :icon="faTrash" /> &nbsp;
                    Delete
                </ButtonLink>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto" :class="$slots.search || $slots.button || $slots.extraActions ? 'mt-4' : 'mt-0'" ref="wrap">
        <table class="w-full h-full border-spacing-x-px border-spacing-y-0 border-separate ">
            <thead>
                <tr>
                    <slot name="tableHead"></slot>
                </tr>
            </thead>
            <tbody class="data-table-layout" @mousedown="grabTable" @mouseup="releaseTable" @mousemove="moveTable">
                <!-- <tr> -->
                <slot name="tableData"></slot>
                <!-- </tr> -->
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <slot name="pagination" />
    </div>
</template>
