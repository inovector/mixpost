<script setup>
import {computed, inject, onMounted, watch} from "vue";
import CountTextCharacters from "../../Util/CountTextCharacters";
import Mastodon from "../../SocialProviders/Mastodon";
import Twitter from "twitter-text";
import {findIndex, minBy, debounce, filter} from "lodash";
import useEditor from "@/Composables/useEditor";
import usePostVersions from "@/Composables/usePostVersions";

const postCtx = inject('postCtx')

const props = defineProps({
    selectedAccounts: {
        type: Array,
        required: true,
    },
    versions: {
        type: Array,
        required: true,
    },
    activeVersion: {
        type: Number,
        required: true,
    }
});

const {accountHasVersion} = usePostVersions();
const {getTextFromHtmlString} = useEditor();

const accountsWithTextLimit = computed(() => {
    return props.selectedAccounts.filter((account) => {
        return account.provider_options.post_character_limit !== null;
    }).map((account) => {
        return {
            account_id: account.id,
            provider: account.provider,
            limit: account.provider_options.post_character_limit
        }
    });
})

const accountsWithoutVersion = computed(() => {
    return accountsWithTextLimit.value.filter((account) => {
        return !accountHasVersion(props.versions, account.account_id)
    });
});

const getLimit = (version) => {
    if (!accountsWithTextLimit.value.length) {
        return null;
    }

    // Original version
    // We get the account that has the lowest limit.
    if (!version) {
        if (!accountsWithoutVersion.value.length) {
            return null;
        }

        return minBy(accountsWithoutVersion.value, 'limit').limit
    }

    // Account version
    const indexAccount = findIndex(accountsWithTextLimit.value, {account_id: version});

    if (indexAccount !== -1) {
        return accountsWithTextLimit.value[indexAccount].limit;
    }

    return null
}

const getProvider = (version) => {
    // Original version
    // We get the provider that has the lowest limit.
    if (!version) {
        if (!accountsWithoutVersion.value.length) {
            return null;
        }

        return minBy(accountsWithoutVersion.value, 'limit').provider;
    }

    const item = props.selectedAccounts.find((account) => account.id === version);

    return item ? item.provider : null;
}

const getAccountName = (version) => {
    const item = props.selectedAccounts.find((account) => account.id === version);

    return item ? item.name : null;
}

const getEnabledVersions = () => {
    return filter(props.versions, (version) => {
        // Original version is always enabled
        if (version.account_id === 0) {
            return true;
        }

        return isAccountSelected(version.account_id)
    });
}

const getPostBody = (version) => {
    const item = props.versions.find((versionItem) => versionItem.account_id === version);

    return item ? item.content[0].body : '';
}

const getContentLength = (provider, text = null) => {
    const content = text ? text : getTextFromHtmlString(getPostBody(props.activeVersion));

    switch (provider) {
        case 'mastodon':
            return Mastodon.getPostLength(content);
        case 'twitter':
            return Twitter.getTweetLength(content);
        default:
            return CountTextCharacters.getLength(content);
    }
}

const isAccountSelected = (accountId) => {
    return props.selectedAccounts.map(account => account.id).includes(accountId);
}

const calc = (limit, used) => {
    return limit - used;
}

const pushLimit = (data) => {
    const index = postCtx.textLimit.findIndex(
        (object) => object.account_id === data.account_id
    )
    // If the account ID exists, replace the value
    if (index !== -1) {
        postCtx.textLimit.splice(index, 1, data)
    }
    // If the account ID doesn't exist, push the new object to the array
    else {
        postCtx.textLimit.push(data)
    }
}

const removeAllLimits = () => {
    if (postCtx) {
        postCtx.textLimit = [];
    }
}

const initLimits = () => {
    getEnabledVersions().forEach((version) => {
        const limit = getLimit(version.account_id);

        if (!limit) {
            return;
        }

        const text = getTextFromHtmlString(version.content[0].body);
        const provider = getProvider(version.account_id);
        const remaining = calc(limit, getContentLength(provider, text));

        pushLimit({
            account_id: version.account_id,
            account_name: getAccountName(version.account_id),
            provider: provider,
            limit: limit,
            hit: remaining < 0
        })
    })
}

const providerActiveVersion = computed(() => {
    return getProvider(props.activeVersion);
});

const limitActiveVersion = computed(() => {
    return getLimit(props.activeVersion);
});

const characterUsedActiveVersion = computed(() => {
    return getContentLength(providerActiveVersion.value);
});

const remaining = computed(() => {
    if (limitActiveVersion.value === null) {
        // Doesn't support text limit
        return null;
    }

    return calc(limitActiveVersion.value, characterUsedActiveVersion.value);
});

watch(remaining, debounce(() => {
    if (!limitActiveVersion.value) {
        return;
    }

    pushLimit({
        account_id: props.activeVersion,
        account_name: getAccountName(props.activeVersion),
        provider: providerActiveVersion.value,
        limit: limitActiveVersion.value,
        hit: remaining.value < 0
    })
}, 100));

watch(() => props.versions.length, () => {
    removeAllLimits();
    initLimits();
})

watch(() => props.selectedAccounts, () => {
    removeAllLimits();
    initLimits();
})

onMounted(() => {
    initLimits();
});
</script>
<template>
    <div v-if="remaining !== null" class="flex items-center justify-center">
        <div
            :class="{'text-stone-800': remaining >= 0, 'text-orange-500': remaining * 100 / limitActiveVersion < 20, 'text-red-500': remaining < 0}">
            {{ remaining }}
        </div>
    </div>
</template>
