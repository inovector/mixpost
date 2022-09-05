<script setup>
import {computed, inject, ref, watch} from "vue";
import {capitalize, uniqBy, clone, cloneDeep} from "lodash";
import usePostVersions from "@/Composables/usePostVersions";
import useEditor from "@/Composables/useEditor";
import Editor from "@/Components/Editor.vue";
import EmojiPicker from '@/Components/EmojiPicker.vue'
import MixpostPanel from "@/Components/Panel.vue";
import MixpostAccount from "@/Components/Account.vue"
import MixpostPostVersionsTab from "@/Components/PostVersionsTab.vue"
import MixpostAddMedia from "@/Components/Media/AddMedia.vue"
// import MixpostProviderPostCharacterCount from "@/Components/ProviderPostCharacterCount.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import ChatIcon from "@/Icons/Chat.vue"

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

const providersWithPostCharactersLimit = computed(() => {
    const items = props.accounts.filter(function (account) {
        return props.form.accounts.includes(account.id) && account.provider_options.post_characters_limit !== null;
    }).map(function (account) {
        return {
            provider: account.provider,
            limit: account.provider_options.post_characters_limit
        };
    });

    return uniqBy(items, 'provider');
});

const isAccountSelected = (account) => {
    return props.form.accounts.includes(account.id);
}

const isAccountUnselectable = (account) => {
    return !isAccountSelected(account) && providersWithDisabledSimultaneousPosting.value.includes(account.provider);
}

/**
 * Post content versions & Editor
 */
const {versionObject, getOriginalVersion, getAccountVersion} = usePostVersions();

const activeVersion = ref(0);

const resetActiveVersion = () => {
    activeVersion.value = 0;
}

const content = computed(() => {
    return getAccountVersion(props.form.versions, activeVersion.value).content;
})

const updateContent = (contentIndex, key, value) => {
    const versionIndex = props.form.versions.findIndex(version => version.account_id === activeVersion.value);

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
    console.log(accountId);

    resetActiveVersion();
}

watch(() => props.form.accounts, (val) => {
    // If an account was deselected, we're make sure to change the active version to the default version
    const isAccountSelected = val.includes(activeVersion.value);

    if (!isAccountSelected) {
        resetActiveVersion();
    }
});

const {insertEmoji, focusEditor} = useEditor();
</script>
<template>
    <div class="flex items-center space-x-3 mb-6">
        <template v-for="account in $page.props.accounts" :key="account.id">
            <button @click="selectAccount(account.id)"
                    :disabled="isAccountUnselectable(account)">
                <MixpostAccount
                    :provider="account.provider"
                    :img-url="account.image"
                    :warning-message="isAccountUnselectable(account) ? capitalize(account.provider) + ' does not allow simultaneous posting of identical content to multiple accounts.' : ''"
                    :active="isAccountSelected(account)"
                    v-tooltip="account.name"
                />
            </button>
        </template>
    </div>
    <MixpostPanel>
        <MixpostPostVersionsTab v-if="form.accounts.length > 1"
                                @add="addVersion"
                                @select="activeVersion = $event"
                                :versions="form.versions"
                                :active-version="activeVersion"
                                :accounts="$page.props.accounts"
                                :selected-accounts="form.accounts"
                                class="mb-3"/>

        <template v-for="(item, index) in content" :key="index">
            <Editor id="postEditor"
                    :value="item.body"
                    @update="updateContent(index, 'body', $event)"
                    placeholder="Type here something interesting for your audience...">
                <template #default="props">
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <div class="flex items-center space-x-2">
                            <EmojiPicker
                                @selected="insertEmoji({editorId: 'postEditor', emoji: $event})"
                                @close="focusEditor({editorId: 'postEditor'})"
                            />

                            <MixpostAddMedia>
                                <button type="button" v-tooltip="'Media'"
                                        class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                    <PhotoIcon/>
                                </button>
                            </MixpostAddMedia>
                        </div>

                        <div class="flex items-center justify-center">
<!--                            In development-->
<!--                            <div class="flex items-center justify-center mr-5">-->
<!--                                <template v-for="item in providersWithPostCharactersLimit" :key="item.provider">-->
<!--                                    <MixpostProviderPostCharacterCount :provider="item.provider"-->
<!--                                                                       :character-limit="item.limit"-->
<!--                                                                       :text="props.bodyText"-->
<!--                                                                       @reached="postContext.reachedMaxCharacterLimit[item.provider] = $event"/>-->
<!--                                </template>-->
<!--                            </div>-->

                            <button v-tooltip="'Add Comment'" type="button"
                                    class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                <ChatIcon/>
                            </button>
                        </div>
                    </div>
                </template>
            </Editor>
        </template>
    </MixpostPanel>
</template>
