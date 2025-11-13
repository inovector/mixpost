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
import Checkbox from "../Form/Checkbox.vue";
import Flex from "../Layout/Flex.vue";
import InputHidden from "../Form/InputHidden.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";
import Label from "../Form/Label.vue";

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
            notify('success', 'Facebook service has been saved');
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
                <a href="https://developers.facebook.com/apps" class="link" target="_blank">
                    Create an App on Facebook</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/services/social/facebook/`"
                         class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="client_id">App ID
                    <LabelSuffix danger>*</LabelSuffix>
                </label>
            </template>

            <Input v-model="form.configuration.client_id"
                   :error="errors['configuration.client_id'] !== undefined"
                   type="text"
                   id="client_id"
                   autocomplete="off"/>

            <template #footer>
                <Error :message="errors['configuration.client_id']"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="client_secret">App secret
                    <LabelSuffix danger>*</LabelSuffix>
                </label>
            </template>

            <InputHidden v-model="form.configuration.client_secret"
                         :error="errors['configuration.client_secret'] !== undefined"
                         id="client_secret"
                         autocomplete="new-password"/>

            <template #footer>
                <Error :message="errors['configuration.client_secret']"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="version">API Version</label>
            </template>

            <Select v-model="form.configuration.api_version"
                    :error="errors['configuration.api_version'] !== undefined"
                    id="version">
                <option value="v24.0">v24.0</option>
                <option value="v23.0">v23.0</option>
                <option value="v22.0">v22.0</option>
                <option value="v21.0">v21.0</option>
                <option value="v20.0">v20.0</option>
                <option value="v19.0">v19.0</option>
                <option value="v18.0">v18.0</option>
                <option value="v17.0">v17.0</option>
                <option value="v16.0">v16.0</option>
            </Select>

            <template #footer>
                <Error :message="errors['configuration.api_version']"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>
                Status
            </template>

            <Flex :responsive="false" class="items-center">
                <Checkbox v-model:checked="form.active" id="active"/>
                <Label for="active" class="mb-0!">Active</Label>
            </Flex>

            <template #footer>
                <Error :message="errors.active"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
