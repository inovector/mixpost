import {computed} from "vue";
import {filter, isEmpty, minBy} from "lodash";
import usePostVersions from "./usePostVersions";
import usePostCharacterLimit from "./usePostCharacterLimit";

export default function usePostMediaLimit(props) {
    const mediaTypesAll = ['photos', 'videos', 'gifs', 'allow_mixing'];
    const mediaTypesBasic = ['photos', 'videos', 'gifs'];

    const {accountHasVersion} = usePostVersions();
    const {getCharLimitForType} = usePostCharacterLimit();

    const accountsWithoutVersion = computed(() =>
        props.selectedAccounts.filter(
            (account) => !accountHasVersion(props.versions, account.id)
        )
    );
    const isAccountSelected = (accountId) =>
        props.selectedAccounts.some((account) => account.id === accountId);

    const getEnabledVersions = () =>
        filter(props.versions, (version) =>
            version.account_id === 0 || isAccountSelected(version.account_id)
        );

    const getMediaLimitForType = (boundary, mediaType, postType, account) => {
        const rules = account.post_configs.media_limit[boundary][mediaType];

        if (rules.hasOwnProperty(postType)) {
            return rules[postType];
        }

        return rules.default;
    };

    const getLowestMaxMediaLimits = (version) => {
        const results = {};
        const accounts = version === 0 ? accountsWithoutVersion.value : props.selectedAccounts.filter(account => account.id === version);

        mediaTypesAll.forEach(mediaType => {
            const accountsLimit = accounts.map(account => {
                const limit = getMediaLimitForType('max', mediaType, 'default', account);

                return {
                    account_id: account.id,
                    provider: account.provider_name,
                    limit: limit,
                };
            });

            results[mediaType] = accountsLimit.length ? minBy(accountsLimit, 'limit') : null;
        });

        return !isEmpty(results) ? results : null;
    };

    const getHighestMinMediaLimits = (version) => {
        const results = {};
        const versionObj = props.versions.find(versionItem => versionItem.account_id === version);
        const accounts = version === 0 ? accountsWithoutVersion.value : props.selectedAccounts.filter(account => account.id === version);

        mediaTypesBasic.forEach(mediaType => {
            const accountsLimit = accounts.map(account => {
                const type = versionObj?.options[account.provider]?.type || 'default';
                const limit = getMediaLimitForType('min', mediaType, type, account);

                return {
                    account_id: account.id,
                    provider: account.provider_name,
                    limit: limit,
                    priority: getCharLimitForType('min', type, account) === 0 ? 1 : 0,
                };
            });

            if (accountsLimit.length) {
                // Using reduce to find the max by priority first and then by limit
                results[mediaType] = accountsLimit.reduce((max, current) => {
                    if (current.priority > max.priority || (current.priority === max.priority && current.limit > max.limit)) {
                        return current;
                    }
                    return max;
                }, accountsLimit[0]);
            } else {
                results[mediaType] = null;
            }
        });

        return !isEmpty(results) ? results : null;
    };

    const getMediaMaxLimits = (version) => {
        if (!version) {
            if (!accountsWithoutVersion.value.length) return null;
            const limits = getLowestMaxMediaLimits(version);

            return mediaTypesAll.reduce((acc, type) => {
                acc[type] = limits[type];
                return acc;
            }, {});
        }

        const account = props.selectedAccounts.find((account) => account.id === version);
        if (!account) return null;

        const limits = getLowestMaxMediaLimits(version);
        return mediaTypesAll.reduce((acc, type) => {
            acc[type] = limits[type];
            return acc;
        }, {});
    };

    const getMediaMinLimits = (version) => {
        const limits = getHighestMinMediaLimits(version);

        return mediaTypesBasic.reduce((acc, type) => {
            acc[type] = limits[type];
            return acc;
        }, {});
    };

    const isMediaTypeMixing = (obj) => {
        const nonZeroValues = Object.values(obj).filter((value) => value !== 0);
        return nonZeroValues.length > 1;
    };

    const getMediaLength = (media) => {
        const byType = {
            photos: filter(media, {type: "image"}).length,
            videos: filter(media, {type: "video"}).length,
            gifs: filter(media, {type: "gif"}).length,
        };
        return {...byType, mixing: isMediaTypeMixing(byType)};
    };

    const currentMedia = computed(() => getEnabledVersions().find(version => version.account_id === props.activeVersion)?.content[props.activeContent]?.media);
    const currentMediaMaxLimits = computed(() => getMediaMaxLimits(props.activeVersion));
    const currentMediaUsed = computed(() => getMediaLength(currentMedia.value));

    return {
        mediaTypesAll,
        mediaTypesBasic,
        currentMedia,
        currentMediaMaxLimits,
        currentMediaUsed,
        getMediaMaxLimits,
        getMediaMinLimits,
        getMediaLength,
    };
}
