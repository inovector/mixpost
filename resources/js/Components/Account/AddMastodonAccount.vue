<script setup>
import {ref} from "vue";
import {Inertia} from "@inertiajs/inertia";
import Input from "@/Components/Form/Input.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import HorizontalGroup from "@/Components/Layout/HorizontalGroup.vue";
import MastodonIcon from "@/Icons/Mastodon.vue";
import ArrowRightIcon from "@/Icons/ArrowRight.vue";

const server = ref('');

const open = ref(false);

const connect = () => {
    Inertia.post(route('mixpost.accounts.add', {provider: 'mastodon'}), {server: server.value});
}
</script>
<template>
    <div :class="{'bg-mastodon bg-opacity-20': open}">
        <div role="button" @click="open = !open"
             type="button"
             class="w-full flex items-center px-lg py-md hover:bg-mastodon hover:bg-opacity-20 ease-in-out duration-200">
            <span class="flex mr-md">
                <MastodonIcon class="text-mastodon"/>
            </span>

            <span class="flex flex-col items-start">
                <span class="font-semibold">Mastodon</span>
                <span>Connect a new Mastodon profile</span>
            </span>
        </div>

        <div v-if="open" class="px-lg py-md">
            <HorizontalGroup>
                <template #title>Choose your Mastodon server</template>
                <Input type="text" v-model="server" placeholder="example.server"/>
            </HorizontalGroup>

            <PrimaryButton :disabled="!server" @click="connect" class="mt-xs md:mt-0">
                <span class="mr-xs">Next</span>
                <span><ArrowRightIcon class="!w-5 !h-5"/></span>
            </PrimaryButton>
        </div>
    </div>
</template>
