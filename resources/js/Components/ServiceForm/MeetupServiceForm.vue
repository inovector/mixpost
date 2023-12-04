<script setup>
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import MeetupIcon from "@/Icons/Meetup.vue";
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

    router.put(route('mixpost.services.update', {service: 'meetup'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', 'Meetup credentials have been saved');
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
                <span class="mr-xs"><MeetupIcon class="text-meetup"/></span>
                <span>Meetup</span>
            </div>
        </template>

        <template #description>
            <p>
                <a href="https://www.meetup.com/api/oauth/list/" class="link" target="_blank">Create an oAuth client on Meetup</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/services-configuration-mixpost/page/meetup`" class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>Client ID</template>
            <Input v-model="form.client_id" :error="errors.hasOwnProperty('client_id')" type="text" class="w-full" autocomplete="off"/>

            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>Client secret</template>
            <Input v-model="form.client_secret" :error="errors.hasOwnProperty('client_secret')" type="password" class="w-full" autocomplete="new-password"/>
            <template #footer>
                <Error :message="errors.client_secret"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>

