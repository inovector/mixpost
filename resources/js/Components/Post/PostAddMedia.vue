<script setup>
import {computed, inject, onMounted, watch} from "vue";
import {filter, findIndex, minBy, sum} from "lodash";
import usePostVersions from "@/Composables/usePostVersions";
import AddMedia from "@/Components/Media/AddMedia.vue"
import PhotoIcon from "@/Icons/Photo.vue"

const postContext = inject('postContext')

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
    media: { // Media of current version
        type: Array,
        default: []
    }
})

defineEmits(['insert']);

const {accountHasVersion} = usePostVersions();

const accountsWithoutVersion = computed(() => {
    return props.selectedAccounts.filter((account) => {
        return !accountHasVersion(props.versions, account.id)
    });
});

const getMinLimit = (type) => {
    return minBy(accountsWithoutVersion.value, `provider_options.media_limit.${type}`).provider_options.media_limit[type];
}

const getLimit = (version) => {
    // Original version
    // We get the account that has the lowest limit.
    if (!version) {
        if (!accountsWithoutVersion.value.length) {
            return null;
        }

        return {
            photos: getMinLimit('photos'),
            videos: getMinLimit('videos'),
            gifs: getMinLimit('gifs'),
            allow_mixing: getMinLimit('allow_mixing'),
        }
    }

    // Account version
    const indexAccount = findIndex(props.selectedAccounts, {id: version});

    if (indexAccount !== -1) {
        return props.selectedAccounts[indexAccount].provider_options.media_limit;
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

        const accountsLimit = accountsWithoutVersion.value.map((account) => {
            return {
                id: account.id,
                provider: account.provider,
                limit: sum([
                    account.provider_options.media_limit.photos,
                    account.provider_options.media_limit.videos,
                    account.provider_options.media_limit.gifs,
                    account.provider_options.media_limit.allow_mixing ? 1 : 0,
                ])
            }
        });

        return minBy(accountsLimit, 'limit').provider;
    }

    const item = props.selectedAccounts.find((account) => account.id === version);

    return item ? item.provider : null;
}

const getAccountName = (version) => {
    const item = props.selectedAccounts.find((account) => account.id === version);

    return item ? item.name : null;
}

const isAccountSelected = (accountId) => {
    return props.selectedAccounts.map(account => account.id).includes(accountId);
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
const isMediaTypeMixing = (obj) => {
    const values = Object.values(obj);

    const nonZeroValues = values.filter(value => value !== 0);

    if (nonZeroValues.length === 0) {
        return false;
    }

    return nonZeroValues.length !== 1;
}

const getMediaLength = (media) => {
    const byType = {
        photos: filter(media, {type: 'image'}).length,
        videos: filter(media, {type: 'video'}).length,
        gifs: filter(media, {type: 'gif'}).length,
    }

    return Object.assign(byType, {
        is_mixing: isMediaTypeMixing(byType),
    })
}

const pushLimit = (data) => {
    const index = postContext.mediaLimit.findIndex(
        (object) => object.account_id === data.account_id
    )
    // If the account ID exists, replace the value
    if (index !== -1) {
        postContext.mediaLimit.splice(index, 1, data)
    }
    // If the account ID doesn't exist, push the new object to the array
    else {
        postContext.mediaLimit.push(data)
    }
}

const removeAllLimits = () => {
    postContext.mediaLimit = [];
}
const initLimits = () => {
    getEnabledVersions().forEach((version) => {
        const limit = getLimit(version.account_id);

        if (!limit) {
            return;
        }

        const provider = getProvider(version.account_id);
        const mediaUsed = getMediaLength(version.content[0].media)

        pushLimit({
            account_id: version.account_id,
            account_name: getAccountName(version.account_id),
            provider,
            photos: {
                limit: limit.photos,
                hit: mediaUsed.photos > limit.photos
            },
            videos: {
                limit: limit.videos,
                hit: mediaUsed.videos > limit.videos
            },
            gifs: {
                limit: limit.gifs,
                hit: mediaUsed.gifs > limit.gifs
            },
            is_mixing: mediaUsed.is_mixing
        })
    })
}

const limitActiveVersion = computed(() => {
    return getLimit(props.activeVersion);
});

const providerActiveVersion = computed(() => {
    return getProvider(props.activeVersion);
});

const mediaUsedActiveVersion = computed(() => {
    return getMediaLength(props.media);
});

watch(mediaUsedActiveVersion, () => {
    if (!limitActiveVersion.value) {
        return;
    }

    pushLimit({
        account_id: props.activeVersion,
        account_name: getAccountName(props.activeVersion),
        provider: providerActiveVersion.value,
        photos: {
            limit: limitActiveVersion.value.photos,
            hit: mediaUsedActiveVersion.value.photos > limitActiveVersion.value.photos
        },
        videos: {
            limit: limitActiveVersion.value.videos,
            hit: mediaUsedActiveVersion.value.videos > limitActiveVersion.value.videos
        },
        gifs: {
            limit: limitActiveVersion.value.gifs,
            hit: mediaUsedActiveVersion.value.gifs > limitActiveVersion.value.gifs
        },
        is_mixing: mediaUsedActiveVersion.value.is_mixing
    })
})

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
    <AddMedia @insert="$emit('insert', $event)">
        <button type="button"
                v-tooltip="'Media'"
                class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
            <PhotoIcon/>
        </button>
    </AddMedia>
</template>
