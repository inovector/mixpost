<script setup>
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import FacebookIcon from "@/Icons/Facebook.vue";
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

    router.put(route('mixpost.services.update', {service: 'facebook'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', 'Facebook credentials have been saved');
        },
        onError: (err) => {
            errors.value = err;
        },
    });
}
</script>
<template>
    <Panel class="mt-lg">
        <template #title>
            <div class="flex items-center">
                <span class="mr-xs"><FacebookIcon class="text-facebook"/></span>
                <span>Facebook</span>
            </div>
        </template>

        <template #description>
            <p>
                <a href="https://developers.facebook.com/apps" class="link" target="_blank">Create an App on
                    Facebook</a>. Select "Business" for the type of application.
            </p>
            <ReadDocHelp href="https://mixpost.app/docs/1.0.0/facebook" class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>App ID</template>
            <Input v-model="form.client_id" :error="errors.hasOwnProperty('client_id')" type="text" class="w-full" autocomplete="off"/>

            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>App secret</template>
            <Input v-model="form.client_secret" :error="errors.hasOwnProperty('client_secret')" type="password" class="w-full" autocomplete="new-password"/>
            <template #footer>
                <Error :message="errors.client_secret"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
