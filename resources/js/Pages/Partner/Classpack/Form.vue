<script setup>
import { reactive, ref } from "vue";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import WarningButton from "@/Components/WarningButton.vue";
import Switcher from "@/Components/Switcher.vue";
import Multiselect from "@vueform/multiselect";
import Datepicker from "@vuepic/vue-datepicker";
import { RadioGroup, RadioGroupOption } from "@headlessui/vue";
import "@vueform/multiselect/themes/tailwind.css";
import "@vuepic/vue-datepicker/dist/main.css";

const props = defineProps({
    options_types: Object,
    options_periods: Object,
    classtypes: Object,
    form: {
        type: Object,
        required: true,
    },
    submitted: {
        type: Function,
        required: true,
    },
    isNew: {
        type: Boolean,
        default: false,
    },
});

props.form.restrictions = reactive({
    offpeak: props.form.restrictions?.offpeak ?? false,
    classtypes: props.form.restrictions?.classtypes ?? [],
});

// is_renewable and is_intro can't be ON at the same time
const checkToggles = (el, v) => {
    if (el == "is_intro" && v) {
        props.form.is_renewable = false;
    }
    if (el == "is_renewable" && v) {
        props.form.is_intro = false;
    }
};
</script>

<template>
    <FormSection @submitted="submitted">
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="title" value="Title" />
                <TextInput
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.title" class="mt-2" />
            </div>
            <div class="">
                <InputLabel
                    for="sessions"
                    value="Number of sessions in one classpack"
                />
                <TextInput
                    id="sessions"
                    v-model="form.sessions"
                    type="number"
                    min="1"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.sessions" class="mt-2" />
            </div>

            <!-- is_expiring (session credits) -->
            <div class="">
                <Switcher
                    v-model="form.is_expiring"
                    title="Classes/credits expiration"
                    :description="
                        'Pack sessions ' +
                        (form.is_expiring ? 'will' : 'never') +
                        ' expire'
                    "
                />
                <InputError :message="form.errors.is_expiring" class="mt-2" />
            </div>

            <div v-if="form.is_expiring" class="">
                <InputLabel for="expiration" value="In" />
                <div class="flex mt-1 gap-2">
                    <TextInput
                        id="expiration"
                        v-model="form.expiration"
                        type="number"
                        min="1"
                        class="block"
                    />
                    <!-- <div> -->
                    <RadioGroup
                        v-model="form.expiration_period"
                        class="flex cursor-pointer rounded-lg shadow-md"
                    >
                        <RadioGroupOption
                            as="template"
                            v-for="option in options_periods"
                            :key="option.value"
                            :value="option.value"
                            v-slot="{ active, checked }"
                        >
                            <span
                                class="px-5 py-2 rounded-lg transition"
                                :class="checked ? 'bg-green-400' : ''"
                                >{{ option.label }}</span
                            >
                        </RadioGroupOption>
                    </RadioGroup>
                    <!-- </div> -->
                </div>
                <InputError :message="form.errors.expiration" class="mt-2" />
                <InputError
                    :message="form.errors.expiration_period"
                    class="mt-2"
                />
            </div>

            <!-- is_active -->
            <div class="">
                <Switcher
                    v-model="form.is_active"
                    title="Status"
                    :description="
                        'Pack is ' + (form.is_active ? 'active' : 'inactive')
                    "
                />
                <InputError :message="form.errors.is_active" class="mt-2" />
            </div>

            <!-- Price -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="price" value="Price of Class pack" />
                <TextInput
                    id="price"
                    v-model="form.price"
                    type="number"
                    min="0"
                    step=".01"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.price" class="mt-2" />
            </div>

            <!-- Type -->
            <div class="">
                <InputLabel for="type" value="Type" />
                <Multiselect
                    v-model="form.type"
                    :options="options_types"
                    :searchable="true"
                    :close-on-select="true"
                    :show-labels="true"
                    placeholder="Select pack type"
                >
                </Multiselect>
                <InputError :message="form.errors.type" class="mt-2" />
            </div>

            <!-- is_renewable -->
            <div class="">
                <Switcher
                    v-model="form.is_renewable"
                    @update:modelValue="
                        checkToggles('is_renewable', form.is_renewable)
                    "
                    title="Renewable"
                    :description="
                        'Pack is ' +
                        (form.is_renewable ? '' : 'not ') +
                        'renewable'
                    "
                />
                <InputError :message="form.errors.is_renewable" class="mt-2" />
            </div>

            <!-- is_intro -->
            <div class="">
                <Switcher
                    v-model="form.is_intro"
                    @update:modelValue="checkToggles('is_intro', form.is_intro)"
                    title="Intro pack"
                    :description="
                        'Pack available to ' +
                        (form.is_intro ? 'new members only' : 'anyone')
                    "
                />
                <InputError :message="form.errors.is_intro" class="mt-2" />
            </div>

            <!-- is_private -->
            <div class="">
                <Switcher
                    v-model="form.is_private"
                    title="Private pack"
                    :description="
                        'Pack visible ' +
                        (form.is_private ? 'via private link' : 'to anyone')
                    "
                />
                <InputError :message="form.errors.is_private" class="mt-2" />
            </div>

            <!-- private_url -->
            <div v-if="form.is_private" class="col-span-6 sm:col-span-4">
                <InputLabel for="private_url" value="Private url" />
                <TextInput
                    id="private_url"
                    v-model="form.private_url"
                    type="text"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.private_url" class="mt-2" />
                <p>
                    Private link will be:
                    <b
                        >https://{{ $page.props.app_domain }}.{{
                            $page.props.business_seetings.subdomain
                        }}/some-route/{{ form.private_url }}</b
                    >
                </p>
            </div>

            <!-- active_from -->
            <div>
                <InputLabel for="active_from" value="Active from" />
                <div>
                    When date is set, pack will not be available for sale
                    <b>before</b> this date.
                </div>
                <Datepicker
                    class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                    v-model="form.active_from"
                    :enable-time-picker="false"
                    :format="
                        $page.props.business_seetings.date_format.format_js
                    "
                    placeholder="Leave blank to ignore"
                    text-input
                    auto-apply
                />
                <InputError :message="form.errors.active_from" class="mt-2" />
            </div>

            <!-- active_to -->
            <div>
                <InputLabel for="active_to" value="Active to" />
                <div>
                    When date is set, pack will not be available for sale
                    <b>after</b> this date.
                </div>
                <Datepicker
                    class="mt-1 block w-full bg-gray-100 border-transparent rounded-md shadow-sm focus:border-gray-300 focus:bg-white focus:ring-0"
                    v-model="form.active_to"
                    :enable-time-picker="false"
                    :format="
                        $page.props.business_seetings.date_format.format_js
                    "
                    placeholder="Leave blank to ignore"
                    text-input
                    auto-apply
                />
                <InputError :message="form.errors.active_to" class="mt-2" />
            </div>

            <!-- is_restricted -->
            <div class="">
                <Switcher
                    v-model="form.is_restricted"
                    title="Restrictions"
                    :description="
                        'Pack has ' +
                        (form.is_restricted ? '' : 'no ') +
                        'restrictions'
                    "
                />
                <InputError :message="form.errors.is_restricted" class="mt-2" />
            </div>
            <div v-if="form.is_restricted" class="space-y-4">
                <!-- restrictions.offpeak -->
                <Switcher
                    v-model="props.form.restrictions.offpeak"
                    title="Off Peak"
                    description="Pack sessions can be used for off peak classes"
                />
                <div>
                    <!-- restrictions.classtypes -->
                    <InputLabel
                        for="classtypes"
                        value="Class type restrictions"
                    />
                    <Multiselect
                        v-model="props.form.restrictions.classtypes"
                        mode="multiple"
                        id="classtypes"
                        :options="classtypes"
                        :searchable="true"
                        :close-on-select="false"
                        :hide-selected="false"
                        placeholder="Select types"
                    >
                    </Multiselect>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <WarningButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                <span v-if="isNew">Create</span>
                <span v-else>Save changes</span>
            </WarningButton>
        </template>
    </FormSection>
</template>
