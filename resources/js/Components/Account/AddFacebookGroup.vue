<script setup>
// @deprecated
// We will remove this feature soon
import { Link, usePage } from '@inertiajs/vue3'
import FacebookIcon from "@/Icons/Facebook.vue";
import Badge from "../DataDisplay/Badge.vue";

const metaAppVersion = usePage().props.additionally.meta_app_version;
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {provider: 'facebook_group'})" method="post" as="button" type="button" class="w-full flex items-center px-lg py-4 hover:bg-facebook/50 ease-in-out duration-200">
        <span class="flex mr-4">
            <FacebookIcon class="text-facebook"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-medium">Facebook Group <template v-if="metaAppVersion === 'v19.0'"><Badge variant="error">Deprecated in v19</Badge></template></span>
            <span>Connect a new Facebook group</span>

            <template v-if="metaAppVersion === 'v19.0'">
                <span class="text-xs text-red-500 text-left">The Facebook Groups API is deprecated in v19.</span>
            </template>
        </span>
    </Link>
</template>
