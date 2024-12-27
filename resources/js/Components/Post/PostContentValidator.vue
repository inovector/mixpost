<script setup>
import {onBeforeUnmount, onMounted, watch} from "vue";
import emitter from "@/Services/emitter";
import {debounce, filter} from "lodash";
import usePostValidator from "../../Composables/usePostValidator";
import useEditor from "@/Composables/useEditor";
import usePostCharacterLimit from "../../Composables/usePostCharacterLimit";
import usePostMediaLimit from "../../Composables/usePostMediaLimit";

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
    },
    activeContent: {
        type: Number,
        required: true,
    }
});

const mediaErrorGroup = 'media';
const charErrorGroup = 'char_limit';

const {addAccountError, removeAccountError, removeError} = usePostValidator();
const {getTextFromHtmlString} = useEditor();

const getAccount = (accountId) => {
    return props.selectedAccounts.find(account => account.id === accountId) || null;
};

const isAccountSelected = (accountId) =>
    props.selectedAccounts.some((account) => account.id === accountId);

const getEnabledVersions = () =>
    filter(props.versions, (version) =>
        version.account_id === 0 || isAccountSelected(version.account_id)
    );

const clearErrors = () => {
    removeError({group: mediaErrorGroup});
    removeError({group: charErrorGroup});
};

const {
    currentCharMaxLimit,
    currentCharLeft,
    getCharMaxLimit,
    getTextLength,
    calculateCharLeft
} = usePostCharacterLimit(props);

const handleCharMaxLimitError = ({
                                     charLimit,
                                     charLeft,
                                     accountId,
                                     accountName,
                                     providerName
                                 }) => {
    if (charLeft < 0) {
        const maxCharMessage = `Maximum of ${charLimit} characters allowed.`;

        addAccountError({
            group: charErrorGroup,
            key: `c`,
            message: maxCharMessage,
            accountId: accountId,
            accountName: accountName,
            providerName: providerName,
        });
    } else {
        removeAccountError({
            group: charErrorGroup,
            key: `c`,
            accountId: accountId,
        });
    }
}

const {
    mediaTypesBasic,
    currentMediaMaxLimits,
    currentMediaUsed,
    getMediaMaxLimits,
    getMediaLength
} = usePostMediaLimit(props);

const handleMediaMaxLimitError = ({used, limits, accountId, accountName}) => {
    const text = {
        'photos': 'Maximum of :count photos allowed.',
        'videos': 'Maximum of :count videos allowed.',
        'gifs': 'Maximum of :count GIFs allowed.',
    }

    mediaTypesBasic.forEach((type) => {
        if (used[type] > limits[type].limit) {
            addAccountError({
                group: mediaErrorGroup,
                key: `c_${type}`,
                message: text[type].replace(':count', limits[type].limit),
                accountId,
                accountName,
                providerName: limits[type].provider,
            });
        } else {
            removeAccountError({group: mediaErrorGroup, key: `c_${type}`, accountId});
        }
    });

    if (used.mixing && !limits.allow_mixing.limit) {
        addAccountError({
            group: mediaErrorGroup,
            key: `c_mixing`,
            message: 'Combining different media types (videos, photos, GIFs) is not allowed.',
            accountId,
            accountName,
            providerName: limits.allow_mixing.provider,
        });
    } else {
        removeAccountError({group: mediaErrorGroup, key: `c_mixing`, accountId});
    }
};

watch(
    [
        currentCharLeft,
        currentCharMaxLimit,
        currentMediaMaxLimits,
        currentMediaUsed,
    ],
    debounce(() => {
        const account = getAccount(props.activeVersion);

        handleCharMaxLimitError({
            charLimit: currentCharMaxLimit.value.limit,
            charLeft: currentCharLeft.value,
            accountId: props.activeVersion,
            accountName: account?.name || "",
            providerName: currentCharMaxLimit.value?.provider.name,
        });

        if (currentMediaMaxLimits.value) {
            handleMediaMaxLimitError({
                used: currentMediaUsed.value,
                limits: currentMediaMaxLimits.value,
                accountId: props.activeVersion,
                accountName: account?.name || "",
            });
        }
    }, 100));

const init = () => {
    getEnabledVersions().forEach((version) => {
        const accountName = getAccount(version.account_id)?.name;
        const charMaxLimit = getCharMaxLimit(version.account_id);
        const mediaMaxLimits = getMediaMaxLimits(version.account_id);

        version.content.forEach(item => {
            const text = getTextFromHtmlString(item.body);
            const usedMedia = getMediaLength(item.media);

            if (charMaxLimit?.limit) {
                handleCharMaxLimitError({
                    charLimit: charMaxLimit.limit,
                    charLeft: calculateCharLeft(charMaxLimit.limit, getTextLength(charMaxLimit?.provider.id, text)),
                    accountId: version.account_id,
                    accountName: accountName || '',
                    providerName: charMaxLimit?.provider.name,
                });
            }

            if (mediaMaxLimits) {
                handleMediaMaxLimitError({
                    used: usedMedia,
                    limits: mediaMaxLimits,
                    accountId: version.account_id,
                    accountName: accountName || '',
                });
            }
        });
    });
};

const handleUpdate = () => {
    clearErrors();
    init();
};

watch(() => props.activeVersion, handleUpdate);
watch(() => props.versions.length, handleUpdate);
watch(() => props.selectedAccounts, handleUpdate);

onMounted(() => {
    emitter.on('postVersionContentDeleted', () => {
        handleUpdate();
    });

    handleUpdate();
});

onBeforeUnmount(() => {
    emitter.off('postVersionContentDeleted');
});
</script>
<template></template>
