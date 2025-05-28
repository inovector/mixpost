<script setup>
import {computed, provide, reactive, ref, watch} from "vue";
import {Head, useForm} from '@inertiajs/vue3';
import {router} from "@inertiajs/vue3";
import {cloneDeep, debounce} from "lodash";
import useMounted from "@/Composables/useMounted";
import usePost from "@/Composables/usePost";
import usePostVersions from "@/Composables/usePostVersions";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import PostForm from "@/Components/Post/PostForm.vue";
import PostActions from "@/Components/Post/PostActions.vue";
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PostStatus from "@/Components/Post/PostStatus.vue";
import Alert from "@/Components/Util/Alert.vue";
import PostErrors from "@/Components/Post/PostErrors.vue";
import EyeIcon from "@/Icons/Eye.vue"
import PostActivity from "../../Components/PostActivity/PostActivity.vue";
import Tabs from "../../Components/Navigation/Tabs.vue";
import Tab from "../../Components/Navigation/Tab.vue";
import SidebarIcon from "@/Icons/Sidebar.vue";
import ChatBubbleBottomCenterText from "../../Icons/ChatBubbleBottomCenterText.vue";
import ProLabel from "../../Components/Pro/ProLabel.vue";

const props = defineProps(['post', 'schedule_at', 'accounts', 'prefill']);

const post = props.post ? cloneDeep(props.post) : null;

const context = reactive({
    errors: {},
});

provide('postCtx', context);

const {isMounted} = useMounted();
const showPreview = ref(false);
const isLoading = ref(false);
const triedToSave = ref(false);
const hasError = ref(false);

const tab = ref('preview');

const setTab = (id) => {
    tab.value = id;
}

const {isInHistory, isScheduleProcessing, editAllowed} = usePost();
const {versionObject} = usePostVersions();
const {notify} = useNotifications();

const form = useForm({
    accounts: post ? post.accounts.map(account => account.id) : [],
    versions: post ? post.versions : [versionObject(0, true, props.prefill.body)],
    tags: post ? post.tags : [],
    date: post ? post.scheduled_at.date : props.schedule_at.date,
    time: post ? post.scheduled_at.time : props.schedule_at.time,
});

const postAccounts = computed(() => {
    if (isInHistory.value) {
        return props.post.accounts;
    }

    return props.accounts.filter(account => form.accounts.includes(account.id));
});

const store = (data) => {
    router.post(route('mixpost.posts.store'), data, {
        onSuccess() {
            triedToSave.value = true;
            // After redirect to the edit mode, it's necessary to track the tag changes
            watchTags();
        }
    });
}

const update = (data) => {
    isLoading.value = true;

    axios.put(route('mixpost.posts.update', {post: props.post.id}), data)
        .then(() => {
            hasError.value = false;
        }).catch((error) => {
        if (error.response.status !== 422) {
            notify('error', error.response.data.message);
            hasError.value = true;
            return;
        }

        const validationErrors = error.response.data.errors;

        const mustRefreshPage = validationErrors.hasOwnProperty('in_history') || validationErrors.hasOwnProperty('publishing');

        if (!mustRefreshPage) {
            notify('error', validationErrors);
            hasError.value = true;
        }

        if (mustRefreshPage) {
            router.visit(route('mixpost.posts.edit', {post: props.post.id}));
        }
    }).finally(() => {
        triedToSave.value = true;
        isLoading.value = false;
    });
}

const save = () => {
    const data = {
        accounts: form.accounts.slice(0),
        versions: form.versions.map((version) => {
            return {
                account_id: version.account_id,
                is_original: version.is_original,
                content: version.content.map((item) => {
                    return {
                        body: item.body,
                        media: item.media.map(itemMedia => itemMedia.id)
                    }
                })
            }
        }),
        tags: form.tags.map(tag => tag.id),
        date: form.date,
        time: form.time
    }

    if (!props.post) {
        store(data);
    }

    if (props.post) {
        update(data);
    }
}

const watchTags = () => {
    watch(() => props.post.tags, (val) => {
        form.tags = val;
    })
}

// PostTags component deal directly with tag itself, such as renaming & changing the color,
// in this case, it's necessary to track the 'post.tags' props and update them.
// This if statement will only work in edit mode and when the page is loaded directly.
if (props.post) {
    watchTags();
}

watch(form, debounce(() => {
    if (editAllowed.value) {
        save();
    }
}, 300))
</script>
<template>
    <Head title="Your post"/>

    <div class="flex flex-col grow h-full overflow-y-auto">
        <div class="flex flex-row h-full overflow-y-auto">
            <div class="w-full h-full flex flex-col overflow-x-hidden overflow-y-auto">
                <div class="flex flex-col h-full">
                    <PostErrors/>

                    <div class="row-py h-full overflow-y-auto">
                        <PageHeader title="Your post">
                            <div v-if="$page.props.post" class="flex items-center">
                                <PostStatus :value="$page.props.post.status"/>
                                <div class="flex items-center ml-lg">
                                    <div
                                        :class="{'hidden': !triedToSave, 'animate-ping': isLoading, 'bg-lime-500': !hasError, 'bg-red-500': hasError}"
                                        class="w-4 h-4 mr-xs rounded-full"></div>
                                    <div v-if="!hasError && triedToSave">Saved</div>
                                    <div v-if="hasError">Error on saving</div>
                                </div>
                            </div>
                        </PageHeader>

                        <div class="w-full max-w-(--container-7xl) mx-auto row-px">
                            <Alert v-if="isInHistory" :closeable="false" class="mb-lg">
                                Posts in history cannot be edited.
                            </Alert>

                            <Alert v-if="isScheduleProcessing" :closeable="false" variant="warning" class="mb-lg">
                                This post is being published, check back shortly!
                            </Alert>

                            <PostForm :form="form" :accounts="$page.props.accounts"/>
                        </div>
                    </div>
                </div>
            </div>
            <div :class="{'translate-x-0 pb-32': showPreview, 'translate-x-full md:translate-x-0': !showPreview}"
                 class="fixed md:relative w-full md:w-[750px] h-full overflow-x-hidden overflow-y-auto flex flex-col border-l border-gray-200 bg-stone-500 transition-transform ease-in-out duration-200">
                <Teleport v-if="isMounted && form.accounts.length" to="#navRightButton">
                    <SecondaryButton @click="showPreview = !showPreview" size="xs" class="md:hidden">
                        <template #icon>
                            <SidebarIcon/>
                        </template>
                    </SecondaryButton>
                </Teleport>

                <div class="flex pb-md flex-col h-full">
                    <div class="flex flex-col h-full">
                        <div class="row-px pt-md relative border-b border-gray-200">
                            <Tabs>
                                <Tab @click="setTab('preview')" :active="tab === 'preview'">

                                    <template #icon>
                                        <EyeIcon/>
                                    </template>
                                    Preview
                                </Tab>

                                <Tab @click="setTab('activity')" :active="tab === 'activity'"
                                class="relative">
                                    <template #icon>
                                        <ChatBubbleBottomCenterText/>
                                    </template>
                                    Activity

                                    <ProLabel class="ml-xs"/>
                                </Tab>
                            </Tabs>
                        </div>

                        <div class="flex flex-col h-full overflow-y-auto">
                            <template v-if="tab === 'preview'">
                                <PostPreviewProviders :accounts="postAccounts"
                                                      :versions="form.versions"
                                                      class="row-px row-pt"
                                />
                            </template>

                            <template v-if="tab === 'activity'">
                                <PostActivity :post="$page.props.post"/>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <PostActions :form="form"/>
    </div>
</template>
