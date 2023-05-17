<script setup>
import {computed, inject, onMounted, ref, watch} from "vue";
import {Head, Link} from '@inertiajs/vue3';
import NProgress from 'nprogress'
import {find} from "lodash";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from '@/Components/DataDisplay/PageHeader.vue';
import Account from "@/Components/Account/Account.vue"
import PrimaryButton from "../Components/Button/PrimaryButton.vue";
import Tabs from "@/Components/Navigation/Tabs.vue"
import Tab from "@/Components/Navigation/Tab.vue"
import TwitterReports from "@/Components/Report/TwitterReports.vue"
import FacebookPageReports from "@/Components/Report/FacebookPageReports.vue"
import FacebookGroupReports from "@/Components/Report/FacebookGroupReports.vue"
import MastodonReports from "@/Components/Report/MastodonReports.vue"

const props = defineProps({
    accounts: {
        required: true,
        type: Array,
    }
})

const {notify} = useNotifications();
const appContext = inject('appContext');

const isLoading = ref(false);
const data = ref({
    metrics: {},
    audience: {}
});

const selectAccount = (account) => {
    appContext.dashboard_filter.account_id = account.id;
}

const isAccountSelected = (account) => {
    return appContext.dashboard_filter.account_id === account.id;
}

const selectPeriod = (value) => {
    appContext.dashboard_filter.period = value;
}

const isPeriodSelected = (value) => {
    return appContext.dashboard_filter.period === value;
}

const fetch = () => {
    isLoading.value = true;
    NProgress.start();

    axios.get(route('mixpost.reports'), {
        params: appContext.dashboard_filter
    }).then(function (response) {
        data.value = response.data;
    }).catch(() => {
        notify('error', 'Error retrieving analytics. Try again!');
    }).finally(() => {
        isLoading.value = false;
        NProgress.done();
    });
}

const providers = {
    'twitter': TwitterReports,
    'facebook_page': FacebookPageReports,
    'facebook_group': FacebookGroupReports,
    'mastodon': MastodonReports,
};

const component = computed(() => {
    const account = find(props.accounts, {id: appContext.dashboard_filter.account_id});

    if (account === undefined) {
        return;
    }

    return providers[account.provider];
});

onMounted(() => {
    if (!props.accounts.length) {
        return null;
    }

    if (!appContext.dashboard_filter.account_id) {
        selectAccount(props.accounts[0]);
        return null;
    }

    fetch();
})

watch(appContext.dashboard_filter, () => {
    fetch()
});
</script>
<template>
    <Head title="Dashboard"/>

    <div class="row-py">
        <PageHeader title="Dashboard">
            <Tabs v-if="accounts.length">
                <Tab @click="selectPeriod('7_days')" :active="isPeriodSelected('7_days')">7 days</Tab>
                <Tab @click="selectPeriod('30_days')" :active="isPeriodSelected('30_days')">30 days</Tab>
                <Tab @click="selectPeriod('90_days')" :active="isPeriodSelected('90_days')">90 days</Tab>
            </Tabs>
        </PageHeader>

        <div class="row-px flex items-center">
            <div class="w-full">
                <div v-if="accounts.length" class="flex flex-wrap items-center gap-sm">
                    <template v-for="account in accounts" :key="account.id">
                        <button @click="selectAccount(account)" type="button">
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
                    <p class="mb-xs">You don't have an social account, please add at least one.</p>
                    <Link :href="route('mixpost.accounts.index')">
                        <PrimaryButton>Add accounts</PrimaryButton>
                    </Link>
                </div>
            </div>
        </div>

        <component :is="component" :data="data" :isLoading="isLoading"/>
    </div>
</template>
