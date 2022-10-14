<script setup>
import {computed, inject, onMounted, ref, watch} from "vue";
import {capitalize, clone, cloneDeep} from "lodash";
import usePostVersions from "@/Composables/usePostVersions";
import useEditor from "@/Composables/useEditor";
import Editor from "@/Components/Package/Editor.vue";
import EmojiPicker from '@/Components/Package/EmojiPicker.vue'
import Panel from "@/Components/Surface/Panel.vue";
import Account from "@/Components/Account/Account.vue"
import PostVersionsTab from "@/Components/Post/PostVersionsTab.vue"
import AddMedia from "@/Components/Media/AddMedia.vue"
import PostMedia from "@/Components/Post/PostMedia.vue"
// import ProviderPostCharacterCount from "@/Components/Post/ProviderPostCharacterCount.vue"
import PhotoIcon from "@/Icons/Photo.vue"

const postContext = inject('postContext')

const props = defineProps({
    form: {
        required: true,
        type: Object
    },
    accounts: {
        required: true,
        type: Array
    },
});

/**
 * Account
 */
const selectAccount = (account) => {
    if (props.form.accounts.includes(account)) {
        props.form.accounts = props.form.accounts.filter(item => item !== account);
        return;
    }

    const accounts = clone(props.form.accounts);
    accounts.push(account);

    props.form.accounts = accounts;
}

const providersWithDisabledSimultaneousPosting = computed(() => {
    return props.accounts.filter(function (account) {
        return props.form.accounts.includes(account.id) && !account.provider_options.simultaneous_posting_on_multiple_accounts;
    }).map(function (account) {
        return account.provider;
    });
});

// const providersWithPostCharactersLimit = computed(() => {
//     const items = props.accounts.filter(function (account) {
//         return props.form.accounts.includes(account.id) && account.provider_options.post_characters_limit !== null;
//     }).map(function (account) {
//         return {
//             provider: account.provider,
//             limit: account.provider_options.post_characters_limit
//         };
//     });
//
//     return uniqBy(items, 'provider');
// });

const isAccountSelected = (account) => {
    return props.form.accounts.includes(account.id);
}

const isAccountUnselectable = (account) => {
    return !isAccountSelected(account) && providersWithDisabledSimultaneousPosting.value.includes(account.provider);
}

/**
 * Post content versions & Editor
 */
const {versionObject, getOriginalVersion, getAccountVersion, getIndexAccountVersion} = usePostVersions();

const activeVersion = ref(0);

const resetActiveVersion = () => {
    activeVersion.value = 0;
}

const content = computed(() => {
    return getAccountVersion(props.form.versions, activeVersion.value).content;
})

const updateContent = (contentIndex, key, value) => {
    const versionIndex = getIndexAccountVersion(props.form.versions, activeVersion.value);

    props.form.versions[versionIndex].content[contentIndex][key] = value;
}

const addVersion = (accountId) => {
    let newVersion = versionObject(accountId);

    // Copy content from the default version to the new version
    newVersion.content = cloneDeep(getOriginalVersion(props.form.versions).content);

    props.form.versions.push(newVersion);

    // Set added version as active version
    activeVersion.value = accountId;
}

const removeVersion = (accountId) => {
    if (!accountId) {
        return;
    }

    const versionIndex = getIndexAccountVersion(props.form.versions, accountId);

    if (versionIndex < 0) {
        return;
    }

    props.form.versions.splice(versionIndex, 1);
    resetActiveVersion();
}

const setupVersions = () => {
    // If an account was deselected, we're make sure to change the active version to the default version
    const isAccountSelected = props.form.accounts.includes(activeVersion.value);

    if (!isAccountSelected) {
        resetActiveVersion();
    }

    // If is only one account selected and if is original active version, we switch active version for that account.
    if (props.form.accounts.length === 1 && activeVersion.value === 0) {
        const firstAccountId = props.form.accounts[0];

        if (firstAccountId !== 0 && getAccountVersion(props.form.versions, firstAccountId)) {
            activeVersion.value = firstAccountId;
        }
    }
}

onMounted(() => {
    setupVersions();
})

watch(() => props.form.accounts, () => {
    setupVersions();
});

const {insertEmoji, focusEditor} = useEditor();
</script>
<template>
    <div class="flex flex-wrap items-center gap-sm mb-lg">
        <template v-for="account in $page.props.accounts" :key="account.id">
            <button @click="selectAccount(account.id)"
                    :disabled="isAccountUnselectable(account)">
                <Account
                    :provider="account.provider"
                    :img-url="account.image"
                    :warning-message="isAccountUnselectable(account) ? capitalize(account.provider) + ' does not allow simultaneous posting of identical content to multiple accounts.' : ''"
                    :active="isAccountSelected(account)"
                    v-tooltip="account.name"
                />
            </button>
        </template>
    </div>

    <Panel>
        <PostVersionsTab v-if="form.accounts.length > 1"
                         @add="addVersion"
                         @remove="removeVersion"
                         @select="activeVersion = $event"
                         :versions="form.versions"
                         :active-version="activeVersion"
                         :accounts="$page.props.accounts"
                         :selected-accounts="form.accounts"
                         class="mb-sm"/>

        <template v-for="(item, index) in content" :key="index">
            <Editor id="postEditor"
                    :value="item.body"
                    @update="updateContent(index, 'body', $event)"
                    placeholder="Type here something interesting for your audience...">
                <template #default="props">
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <div class="flex items-center space-x-xs">
                            <EmojiPicker
                                @selected="insertEmoji({editorId: 'postEditor', emoji: $event})"
                                @close="focusEditor({editorId: 'postEditor'})"
                            />

                            <AddMedia @insert="updateContent(index, 'media', [...item.media, ...$event])">
                                <button type="button" v-tooltip="'Media'"
                                        class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                    <PhotoIcon/>
                                </button>
                            </AddMedia>
                        </div>

                        <div class="flex items-center justify-center">
                            <!--                            In development-->
                            <!--                            <div class="flex items-center justify-center mr-5">-->
                            <!--                                <template v-for="item in providersWithPostCharactersLimit" :key="item.provider">-->
                            <!--                                    <ProviderPostCharacterCount :provider="item.provider"-->
                            <!--                                                                       :character-limit="item.limit"-->
                            <!--                                                                       :text="props.bodyText"-->
                            <!--                                                                       @reached="postContext.reachedMaxCharacterLimit[item.provider] = $event"/>-->
                            <!--                                </template>-->
                            <!--                            </div>-->
                        </div>
                    </div>
                </template>
            </Editor>

            <PostMedia :media="item.media"/>
        </template>
    </Panel>
</template>
