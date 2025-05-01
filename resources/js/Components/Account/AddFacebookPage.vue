<script setup>
import { Link } from '@inertiajs/vue3'
import FacebookIcon from "@/Icons/Facebook.vue";
</script>
<template>
    <Link :href="route('mixpost.accounts.add', {provider: 'facebook_page'})" method="post" as="button" type="button" class="w-full flex items-center px-lg py-4 hover:bg-facebook/20 ease-in-out duration-200">
        <span class="flex mr-4">
            <FacebookIcon class="text-facebook"/>
        </span>
        <span class="flex flex-col items-start">
            <span class="font-medium">Facebook Page</span>
            <span>Connect a new Facebook page</span>
        </span>
    </Link>
</template>
