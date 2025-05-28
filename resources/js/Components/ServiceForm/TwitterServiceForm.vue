<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import TwitterIcon from "@/Icons/Twitter.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";
import Select from "../Form/Select.vue";
import Flex from "../Layout/Flex.vue";
import Checkbox from "../Form/Checkbox.vue";
import Label from "../Form/Label.vue";
import InputHidden from "../Form/InputHidden.vue";
import LabelSuffix from "../Form/LabelSuffix.vue";

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

    router.put(route('mixpost.services.update', {service: 'twitter'}), props.form, {
        preserveScroll: true,
        onSuccess() {
            notify('success', 'Twitter service have been saved');
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
                <span>X</span>
            </div>
        </template>

        <template #description>
            <a href="https://developer.twitter.com/en/portal/projects-and-apps" class="link" target="_blank">
                Create an App on Twitter</a>.
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/services/social/x`"
                         class="mt-xs"/>
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
                <label for="client_secret">API Secret <LabelSuffix danger>*</LabelSuffix></label>
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
                <label for="tier">Tier</label>
            </template>

            <Select v-model="form.configuration.tier"
                    :error="errors['configuration.tier'] !== undefined"
                    id="tier">
                <option value="legacy">Legacy</option>
                <option value="free">Free</option>
                <option value="basic">Basic</option>
            </Select>

            <template #footer>
                <Error :message="errors['configuration.tier']"/>
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
