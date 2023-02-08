<script setup>
import {computed} from "vue";
import useEditor from "@/Composables/useEditor";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import Alert from "@/Components/Util/Alert.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Gallery from "@/Components/ProviderGallery/Mastodon/MastodonGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";
import GlobeImg from "@img/social-icons/mastodon/globe.svg"
import ReplyImg from "@img/social-icons/mastodon/reply.svg"
import RetweetImg from "@img/social-icons/mastodon/retweet.svg"
import StarImg from "@img/social-icons/mastodon/star.svg"
import BookmarkImg from "@img/social-icons/mastodon/bookmark.svg"
import EllipsisImg from "@img/social-icons/mastodon/ellipsis.svg"

const props = defineProps({
    name: {
        required: true,
        type: String
    },
    username: {
        required: true,
        type: String
    },
    image: {
        required: true,
        type: String
    },
    content: {
        required: true,
        type: Array,
    },
    reachedMaxCharacterLimit: {
        type: Boolean,
        default: false,
    }
})

const {isDocEmpty} = useEditor();

const mainContent = computed(()=> {
    return props.content[0];
});
</script>
<template>
    <Alert v-if="reachedMaxCharacterLimit" variant="error" :closeable="false" class="mb-xs">
        [{{ name }}] : You have reached the maximum character limit
    </Alert>

    <Alert v-if="mainContent.media.length > 4" variant="warning" class="mb-xs">[{{ name }}]: Twitter accepts a maximum of 4 pictures. Don't worry, if you have selected several pictures, Mixpost will only post the first 4.</Alert>

    <Panel :class="{'border-red-500': reachedMaxCharacterLimit}" class="relative">
        <div class="absolute right-0 top-0 -mt-sm -mr-xs">
            <div class="flex items-center justify-center p-2 w-7 h-7 rounded-full bg-white border border-gray-200">
                <div>
                    <ProviderIcon provider="mastodon" class="!w-5 !h-5"/>
                </div>
            </div>
        </div>

        <div class="flex items-start justify-between">
           <div class="flex items-center">
               <div class="mr-sm">
                <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full">
                    <img :src="image"
                         class="object-cover w-full h-full rounded" alt=""/>
                </span>
               </div>
               <div>
                   <div class="font-medium mr-xs font-semibold">{{ name }}</div>
                   <div class="text-gray-400">@{{ username }}</div>
               </div>
           </div>
            <div class="flex items-center">
                <span class="mr-xs">
                    <img :src="GlobeImg" alt="Globe" class="w-4 h-4"/>
                </span>
                <span class="text-gray-400 text-sm">19h</span>
            </div>
        </div>

        <div class="w-full">
            <EditorReadOnly :value="mainContent.body"
                            :class="{'mt-xs': !isDocEmpty(mainContent.body), 'mb-xs': mainContent.media.length}"/>

            <Gallery :media="mainContent.media"/>

            <div class="mt-5 flex items-center justify-between">
                <div class="flex items-center">
                    <img :src="ReplyImg" alt="Reply" class="w-5 h-5"/>
                    <div class="ml-xs">0</div>
                </div>
                <div class="flex items-center">
                    <img :src="RetweetImg" alt="Retweet" class="w-5 h-5"/>
                </div>
                <div class="flex items-center">
                    <img :src="StarImg" alt="Star" class="w-5 h-5"/>
                </div>
                <div class="flex items-center">
                    <img :src="BookmarkImg" alt="Bookmark" class="w-5 h-5"/>
                </div>
                <div class="flex items-center">
                    <img :src="EllipsisImg" alt="Ellipsis" class="w-5 h-5"/>
                </div>
            </div>
        </div>
    </Panel>
</template>
