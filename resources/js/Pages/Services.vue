<script setup>
import {ref, defineAsyncComponent} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import TwitterIcon from "@/Icons/Twitter.vue";
import FacebookIcon from "@/Icons/Facebook.vue";
import UnsplashIcon from "@/Icons/Unsplash.vue";

const TwitterServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/TwitterServiceForm.vue"));
const FacebookServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/FacebookServiceForm.vue"));
const UnsplashServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/UnsplashServiceForm.vue"));
const TenorServiceForm = defineAsyncComponent(() => import("@/Components/ServiceForm/TenorServiceForm.vue"));

const pageTitle = 'Third Party Services';

const props = defineProps(['services'])

const form = useForm(props.services);

const {notify} = useNotifications();

const tab = ref('twitter');
</script>
<template>
    <Head :title="pageTitle"/>

    <div class="row-py mb-2xl w-full max-w-3xl mx-auto">
        <PageHeader :title="pageTitle">
            <template #description>
                This page is for storing the credentials for third party services.
            </template>
        </PageHeader>

        <div class="w-full row-px mb-lg">
            <Tabs>
                <Tab @click="tab = 'twitter'" :active="tab === 'twitter'">
                    <span class="mr-xs"><TwitterIcon class="text-twitter !h-5 !w-5"/></span>
                    <span>Twitter</span>
                </Tab>

                <Tab @click="tab = 'facebook'" :active="tab === 'facebook'">
                    <span class="mr-xs"><FacebookIcon class="text-facebook !h-5 !w-5"/></span>
                    <span>Facebook</span>
                </Tab>

                <Tab @click="tab = 'unsplash'" :active="tab === 'unsplash'">
                    <span class="mr-xs"><UnsplashIcon class="text-black !h-5 !w-5"/></span>
                    <span>Unsplash</span>
                </Tab>

                <Tab @click="tab = 'tenor'" :active="tab === 'tenor'">
                    <span>Tenor</span>
                </Tab>
            </Tabs>
        </div>

        <div class="row-px">
            <template v-if="tab === 'twitter'">
                <TwitterServiceForm :form="form.twitter"/>
            </template>

            <template v-if="tab === 'facebook'">
                <FacebookServiceForm :form="form.facebook"/>
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
