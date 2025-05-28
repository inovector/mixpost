<script setup>
import {computed} from "vue";
import useEditor from "@/Composables/useEditor";
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
    }
})

const {isDocEmpty} = useEditor();

const mainContent = computed(()=> {
    return props.content[0];
});
</script>
<template>
    <Panel class="relative">
        <div class="flex items-start justify-between">
           <div class="flex items-center">
               <div class="mr-sm">
                <span class="inline-flex justify-center items-center shrink-0 w-10 h-10 rounded-full">
                    <img :src="image"
                         class="object-cover w-full h-full rounded-sm" alt=""/>
                </span>
               </div>
               <div>
                   <div class="font-medium mr-xs">{{ name }}</div>
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
