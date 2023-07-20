import Swal from "sweetalert2";
import { computed, ref, watch } from "vue";

export function useSwal({ flash, errors: propsErrors, options }) {

    if(!flash && !propsErrors) return { toast, messageBox };

    const toast = (params) => {
        Swal.fire({
            showConfirmButton: false,
            position: 'top',
            timer: 5000,
            ...options,
            ...params,
            toast: true, // toast always true
        });
    };

    const messageBox = (params) => {
        Swal.fire({
            timer: 5000,
            ...options,
            ...params,
            toast: false // toast always false
        });
    };

    const has_msg = computed(() => flash.value.timestamp);
    const msg_type = computed(() => flash.value.type);
    const errors = computed(() => propsErrors.value);
    const show = ref(false);
    const shown = ref([]);
    const show_errors = ref(false);
    const title = ref(null);
    const timer = ref(null);
    const classes = ref("");

    watch(has_msg, () => {
        if (!has_msg.value) return;
        if (shown.value.includes(has_msg.value)) return;
        shown.value.push(has_msg.value);

        switch (msg_type.value) {
            case "success":
                title.value = "Success";
                break;
            case "warning":
                title.value = "Warning";
                break;
            case "error":
                title.value = "Error";
                break;

            default:
                title.value = "";
        }


        toast({
            title: title.value,
            icon: msg_type.value,
            text: flash.value.message,
        });
    });

    watch(errors, (newValue, oldValue) => {
        if (Object.keys(errors.value).length == 0) return;

        let errorText = "<ul class='text-sm text-red-500'>";
        for(let [key, error] of Object.entries(errors.value)) {
            errorText += `<li>${error}</li>`;
        }
        errorText += "</ul>";

        toast({
            title: "Error",
            icon: "error",
            html: errorText,
        });
    });

    return { toast, messageBox };
}