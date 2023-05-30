<script setup>
import {computed, inject, onMounted, watch} from "vue";
import {filter, findIndex, minBy} from "lodash";
import usePostVersions from "@/Composables/usePostVersions";
import AddMedia from "@/Components/Media/AddMedia.vue"
import PhotoIcon from "@/Icons/Photo.vue"

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
    const item = minBy(accountsWithoutVersion.value, `provider_options.media_limit.${type}`);

    return {
        provider: item.provider,
        count: item.provider_options.media_limit[type]
    }
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
        const account = props.selectedAccounts[indexAccount];
        const accountMediaLimit = account.provider_options.media_limit;

        return {
            photos: {
                provider: account.provider,
                count: accountMediaLimit.photos
            },
            videos: {
                provider: account.provider,
                count: accountMediaLimit.videos
            },
            gifs: {
                provider: account.provider,
                count: accountMediaLimit.gifs
            },
            allow_mixing: {
                provider: account.provider,
                count: accountMediaLimit.allow_mixing
            },
        }
    }

    return null
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
        mixing: isMediaTypeMixing(byType),
    })
}

const pushLimit = (data) => {
    const index = postCtx.mediaLimit.findIndex(
        (object) => object.account_id === data.account_id
    )
    // If the account ID exists, replace the value
    if (index !== -1) {
        postCtx.mediaLimit.splice(index, 1, data)
    }
    // If the account ID doesn't exist, push the new object to the array
    else {
        postCtx.mediaLimit.push(data)
    }
}

const removeAllLimits = () => {
    if (postCtx) {
        postCtx.mediaLimit = [];
    }
}

const initLimits = () => {
    getEnabledVersions().forEach((version) => {
        const limit = getLimit(version.account_id);

        if (!limit) {
            return;
        }

        const mediaUsed = getMediaLength(version.content[0].media)

        pushLimit({
            account_id: version.account_id,
            account_name: getAccountName(version.account_id),
            photos: {
                provider: limit.photos.provider,
                limit: limit.photos.count,
                hit: mediaUsed.photos > limit.photos.count
            },
            videos: {
                provider: limit.videos.provider,
                limit: limit.videos.count,
                hit: mediaUsed.videos > limit.videos.count
            },
            gifs: {
                provider: limit.gifs.provider,
                limit: limit.gifs.count,
                hit: mediaUsed.gifs > limit.gifs.count
            },
            mixing: {
                provider: limit.allow_mixing.provider,
                hit: mediaUsed.mixing && !limit.allow_mixing.count
            }
        })
    })
}

const limitActiveVersion = computed(() => {
    return getLimit(props.activeVersion);
});

const mediaUsedActiveVersion = computed(() => {
    return getMediaLength(props.media);
});

watch(mediaUsedActiveVersion, () => {
    const limit = limitActiveVersion.value;

    if (!limit) {
        return;
    }

    const mediaUsed = mediaUsedActiveVersion.value;

    pushLimit({
        account_id: props.activeVersion,
        account_name: getAccountName(props.activeVersion),
        photos: {
            provider: limit.photos.provider,
            limit: limit.photos.count,
            hit: mediaUsed.photos > limit.photos.count
        },
        videos: {
            provider: limit.videos.provider,
            limit: limit.videos.count,
            hit: mediaUsed.videos > limit.videos.count
        },
        gifs: {
            provider: limit.gifs.provider,
            limit: limit.gifs.count,
            hit: mediaUsed.gifs > limit.gifs.count
        },
        mixing: {
            provider: limit.allow_mixing.provider,
            hit: mediaUsed.mixing && !limit.allow_mixing.count
        }
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
