<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import UnsplashIcon from "@/Icons/Unsplash.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";
import Flex from "../Layout/Flex.vue";
import Checkbox from "../Form/Checkbox.vue";
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

    router.put(route('mixpost.services.update', {service: 'unsplash'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', 'Unsplash service have been saved');
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
                <span class="mr-xs"><UnsplashIcon class="text-black"/></span>
                <span>Unsplash</span>
            </div>
        </template>

        <template #description>
            <p>With Unsplash you can use external stock photos directly in Mixpost.</p>
            <p>
                <a href="https://unsplash.com/oauth/applications" class="link" target="_blank">
                    Create an App on Unsplash</a>.
            </p>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>
                <label for="client_id">API Key <LabelSuffix danger>*</LabelSuffix></label>
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
