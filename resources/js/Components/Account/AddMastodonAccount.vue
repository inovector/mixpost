<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
import useNotifications from "@/Composables/useNotifications";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import MastodonIcon from "@/Icons/Mastodon.vue";
import ArrowRightIcon from "@/Icons/ArrowRight.vue";

const {notify} = useNotifications();

const isLoading = ref(false);
const server = ref('');

const open = ref(false);

const createApp = () => {
    return new Promise((resolve, reject) => {
        axios.post(route('mixpost.services.createMastodonApp'), {server: server.value})
            .then(() => {
                resolve();
            }).catch(function (error) {
            reject(error);
        });
    });
}

const oAuthRedirect = () => {
    isLoading.value = true;

    router.post(route('mixpost.accounts.add', {provider: 'mastodon'}), {server: server.value}, {
        onSuccess() {
            isLoading.value = false;
        }
    });
}
const connect = async () => {
    isLoading.value = true;

    await createApp().then(() => {
        oAuthRedirect();
    }).catch((error) => {
        if (error.response.status !== 422) {
            notify('error', error.response.data.message);
            return;
        }

        notify('error', error.response.data.errors);
    }).finally(() => {
        isLoading.value = false;
    })
}
</script>
<template>
    <div :class="{'bg-mastodon/20': open}">
        <div role="button" @click="open = !open"
             type="button"
             class="w-full flex items-center px-lg py-md hover:bg-mastodon/20 ease-in-out duration-200">
            <span class="flex mr-md">
                <MastodonIcon class="text-mastodon"/>
            </span>

            <span class="flex flex-col items-start">
                <span class="font-medium">Mastodon</span>
                <span>Connect a new Mastodon profile</span>
            </span>
        </div>

        <div v-if="open" class="px-lg py-md">
            <HorizontalGroup>
                <template #title>Enter your Mastodon server</template>
                <Input type="text" v-model="server" placeholder="example.server"/>
            </HorizontalGroup>

            <PrimaryButton @click="connect" :disabled="!server || isLoading" :isLoading="isLoading"
                           class="mt-xs md:mt-0">
                <span class="mr-xs">Next</span>
                <span><ArrowRightIcon class="w-5! h-5!"/></span>
            </PrimaryButton>
        </div>
    </div>
</template>
