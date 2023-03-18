<script setup>
import {computed} from "vue";
import usePostVersions from "@/Composables/usePostVersions";
import PostPreviewTwitter from "@/Components/Post/PostPreviewTwitter.vue"
import PostPreviewFacebook from "@/Components/Post/PostPreviewFacebook.vue"
import PostPreviewMastodon from "@/Components/Post/PostPreviewMastodon.vue"
import Panel from "@/Components/Surface/Panel.vue";
import Alert from "@/Components/Util/Alert.vue";

const props = defineProps({
    accounts: {
        required: true,
        type: Array,
    },
    selectedAccounts: {
        required: true,
        type: Array,
    },
    versions: {
        required: true,
        type: Array,
    }
});

const {getOriginalVersion, getAccountVersion} = usePostVersions();

const defaultVersion = computed(() => {
    return getOriginalVersion(props.versions);
});

const previews = computed(() => {
    return props.accounts.filter((account) => {
        return props.selectedAccounts.includes(account.id);
    }).map((account) => {
        const accountVersion = getAccountVersion(props.versions, account.id);

        return {
            account,
            content: accountVersion ? accountVersion.content : defaultVersion.value.content,
            providerComponent: {
                'twitter': PostPreviewTwitter,
                'facebook_page': PostPreviewFacebook,
                'facebook_group': PostPreviewFacebook,
                'mastodon': PostPreviewMastodon
            }[account.provider]
        }
    });
})
</script>
<template>
    <template v-if="selectedAccounts.length">
        <template v-for="preview in previews" :key="preview.id">
            <Alert v-if="preview.account.errors && preview.account.errors.length"
                   variant="error"
                   :closeable="false"
                   class="mb-1">
                <span v-html="preview.account.errors.join('</br>')"></span>
            </Alert>

            <div class="mb-lg last:mb-0">
                <component :is="preview.providerComponent"
                           :name="preview.account.name"
                           :username="preview.account.username"
                           :image="preview.account.image"
                           :content="preview.content"
                />
            </div>
        </template>
    </template>
    <template v-else>
        <div>
            <div>Hi 👋</div>
            <div>Select an account and start writing your post in the left panel to start.</div>
        </div>
        <Panel class="mt-lg">
            <div class="flex items-start">
                <div class="mr-sm">
                    <span
                        class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full bg-gray-100"></span>
                </div>
                <div class="w-full">
                    <div class="flex items-center">
                        <div class="mr-xs bg-gray-100 w-2/12 h-4"></div>
                        <div class="bg-gray-100 w-3/12 h-4"></div>
                    </div>
                    <div class="mt-5">
                        <div class="bg-gray-100 w-3/4 h-4"></div>
                        <div class="bg-gray-100 w-full h-4 mt-xs"></div>
                        <div class="bg-gray-100 w-2/5 h-4 mt-xs"></div>
                    </div>
                </div>
            </div>
        </Panel>
    </template>
</template>
