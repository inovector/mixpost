<script setup>
import {Head} from '@inertiajs/vue3';
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import ActionSection from "../Components/Surface/ActionSection.vue";
import UserAccount from "../Components/Profile/UserAccount.vue";
import SectionBorder from "../Components/Surface/SectionBorder.vue";
import Password from "../Components/Profile/Password.vue";
</script>
<template>
    <Head title="Edit Profile"/>

    <div class="row-py mb-2xl w-full max-w-(--container-5xl) mx-auto">
        <PageHeader title="Edit Profile"/>

        <div class="row-px">
            <div class="mt-2xl sm:mt-0">
                <ActionSection>
                    <template #title>Profile Information</template>
                    <template #description>Update your account's profile information and email address.</template>

                    <UserAccount/>
                </ActionSection>
            </div>

            <SectionBorder/>

            <div class="mt-2xl sm:mt-0">
                <ActionSection>
                    <template #title>Update Password</template>
                    <template #description>Ensure your account is using a long, random password to stay secure.</template>

                    <Password/>
                </ActionSection>
            </div>
        </div>
    </div>
</template>
