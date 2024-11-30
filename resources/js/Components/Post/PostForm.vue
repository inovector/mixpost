<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {capitalize, clone, cloneDeep} from "lodash";
import usePost from "@/Composables/usePost";
import usePostVersions from "@/Composables/usePostVersions";
import useEditor from "@/Composables/useEditor";
import Editor from "@/Components/Package/Editor.vue";
import EmojiPicker from '@/Components/Package/EmojiPicker.vue'
import Panel from "@/Components/Surface/Panel.vue";
import Account from "@/Components/Account/Account.vue"
import PostVersionsTab from "@/Components/Post/PostVersionsTab.vue"
import PostAddMedia from "@/Components/Post/PostAddMedia.vue"
import PostMedia from "@/Components/Post/PostMedia.vue"
import PostCharacterCount from "@/Components/Post/PostCharacterCount.vue"
import Flex from "../Layout/Flex.vue";
import Plus from "../../Icons/Plus.vue";
import Hashtag from "../../Icons/Hashtag.vue";
import Variable from "../../Icons/Variable.vue";
import RectangleGroup from "../../Icons/RectangleGroup.vue";
import ProEditorButton from "../Pro/ProEditorButton.vue";
import PostContentValidator from "./PostContentValidator.vue";
import Sparkles from "../../Icons/Sparkles.vue";

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

const {editAllowed} = usePost();

/**
 * Account
 */
const selectAccount = (account) => {
    if (!editAllowed.value) {
        return;
    }

    if (props.form.accounts.includes(account)) {
        props.form.accounts = props.form.accounts.filter(item => item !== account);
        return;
    }

    const accounts = clone(props.form.accounts);
    accounts.push(account);

    props.form.accounts = accounts;
}

const selectedAccounts = computed(() => {
    return props.accounts.filter(function (account) {
        return isAccountSelected(account);
    })
});

const providersWithDisabledSimultaneousPosting = computed(() => {
    return selectedAccounts.value.filter((account) => {
        return !account.post_configs.simultaneous_posting;
    }).map((account) => {
        return account.provider;
    });
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
const {
    versionObject,
    getOriginalVersion,
    getAccountVersion,
    getIndexAccountVersion
} = usePostVersions();

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
    const originalVersion = getOriginalVersion(props.form.versions);

    newVersion.content = cloneDeep(originalVersion.content);
    newVersion.options = cloneDeep(originalVersion.options);

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
                    :name="account.name"
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
                    :editable="editAllowed"
                    @update="updateContent(index, 'body', $event)">
                <template #default="props">
                    <PostMedia :media="item.media"/>

                    <Flex :responsive="false"
                          class="relative justify-between border-t border-gray-200 pt-md mt-md">
                        <div v-if="!editAllowed" class="absolute w-full h-full"></div>

                        <Flex :responsive="false">
                            <EmojiPicker
                                @selected="insertEmoji({editorId: 'postEditor', emoji: $event})"
                                @close="focusEditor({editorId: 'postEditor'})"
                            />

                            <PostAddMedia @insert="updateContent(index, 'media', [...item.media, ...$event])"/>

                            <ProEditorButton tooltip="Open Hashtag Manager">
                                <Hashtag/>
                            </ProEditorButton>

                            <ProEditorButton tooltip="Open Variable Manager">
                                <Variable/>
                            </ProEditorButton>

                            <ProEditorButton tooltip="Open Template Manager">
                                <RectangleGroup/>
                            </ProEditorButton>

                            <ProEditorButton tooltip="AI Assistant">
                                <Sparkles/>
                            </ProEditorButton>
                        </Flex>

                       <Flex>
                           <PostCharacterCount :selectedAccounts="selectedAccounts"
                                               :versions="form.versions"
                                               :activeVersion="activeVersion"
                                               :activeContent="index"/>

                           <ProEditorButton tooltip="Add first comment">
                               <Plus/>
                           </ProEditorButton>
                       </Flex>
                    </Flex>
                </template>
            </Editor>

            <PostContentValidator
                :selectedAccounts="selectedAccounts"
                :activeVersion="activeVersion"
                :activeContent="index"
                :versions="form.versions"/>
        </template>
    </Panel>
</template>
