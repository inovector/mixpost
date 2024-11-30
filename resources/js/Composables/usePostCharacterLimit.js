import {computed} from "vue";
import CountTextCharacters from "../Util/CountTextCharacters";
import Mastodon from "../SocialProviders/Mastodon";
import Twitter from "twitter-text";
import {minBy, maxBy} from "lodash";
import useEditor from "@/Composables/useEditor";
import usePostVersions from "@/Composables/usePostVersions";

export default function usePostCharacterLimit(props) {
    const {accountHasVersion} = usePostVersions();
    const {getTextFromHtmlString} = useEditor();

    const accountsWithoutVersion = computed(() => {
        return props.selectedAccounts.filter(account => !accountHasVersion(props.versions, account.id));
    });

    const getCharLimitForType = (boundary, postType, account) => {
        const rules = account.post_configs.text_char_limit[boundary];
        return rules[postType] || rules.default;
    };

    const getCharLimit = (version, boundary, comparator) => {
        if (!props.selectedAccounts.length) return null;

        const accounts = version === 0 ? accountsWithoutVersion.value : props.selectedAccounts.filter(account => account.id === version);

        const accountsLimit = accounts.map(account => {
            return {
                account_id: account.id,
                provider: {
                    id: account.provider,
                    name: account.provider_name,
                },
                limit: getCharLimitForType(boundary, 'default', account),
            };
        });

        return accountsLimit.length ? comparator(accountsLimit, 'limit') : null;
    };

    const getCharMaxLimit = (version) => getCharLimit(version, 'max', minBy) || null;
    const getCharMinLimit = (version) => getCharLimit(version, 'min', maxBy) || null;

    const getTextLength = (providerId, text) => {
        switch (providerId) {
            case 'mastodon':
                return Mastodon.getPostLength(text);
            case 'twitter':
                return Twitter.getTweetLength(text);
            default:
                return CountTextCharacters.getLength(text);
        }
    };

    const calculateCharLeft = (limit, used) => limit - used;

    const getPostVersionContentBody = (version) => {
        const item = props.versions.find(({account_id}) => account_id === version);
        return item?.content[props.activeContent]?.body || '';
    };

    const currentCharMaxLimit = computed(() => getCharMaxLimit(props.activeVersion));
    const currentCharMinLimit = computed(() => getCharMinLimit(props.activeVersion));
    const currentCharUsed = computed(() => getTextLength(currentCharMaxLimit.value?.provider.id, getTextFromHtmlString(getPostVersionContentBody(props.activeVersion))));
    const currentCharLeft = computed(() => currentCharMaxLimit.value?.limit || null ? calculateCharLeft(currentCharMaxLimit.value?.limit, currentCharUsed.value) : null);

    return {
        currentCharMaxLimit,
        currentCharMinLimit,
        currentCharUsed,
        currentCharLeft,
        getCharLimitForType,
        getCharMaxLimit,
        getCharMinLimit,
        getTextLength,
        calculateCharLeft,
    };
}
