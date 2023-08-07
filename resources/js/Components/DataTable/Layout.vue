<template>
    <div v-if="$slots.search || $slots.button || $slots.extraActions" class="flex flex-col lg:flex-row items-end lg:items-center">
        <slot name="search"></slot>
        <div class="sm:flex sm:items-center">
            <div
                class="mt-4 sm:mt-0 lg:ml-2 fixed bottom-0 left-0 w-full bg-white py-5 border-t border-t-grey z-10 lg:static lg:bg-transparent lg:border-none lg:py-0 flex flex-wrap justify-center gap-2"
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
                class="mt-0 fixed top-0 left-0 w-full bg-white py-5 z-10 flex flex-wrap justify-center gap-2"
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

    <div class="mt-0 flex flex-col" :class="$slots.search || $slots.button || $slots.extraActions ? 'lg-mt-8' : ''">
        <div class="overflow-x-auto overflow-y-hidden">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="md:rounded-lg overflow-hidden">
                    <table class="min-w-full border-spacing-0 border-separate">
                        <thead>
                            <tr>
                                <slot name="tableHead"></slot>
                            </tr>
                        </thead>
                        <tbody class="data-table-layout">
                            <!-- <tr> -->
                            <slot name="tableData"></slot>
                            <!-- </tr> -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <slot name="pagination" />
        </div>
    </div>
</template>

<script setup>
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
</script>
