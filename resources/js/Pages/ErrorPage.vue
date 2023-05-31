<script setup>
import {Head, Link} from '@inertiajs/vue3';
import MinimalLayout from "@/Layouts/Minimal.vue";
import Panel from "@/Components/Surface/Panel.vue";
import SecondaryButton from "../Components/Button/SecondaryButton.vue";

defineOptions({layout: MinimalLayout});
</script>
<template>
    <Head :title="$page.props.title"/>

    <Panel>
        <template #title>{{ $page.props.title }}</template>

        {{ $page.props.text }}

        <div class="mt-lg">
            <Link :href="route('mixpost.dashboard')">
                <SecondaryButton>Go home</SecondaryButton>
            </Link>
        </div>
    </Panel>
</template>
