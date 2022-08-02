<script setup>
import {ref} from "vue";
import {Head} from '@inertiajs/inertia-vue3';
import MixpostPageHeader from "@/Components/PageHeader.vue";
import MixpostPanel from "@/Components/Panel.vue";
import MixpostModal from "@/Components/Modal.vue"
import MixpostAccount from "@/Components/Account.vue"
import MixpostAddTwitterAccount from "@/Components/AddTwitterAccount.vue"
import MixpostAddFacebookAccount from "@/Components/AddFacebookAccount.vue"
import PlusIcon from "@/Icons/Plus.vue";
import DotsVerticalIcon from "@/Icons/DotsVertical.vue";

const title = 'Social Accounts';

const addSocial = ref(false);
</script>
<template>
    <Head :title="title"/>

    <div class="max-w-5xl mx-auto default-y-padding">
        <MixpostPageHeader :title="title">
            <template #description>
                Connect a social account you'd like to manage.
            </template>
        </MixpostPageHeader>

        <div class="mt-6 default-x-padding">
            <div class="grid grid-cols-4 gap-6">
                <template v-for="account in $page.props.accounts" :key="account.id">
                    <MixpostPanel class="relative">
                        <button class="absolute top-0 right-0 mt-3 mr-3">
                            <DotsVerticalIcon/>
                        </button>

                        <div class="flex flex-col justify-center">
                            <MixpostAccount
                                size="lg"
                                :imgUrl="account.image"
                                :provider="account.provider"
                                :active="true"
                            />

                            <div class="mt-3 font-semibold text-center">{{ account.name }}</div>
                            <div class="mt-1 text-center text-stone-800">Added: {{ account.created_at }}</div>
                        </div>
                    </MixpostPanel>
                </template>

                <button @click="addSocial = true"
                        class="border border-stone-600 rounded-lg hover:border-indigo-500 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                    <span class="block p-6">
                        <span class="flex flex-col justify-center items-center">
                            <PlusIcon class="w-7 h-7"/>
                            <span class="mt-2 text-lg">Add account</span>
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <MixpostModal :show="addSocial"
                  :closeable="true"
                  @close="addSocial = false">
        <div class="flex flex-col">
            <MixpostAddTwitterAccount/>
            <MixpostAddFacebookAccount/>
        </div>
    </MixpostModal>
</template>
