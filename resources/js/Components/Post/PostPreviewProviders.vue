<script setup>
import {computed} from "vue";
import usePostVersions from "@/Composables/usePostVersions";
import PostPreviewTwitter from "@/Components/PostPreview/PostPreviewTwitter.vue"
import PostPreviewFacebook from "@/Components/PostPreview/PostPreviewFacebook.vue"
import PostPreviewMastodon from "@/Components/PostPreview/PostPreviewMastodon.vue"
import Panel from "@/Components/Surface/Panel.vue";
import Alert from "@/Components/Util/Alert.vue";
import ProviderIcon from "../Account/ProviderIcon.vue";
import ArrowTopRightOnSquare from "../../Icons/ArrowTopRightOnSquare.vue";

const props = defineProps({
    accounts: {
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
    return props.accounts.map((account) => {
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

const hasErrors = (errors) => {
    if (errors && Array.isArray(errors)) {
        return errors.length > 0;
    }

    return errors && typeof errors === 'object';
}
</script>
<template>
   <div>
       <template v-if="accounts.length">
           <template v-for="preview in previews" :key="preview.account.id">
               <Alert v-if="hasErrors(preview.account.errors)"
                      variant="error"
                      :closeable="false"
                      class="mb-1">
                   <!--TODO: print human error-->
                   <div class="overflow-x-auto">
                       <div class="hyphens-none">{{ preview.account.errors }}</div>
                   </div>
               </Alert>

               <div class="mb-lg last:mb-0 relative">
                   <component :is="preview.providerComponent"
                              :name="preview.account.name"
                              :username="preview.account.username"
                              :image="preview.account.image"
                              :content="preview.content"
                              :options="preview.options"
                   />

                   <div class="absolute right-0 top-0 -mt-sm -mr-xs">
                       <div class="flex items-center">
                           <div v-if="preview.account.external_url"
                                class="mr-xs flex items-center justify-center p-2 w-7 h-7 rounded-full bg-white border border-gray-200">
                               <a :href="preview.account.external_url" target="_blank" class="link">
                                   <ArrowTopRightOnSquare class="w-5! h-5!"/>
                               </a>
                           </div>

                           <div
                               class="flex items-center justify-center p-2 w-7 h-7 rounded-full bg-white border border-gray-200">
                               <div>
                                   <ProviderIcon :provider="preview.account.provider" class="w-5! h-5!"/>
                               </div>
                           </div>
                       </div>
                   </div>
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
                        class="inline-flex justify-center items-center shrink-0 w-10 h-10 rounded-full bg-gray-100"></span>
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
   </div>
</template>
