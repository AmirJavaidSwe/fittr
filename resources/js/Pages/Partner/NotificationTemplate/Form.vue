<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Switcher from "@/Components/Switcher.vue";
import RichTextInput from "@/Components/RichTextInput.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    modal: Boolean,
});

</script>

<template>
    <div class="my-3">
        <InputLabel for="title" value="Title" />
        <TextInput
            id="title"
            v-model="form.title"
            type="text"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.title" class="mt-2" />
    </div>
    <div v-if="form.sg_template_id" class="my-3">
        <InputLabel for="sg_template_id" value="Sg Template Id" />
        <div class="mt-2">{{ form.sg_template_id ?? '&nbsp;' }}</div>
    </div>

    <div v-if="form.sg_template_name" class="my-3">
        <InputLabel for="sg_template_name" value="Sg Template Name" />
        <div class="mt-2">{{ form.sg_template_name ?? '&nbsp;' }}</div>
    </div>
    <div class="my-3">
        <InputLabel value="Placeholders used" />
        <div class="bg-gray-700 text-white p-2 rounded-lg mt-2">{{ form.placeholders.join(', ') }}</div>
    </div>
    <div class="my-3">
        <InputLabel value="Mail Driver" />
        <div class="mt-2">{{ form.mail_driver.toUpperCase() }}</div>
    </div>

    <div class="my-3">
        <InputLabel for="from_name" value="From Name" />
        <TextInput
            id="title"
            v-model="form.from_name"
            type="text"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.from_name" class="mt-2" />
    </div>

    <div class="my-3">
        <InputLabel for="from_email" value="From Email" />
        <TextInput
            id="title"
            v-model="form.from_email"
            type="text"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.from_email" class="mt-2" />
    </div>

    <div class="my-3">
        <InputLabel for="subject" value="Subject" />
        <TextInput
            id="title"
            v-model="form.subject"
            type="text"
            class="mt-1 block w-full"
        />
        <InputError :message="form.errors.subject" class="mt-2" />
    </div>

    <div class="my-3">
        <InputLabel for="content" value="Content" />
        <RichTextInput v-model="form.content" @setPlainText="data => (form.content_plain = data)" />
        <!-- <textarea v-model="form.content" class="input-field mt-1 block w-full" id="content" rows="10"></textarea> -->
        <InputError :message="form.errors.content" class="mt-2" />
    </div>

    <div class="my-3">
        <InputLabel for="content_plain" value="Content Plain" />
        <textarea v-model="form.content_plain" class="input-field mt-1 block w-full" id="content_plain" rows="10" readonly></textarea>
        <InputError :message="form.errors.content_plain" class="mt-2" />
    </div>

    <div class="my-3">
        <Switcher v-model="form.unsubscribe" title="Unsubscribe" />
        <InputError :message="form.errors.unsubscribe" class="mt-2" />
    </div>
    <div class="my-3">
        <Switcher v-model="form.bypass" title="Bypass" />
        <InputError :message="form.errors.bypass" class="mt-2" />
    </div>
    <div class="my-3">
        <Switcher v-model="form.status" title="Enabled/Disabled" />
        <InputError :message="form.errors.status" class="mt-2" />
    </div>
    <div class="my-3">
        <InputLabel for="notes" value="Notes" />
        <textarea v-model="form.notes" class="input-field mt-1 block w-full" id="notes" rows="3"></textarea>
        <InputError :message="form.errors.notes" class="mt-2" />
    </div>
</template>
