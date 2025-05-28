<script setup>
import PrimaryButton from "../Button/PrimaryButton.vue";
import LockOpen from "../../Icons/LockOpen.vue";
</script>
<template>
    <a href="https://mixpost.app/pricing?utm_medium=software&utm_source=mixpost&utm_campaign=ad_upgrade"
       target="_blank" class="block w-full mt-md">
        <PrimaryButton
            class="w-full text-center! justify-center bg-linear-to-r from-purple-500 to-indigo-500 hover:drop-shadow-xl transition-all ease-in-out duration-200">
            <template #icon>
                <LockOpen/>
            </template>
            Unlock with Mixpost Pro
        </PrimaryButton>
    </a>
</template>
