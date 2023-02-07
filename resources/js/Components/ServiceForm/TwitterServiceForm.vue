<script setup>
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import TwitterIcon from "@/Icons/Twitter.vue";
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

    Inertia.put(route('mixpost.services.update', {service: 'twitter'}), props.form, {
        onSuccess() {
            notify('success', 'Twitter credentials have been saved');
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
                <span class="mr-xs"><TwitterIcon class="text-twitter"/></span>
                <span>Twitter</span>
            </div>
        </template>

        <template #description>
            <a href="https://developer.twitter.com/en/portal/projects-and-apps" class="link" target="_blank">Create
                an App on Twitter</a>. You will need to edit the App Permissions and allow "Read and Write".
            <ReadDocHelp class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>API Key</template>
            <div class="w-full">
                <Input v-model="form.client_id" type="text" autocomplete="off"/>
                <Error :message="errors.client_id"/>
            </div>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>API Secret</template>
            <div class="w-full">
                <Input v-model="form.client_secret" type="password" autocomplete="new-password"/>
                <Error :message="errors.client_secret"/>
            </div>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
