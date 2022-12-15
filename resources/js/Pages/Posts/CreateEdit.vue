<script setup>
import {ref, watch} from "vue";
import {Head, useForm} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {cloneDeep, debounce} from "lodash";
import useMounted from "@/Composables/useMounted";
import usePost from "@/Composables/usePost";
import usePostVersions from "@/Composables/usePostVersions";
import useNotifications from "@/Composables/useNotifications";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import PostContext from "@/Context/PostContext.vue";
import PostForm from "@/Components/Post/PostForm.vue";
import PostActions from "@/Components/Post/PostActions.vue";
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PostStatus from "@/Components/Post/PostStatus.vue";
import EyeIcon from "@/Icons/Eye.vue"
import EyeOffIcon from "@/Icons/EyeOff.vue"
import Alert from "@/Components/Util/Alert.vue";

const props = defineProps(['post']);

const post = props.post ? cloneDeep(props.post) : null;

const {isMounted} = useMounted();
const showPreview = ref(false);
const isLoading = ref(false);
const triedToSave = ref(false);
const hasError = ref(false);

const {isInHistory, isScheduleProcessing, editAllowed} = usePost();
const {versionObject} = usePostVersions();
const {notify} = useNotifications();

const form = useForm({
    accounts: post ? post.accounts.map(account => account.id) : [],
    versions: post ? post.versions : [versionObject(0, true)],
    tags: post ? post.tags : [],
    date: post ? post.scheduled_at.date : '',
    time: post ? post.scheduled_at.time : '',
});

const store = (data) => {
    Inertia.post(route('mixpost.posts.store'), data, {
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
            Inertia.visit(route('mixpost.posts.edit', {post: props.post.id}));
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

    <PostContext>
        <div class="flex flex-col grow h-full">
            <div class="flex flex-row h-full overflow-y-auto">
                <div class="w-full md:w-3/5 h-full flex flex-col overflow-x-hidden overflow-y-auto">
                    <div class="row-py">
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

                        <div class="w-full max-w-7xl mx-auto row-px">
                            <Alert v-if="isInHistory" :closeable="false" class="mb-lg">
                                Posts in history cannot be edited.
                            </Alert>
                            <Alert v-if="isScheduleProcessing" :closeable="false" variant="warning" class="mb-lg">
                                This post is being published. It cannot be edited.
                            </Alert>
                            <PostForm :form="form" :accounts="$page.props.accounts"/>
                        </div>
                    </div>
                </div>
                <div :class="{'translate-x-0 pb-32': showPreview, 'translate-x-full md:translate-x-0': !showPreview}"
                     class="fixed md:relative w-full md:w-2/5 h-full overflow-x-hidden overflow-y-auto flex flex-col border-l border-gray-200 bg-stone-500 transition-transform ease-in-out duration-200">
                    <Teleport v-if="isMounted && form.accounts.length" to="#navRightButton">
                        <SecondaryButton @click="showPreview = !showPreview" size="xs">
                            <span class="mr-xs">
                                <EyeOffIcon v-if="showPreview"/>
                                <EyeIcon v-else/>
                            </span>
                            <span>Preview</span>
                        </SecondaryButton>
                    </Teleport>

                    <div class="py-2xl">
                        <PageHeader title="Preview"/>

                        <div class="row-px">
                            <PostPreviewProviders :accounts="$page.props.accounts"
                                                  :selected-accounts="form.accounts"
                                                  :versions="form.versions"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <PostActions :form="form"/>
        </div>
    </PostContext>
</template>
