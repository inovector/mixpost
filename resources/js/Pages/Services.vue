<script setup>
import {ref, defineAsyncComponent} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import UnsplashIcon from "@/Icons/Unsplash.vue";
import TenorIcon from "@/Icons/Tenor.vue";

const TwitterServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/TwitterServiceForm.vue"));
const FacebookServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/FacebookServiceForm.vue"));
const UnsplashServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/UnsplashServiceForm.vue"));
const TenorServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/TenorServiceForm.vue"));

const pageTitle = 'Third Party Services';

const props = defineProps(['services'])

const form = useForm(props.services);

const tab = ref('facebook');
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="row-py w-full mx-auto">
        <PageHeader :title="pageTitle">
            <template #description>
                This page is for storing the credentials for third party services.
            </template>
        </PageHeader>

        <div class="w-full row-px mb-lg">
            <Tabs class="overflow-x-auto flex-nowrap! 2xl:flex-wrap! 2xl:gap-sm max-w-full w-full">
                <Tab @click="tab = 'facebook'" :active="tab === 'facebook'">
                    <template #icon>
                        <ProviderIcon provider="facebook"/>
                    </template>

                    Facebook
                </Tab>

                <Tab @click="tab = 'twitter'" :active="tab === 'twitter'">
                    <template #icon>
                        <ProviderIcon provider="twitter"/>
                    </template>
                    X
                </Tab>

                <Tab @click="tab = 'unsplash'" :active="tab === 'unsplash'">
                    <template #icon>
                        <UnsplashIcon class="text-black"/>
                    </template>
                    Unsplash
                </Tab>

                <Tab @click="tab = 'tenor'" :active="tab === 'tenor'">
                    <template #icon>
                        <TenorIcon/>
                    </template>
                    Tenor
                </Tab>
            </Tabs>
        </div>

        <div class="row-px">
            <template v-if="tab === 'facebook'">
                <FacebookServiceForm :form="form.facebook"/>
            </template>

            <template v-if="tab === 'twitter'">
                <TwitterServiceForm :form="form.twitter"/>
            </template>

            <template v-if="tab === 'unsplash'">
                <UnsplashServiceForm :form="form.unsplash"/>
            </template>

            <template v-if="tab === 'tenor'">
                <TenorServiceForm :form="form.tenor"/>
            </template>
        </div>
    </div>
</template>
