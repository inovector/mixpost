<script setup>
import {ref, watch} from "vue";
import {Head, useForm} from '@inertiajs/inertia-vue3';
import {Inertia} from "@inertiajs/inertia";
import {cloneDeep, debounce, differenceBy} from "lodash";
import useMounted from "@/Composables/useMounted";
import usePostVersions from "@/Composables/usePostVersions";
import PageHeader from "@/Components/DataDisplay/PageHeader.vue";
import PostContext from "@/Context/PostContext.vue";
import PostForm from "@/Components/Post/PostForm.vue";
import PostActions from "@/Components/Post/PostActions.vue";
import PostTags from "@/Components/Post/PostTags.vue"
import PostPreviewProviders from "@/Components/Post/PostPreviewProviders.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import EyeIcon from "@/Icons/Eye.vue"
import EyeOffIcon from "@/Icons/EyeOff.vue"

const props = defineProps(['post']);

const post = props.post ? cloneDeep(props.post) : null;

const {isMounted} = useMounted();
const showPreview = ref(false);
const isLoading = ref(false);
const hasError = ref(false);

const {versionObject} = usePostVersions();

const form = useForm({
    accounts: post ? post.accounts.map(account => account.id) : [],
    versions: post ? post.versions : [versionObject(0, true)],
    tags: post ? post.tags : [],
    date: post ? post.scheduled_at.date : '',
    time: post ? post.scheduled_at.time : '',
});

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

const store = (data) => {
    Inertia.post(route('mixpost.posts.store'), data);
}

const update = (data) => {
    isLoading.value = true;

    axios.put(route('mixpost.posts.update', {post: props.post.id}), data)
        .then(() => {
            hasError.value = false;
        }).catch(() => {
        hasError.value = true;
    }).finally(() => {
        isLoading.value = false;
    });
}

// Since we are cloning the post tag props, we need to track the post tags and update them
if (props.post) {
    watch(() => props.post.tags, (val) => {
        form.tags = val;
    })
}

watch(form, debounce(() => {
    save();
}, 300))
</script>
<template>
    <Head title="Your post"/>

    <PostContext>
        <div class="flex flex-col grow h-full">
            <div class="flex flex-row h-full overflow-y-auto">
                <div class="w-full md:w-3/5 h-full flex flex-col overflow-x-hidden overflow-y-auto">
                    <div class="default-y-padding">
                        <PageHeader title="Your post">
                            <div v-if="$page.props.post" class="flex items-center">
                                <div class="flex items-center mr-6">
                                    <div class="w-4 h-4 mr-2 rounded-full bg-gray-500"></div>
                                    <div>Draft</div>
                                </div>
                                <div class="flex items-center">
                                    <div
                                        :class="{'animate-ping': isLoading, 'bg-lime-500': !hasError, 'bg-red-500': hasError}"
                                        class="w-4 h-4 mr-2 rounded-full"></div>
                                    <div v-if="!hasError">Saved</div>
                                    <div v-if="hasError">Error on saving</div>
                                </div>
                            </div>
                        </PageHeader>

                        <div class="w-full max-w-7xl mx-auto default-x-padding">
                            <PostForm :form="form" :accounts="$page.props.accounts"/>
                        </div>
                    </div>
                </div>
                <div :class="{'translate-x-0 pb-32': showPreview, 'translate-x-full md:translate-x-0': !showPreview}"
                     class="fixed md:relative w-full md:w-2/5 h-full overflow-x-hidden overflow-y-auto flex flex-col border-l border-gray-200 bg-stone-500 transition-transform ease-in-out duration-200">
                    <Teleport v-if="isMounted && form.accounts.length" to="#navRightButton">
                        <SecondaryButton @click="showPreview = !showPreview" size="xs">
                            <span class="mr-2">
                                <EyeOffIcon v-if="showPreview"/>
                                <EyeIcon v-else/>
                            </span>
                            <span>Preview</span>
                        </SecondaryButton>
                    </Teleport>

                    <div class="py-10">
                        <PageHeader title="Preview"/>

                        <div class="default-x-padding">
                            <PostPreviewProviders :accounts="$page.props.accounts"
                                                  :selected-accounts="form.accounts"
                                                  :versions="form.versions"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <PostActions :form="form" @submit="save">
                <PostTags :items="form.tags" @update="form.tags = $event"/>
            </PostActions>
        </div>
    </PostContext>
</template>
