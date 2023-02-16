<script setup>
import {computed, inject, onMounted, watch} from "vue";
import Twitter from "twitter-text";
import {findIndex, minBy, debounce, filter} from "lodash";
import useEditor from "@/Composables/useEditor";
import usePostVersions from "@/Composables/usePostVersions";

const postContext = inject('postContext')

const props = defineProps({
    selectedAccounts: {
        type: Array,
        required: true,
    },
    activeVersion: {
        type: Number,
        required: true,
    },
    versions: {
        type: Array,
        required: true,
    },
    text: {
        required: false,
        default: ''
    }
});

const {accountHasVersion} = usePostVersions();
const {extractTextFromHtml} = useEditor();

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
    // We get the provider that has the lowest limit.
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

const getTextLength = (provider, text = null) => {
    const _text = text ? text : props.text;

    return {
        'twitter': Twitter.getTweetLength(_text),
        'facebook': _text.length,
        'mastodon': _text.length,
    }[provider]
}

const providerActiveVersion = computed(() => {
    return getProvider(props.activeVersion);
});

const limitActiveVersion = computed(() => {
    return getLimit(props.activeVersion);
});

const characterUsedActiveVersion = computed(() => {
    return getTextLength(providerActiveVersion.value);
});

const calc = (limit, used) => {
    return limit - used;
}

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

    postContext.textLimit[props.activeVersion] = {
        account_name: getAccountName(props.activeVersion),
        provider: providerActiveVersion.value,
        limit: limitActiveVersion.value,
        hit: remaining.value < 0
    }
}, 100));

const setupVersionsLimit = () => {
    props.versions.forEach((version) => {
        const limit = getLimit(version.account_id);

        if (!limit) {
            return;
        }

        const text = extractTextFromHtml(version.content[0].body);
        const provider = getProvider(version.account_id);
        const remaining = calc(limit, getTextLength(provider, text));

        postContext.textLimit[version.account_id] = {
            account_name: getAccountName(version.account_id),
            provider: provider,
            limit: limit,
            hit: remaining < 0
        }
    })
}

onMounted(() => {
    setupVersionsLimit();
});

watch(props.versions, (value) => {
    const versions = value.map(item => item.account_id);

    // TODO: create a fnc to postContext to push limits, no need to use account id as key
    console.log(versions);
    console.log(filter(postContext.textLimit, (_, key) => {
        return !versions.includes(key);
    }))
    // Remove limits
    // postContext.textLimit =
})
</script>
<template>
    <div v-if="remaining !== null" class="flex items-center justify-center">
        <div :class="{'text-stone-800': remaining >= 0, 'text-red-500': remaining < 0}">{{ remaining }}</div>
    </div>
</template>
