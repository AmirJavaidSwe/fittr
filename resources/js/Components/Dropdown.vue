<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

const props = defineProps({
    align: {
        type: String,
        default: "right",
    },
    width: {
        type: String,
        default: "48",
    },
    contentClasses: {
        type: Array,
        default: () => ["py-1", "bg-white"],
    },
    top: Boolean,
});

let open = ref(false);

const emit = defineEmits(["toggled"]);
watch(open, (new_val) => {
    emit("toggled", new_val);

    setRowHeight(new_val);
});

const setRowHeight = (new_val) => {
    setTimeout(() => {
        // We can use the native DOM API and querySelectorAll.
        const dataTableLayout = document.querySelectorAll(".data-table-layout");

        // elementsWithClass is already a NodeList, so there is no need to check for .length.
        if (dataTableLayout.length) {
            // we can directly access the elements from the NodeList.
            const dataTableLayoutTr = dataTableLayout[0].querySelectorAll("tr");

            if (dataTableLayoutTr.length <= 2) {
                // we can use the same elementsWithClass1 variable.
                const openedDropdownInnerLinks =
                    dataTableLayoutTr[0].querySelectorAll(
                        ".main-dropdown-opened .dropdown-inner-link"
                    );

                // Use parseInt with a radix of 10 to avoid unexpected behavior with leading zeros.

                if (openedDropdownInnerLinks.length > 0) {
                    const height =
                        parseInt(66) *
                            parseInt(openedDropdownInnerLinks.length) +
                        "px";
                    // Remove !important from the style object, as it's not necessary.
                    dataTableLayoutTr.forEach((element) => {
                        element.style.height = height;
                    });
                } else {
                    dataTableLayoutTr.forEach((element) => {
                        element.removeAttribute("style");
                    });
                }
            }
        }
    }, 100);
};

const closeOnEscape = (e) => {
    if (open.value && e.key === "Escape") {
        open.value = false;
    }
};

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));

const widthClass = computed(() => {
    return {
        48: "w-48",
        56: "w-56",
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === "left") {
        return "origin-top-left left-0";
    }

    if (props.align === "right") {
        return "origin-top-right right-0";
    }

    return "origin-top";
});

const alignmentVerticalClasses = computed(() => {
    if (props.top) {
        return "bottom-full";
    }

    return "origin-top";
});
</script>

<template>
    <div class="relative">
        <div @click="open = !open" class="cursor-pointer">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div
            v-show="open"
            class="bg-gray-500 fixed inset-0 opacity-25 transition z-40"
            @click="open = false"
        />

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 rounded-md shadow-lg overflow-hidden"
                :class="[
                    widthClass,
                    alignmentVerticalClasses,
                    alignmentClasses,
                    open ? 'main-dropdown-opened' : '',
                ]"
                style="display: none"
                @click="open = false"
            >
                <div
                    class="rounded-md ring-1 ring-black ring-opacity-5"
                    :class="contentClasses"
                >
                    <slot name="content" />
                </div>
            </div>
        </transition>
    </div>
</template>
