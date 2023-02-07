<script setup>
import {Head, Link} from '@inertiajs/inertia-vue3';
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import Panel from "@/Components/Surface/Panel.vue";
import Account from "@/Components/Account/Account.vue"
import PrimaryButton from "../Components/Button/PrimaryButton.vue";
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import ChartBar from "@/Components/Package/ChartBar.vue";
import {ref} from "vue";

defineProps({
    accounts: {
        required: true,
        type: Array,
    }
})

const selectedAccount = ref(7);

const isAccountSelected = (account) => {
    return selectedAccount.value === account.id;
}
</script>
<template>
    <Head title="Dashboard"/>

    <div class="row-py">
        <PageHeader title="Dashboard">
            <Tabs>
                <Tab>7 days</Tab>
                <Tab :active="true">30 days</Tab>
                <Tab>90 days</Tab>
                <Tab>Custom</Tab>
            </Tabs>
        </PageHeader>

        <div class="row-px flex items-center">
            <div class="w-full md:w-1/2">
                <div v-if="accounts.length" class="flex flex-wrap items-center gap-sm">
                    <template v-for="account in accounts" :key="account.id">
                        <button type="button">
                            <Account
                                :provider="account.provider"
                                :active="isAccountSelected(account)"
                                :img-url="account.image"
                                v-tooltip="account.name"
                            />
                        </button>
                    </template>
                </div>
                <div v-else>
                    <Link :href="route('mixpost.accounts.index')">
                        <PrimaryButton>Add account</PrimaryButton>
                    </Link>
                </div>
            </div>
        </div>

        <div class="row-px mt-2xl">
            <div class="grid grid-cols-3 gap-sm">
                <Panel>
                    <template #title>Actions</template>
                    <div class="font-extrabold text-indigo-500 text-2xl">12</div>
                </Panel>

                <Panel>
                    <template #title>Views</template>
                    <div class="font-extrabold text-indigo-500 text-2xl">14</div>
                </Panel>

                <Panel>
                    <template #title>Impressions</template>
                    <div class="font-extrabold text-indigo-500 text-2xl">40</div>
                </Panel>
            </div>
        </div>

        <div class="row-px mt-2xl">
            <Panel>
                <template #title>Follows</template>

                <ChartBar/>
            </Panel>
        </div>
    </div>
</template>
