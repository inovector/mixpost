<script setup>
import {computed} from "vue";
import useEditor from "@/Composables/useEditor";
import Panel from "@/Components/Surface/Panel.vue";
import Gallery from "@/Components/ProviderGallery/Facebook/FacebookGallery.vue"
import EditorReadOnly from "@/Components/Package/EditorReadOnly.vue";

import fbIconsImgUrl from "@img/fb-icons.png"
import {REPRESENTATIVE_DATA_TEXT} from "../../Constants/Text";

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

const mainContent = computed(() => {
    return props.content[0];
});
</script>
<template>
    <Panel class="relative">
        <div class="flex items-center">
            <span class="inline-flex justify-center items-center shrink-0 w-10 h-10 rounded-full mr-sm">
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
            <div v-tooltip="REPRESENTATIVE_DATA_TEXT" class="flex items-center">
                <div class="flex mr-xs">
                    <img src="@img/fb-like.svg" class="w-5 h-5 z-10" alt=""/>
                    <img src="@img/fb-wow.svg" class="w-5 h-5 -ml-1" alt=""/>
                </div>
                <div class="text-gray-500">116</div>
            </div>
            <div v-tooltip="REPRESENTATIVE_DATA_TEXT" class="text-gray-500">0 comments</div>
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
<style lang="css">
@reference "@css/app.css";
.facebook-toolbar-icon {
    @apply inline-block w-5 h-5 bg-no-repeat;
}
</style>
