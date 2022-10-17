<script setup>
import {Head} from '@inertiajs/inertia-vue3';
import PrimaryButton from "@/Components/Button/PrimaryButton.vue";
import Panel from "@/Components/Surface/Panel.vue";
</script>
<template>
    <Head title="Dashboard"/>

    <div class="row-px row-py">
        <div class="flex justify-between items-center">
            <div class="flex flex-col items-start">
                <div class="text-4xl font-semibold text-center mb-xs">
                    <span class="daytime-emoji emoji mr-sm">☀️</span>
                    <span class="daytime-text">Good morning, Genius!</span>
                </div>
                <div class="text-base text-center mt-sm">
                    Here's what's happening with your socials today.
                </div>
            </div>
            <PrimaryButton>Create</PrimaryButton>
        </div>

        <div class="mt-lg">
            <Panel>
                Content
            </Panel>
        </div>
    </div>
</template>
