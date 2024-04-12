<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Panel from "@/Components/Surface/Panel.vue";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import Error from "@/Components/Form/Error.vue";
import ReadDocHelp from "@/Components/Util/ReadDocHelp.vue";
import TenorIcon from "@/Icons/Tenor.vue";

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

    router.put(route('mixpost.services.update', {service: 'tenor'}), props.form, {
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
                <span class="mr-xs"><TenorIcon/></span>
                Tenor
            </div>
        </template>

        <template #description>
            <p>With Tenor you can use GIF's directly in Mixpost.</p>
            <p>
                <a href="https://console.cloud.google.com/" class="link" target="_blank">Create
                    an App on Google Console</a>.
            </p>
            <ReadDocHelp :href="`${$page.props.mixpost.docs_link}/books/services-configuration-mixpost/page/tenor`" class="mt-xs"/>
        </template>

        <HorizontalGroup class="mt-lg">
            <template #title>API Key</template>
            <Input v-model="form.client_id" :error="errors.hasOwnProperty('client_id')" type="text" autocomplete="off"/>
            <template #footer>
                <Error :message="errors.client_id"/>
            </template>
        </HorizontalGroup>

        <PrimaryButton @click="save" class="mt-lg">Save</PrimaryButton>
    </Panel>
</template>
