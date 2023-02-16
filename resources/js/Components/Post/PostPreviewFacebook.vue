<script setup>
import {computed} from "vue";
import useEditor from "@/Composables/useEditor";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import Alert from "@/Components/Util/Alert.vue";
import Panel from "@/Components/Surface/Panel.vue";
import Gallery from "@/Components/ProviderGallery/Facebook/FacebookGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";

import fbIconsImgUrl from "@img/fb-icons.png"

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
    textLimitReached: {
        type: Boolean,
        default: false,
    }
})

const {isDocEmpty} = useEditor();

const mainContent = computed(() => {
    return props.content[0];
});
</script>
<template>
    <Alert v-if="textLimitReached" variant="error" :closeable="false" class="mb-xs">[{{ name }}] : You have
        reached the maximum character limit
    </Alert>
    <Panel :class="{'border-red-500': textLimitReached}" class="relative">
        <div class="absolute right-0 top-0 -mt-sm -mr-xs">
            <div class="flex items-center justify-center p-2 w-7 h-7 rounded-full bg-white border border-gray-200">
                <div>
                    <ProviderIcon provider="facebook" class="!w-5 !h-5"/>
                </div>
            </div>
        </div>

        <div class="flex items-center">
            <span class="inline-flex justify-center items-center flex-shrink-0 w-10 h-10 rounded-full mr-sm">
                <img :src="image"
                     class="object-cover w-full h-full rounded-full" alt=""/>
            </span>
            <div class="flex flex-col">
                <div class="font-medium mr-xs">{{ name }}</div>
                <div class="text-gray-400 text-sm">19h</div>
            </div>
        </div>

        <div class="w-full">
            <EditorReadOnly :value="mainContent.body"
                            :class="{'mt-xs': !isDocEmpty(mainContent.body), 'mb-xs': mainContent.media.length}"/>

            <Gallery :media="mainContent.media"/>
        </div>

        <div class="mt-5 flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex mr-xs">
                    <img src="@img/fb-like.svg" class="w-5 h-5 z-10" alt=""/>
                    <img src="@img/fb-wow.svg" class="w-5 h-5 -ml-1" alt=""/>
                </div>
                <div class="text-gray-500">116</div>
            </div>
            <div class="text-gray-500">0 comments</div>
        </div>

        <div class="mt-5 flex items-center justify-around border-t border-b border-gray-200 text-gray-500 py-2">
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -342px', backgroundSize: '25px 867px'}"
                   class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Like</span>
            </div>
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -304px', backgroundSize: '25px 867px'}"
                   class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Comment</span>
            </div>
            <div class="flex items-center">
                <i :style="{backgroundImage: `url(${fbIconsImgUrl})`, backgroundPosition: '0px -361px', backgroundSize: '25px 867px'}"
                   class="facebook-toolbar-icon"></i>
                <span class="ml-1 font-semibold">Share</span>
            </div>
        </div>
    </Panel>
</template>
<style>
.facebook-toolbar-icon {
    @apply inline-block w-5 h-5 bg-no-repeat;
}
</style>
