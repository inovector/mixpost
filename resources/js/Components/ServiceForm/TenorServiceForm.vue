<script setup>
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";

const props = defineProps({
    form: {
        required: true,
        type: Object
    }
})

const {notify} = useNotifications();
const errors = ref({});

const save = () => {
    errors.value = {};

    Inertia.put(route('mixpost.services.update', {service: 'tenor'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', 'Tenor credentials have been saved');
        },
        onError: (err) => {
            errors.value = err;
        },
    });
}
</script>
<template>
    <Panel>
        <template #title>
            <div class="flex items-center">
                Tenor
            </div>
        </template>

        <template #description>
            <p>With Tenor you can use GIF's directly in Mixpost.</p>
            <p>
                <a href="https://console.cloud.google.com/" class="link" target="_blank">Create
                    an App on Google Console</a>.
            </p>
            <ReadDocHelp href="https://mixpost.app/docs/1.0.0/tenor" class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>API Key</template>
            <div class="w-full">
                <Input v-model="form.client_id" type="text" autocomplete="off"/>
                <Error :message="errors.client_id"/>
            </div>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
