<script setup>
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import Select from "@/Components/Form/Select.vue";
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
                    Facebook</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/services/social/facebook/`" class="mt-xs"/>
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

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="version">API Version</label>
            </template>

            <Select v-model="form.api_version"
                    :error="errors.hasOwnProperty('api_version')"
                    id="version">
                <option value="v20.0">v20.0</option>
                <option value="v19.0">v19.0</option>
                <option value="v18.0">v18.0</option>
                <option value="v17.0">v17.0</option>
                <option value="v16.0">v16.0</option>
            </Select>

            <template #footer>
                <Error :message="errors.api_version"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
