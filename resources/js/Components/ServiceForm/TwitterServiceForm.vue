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
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/services-configuration-mixpost/page/twitter`" class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>API Key</template>
            <Input v-model="form.client_id" :error="errors.hasOwnProperty('client_id')" type="text" autocomplete="off"/>
            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>API Secret</template>
            <Input v-model="form.client_secret" :error="errors.hasOwnProperty('client_secret')" type="password" autocomplete="new-password"/>
            <template #footer>
                <Error :message="errors.client_secret"/>
            </template>
        </HorizontalGroup>

        <HorizontalGroup class="mt-lg">
            <template #title>Tier</template>
            <Select v-model="form.tier" :error="errors.hasOwnProperty('tier')" class="w-full">
                <option value="legacy">Legacy</option>
                <option value="free">Free</option>
                <option value="basic">Basic</option>
            </Select>
            <template #footer>
                <Error :message="errors.environment"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
