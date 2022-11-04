<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import TwitterIcon from "@/Icons/Twitter.vue";
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {provider: 'twitter'})" method="post" as="button" type="button" class="w-full flex items-center px-lg py-4 hover:bg-twitter hover:bg-opacity-20 ease-in-out duration-200">
        <span class="flex mr-4">
            <TwitterIcon class="text-twitter"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-semibold">Twitter</span>
            <span>Connect a new Twitter profile</span>
        </span>
    </Link>
</template>
