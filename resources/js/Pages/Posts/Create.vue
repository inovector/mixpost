<script setup>
import {Head, useForm} from '@inertiajs/inertia-vue3';
import EmojiPicker from '@/Components/EmojiPicker.vue'
import PageHeader from "@/Components/PageHeader.vue";
import MixpostPanel from "@/Components/Panel.vue";
import MixpostContentEditable from "@/Components/ContentEditable.vue";
import MixpostSocialAccount from "@/Components/SocialAccount.vue"
import MixpostTwitterPreview from "@/Components/TwitterPreview.vue"
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import TwitterIcon from "@/Icons/Twitter.vue"
import FacebookIcon from "@/Icons/Facebook.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import ChatIcon from "@/Icons/Chat.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"

const form = useForm({
    name: '',
});

function onSelectEmoji(emoji) {
    console.log(emoji);
}
</script>
<template>
    <Head title="Create new post"/>

    <div class="flex flex-row h-full">
        <div class="w-3/5 h-full flex flex-col overflow-y-auto">
            <div class="default-y-padding">
                <PageHeader title="Create new post">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 rounded-full bg-lime-500"></div>
                        <div>Saved</div>
                    </div>
                </PageHeader>

                <div class="max-w-7xl mx-auto default-x-padding">
                    <MixpostPanel>
                        <form class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <MixpostSocialAccount
                                    imgUrl="https://pbs.twimg.com/profile_images/1539256262860460034/s69PIJB8_normal.jpg"
                                    active-border-class="border-blue-400" active-bg-class="bg-blue-400" :active="true"
                                    v-tooltip="'Twitter'">
                                    <TwitterIcon class="text-blue-400 !w-4 !h-4"/>
                                </MixpostSocialAccount>

                                <MixpostSocialAccount
                                    imgUrl="https://pbs.twimg.com/profile_images/1539256262860460034/s69PIJB8_normal.jpg"
                                    active-border-class="border-blue-700" active-bg-class="bg-blue-700" :active="false"
                                    v-tooltip="'Facebook'">
                                    <FacebookIcon class="text-blue-700 !w-4 !h-4"/>
                                </MixpostSocialAccount>
                            </div>
                            <div>
                                <MixpostContentEditable min-height="200px" placeholder="Type here something interesting for your audience...">
                                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                       <div class="flex items-center space-x-2">
                                           <EmojiPicker @selected="onSelectEmoji"/>
                                           <div>
                                               <button type="button" v-tooltip="'Media'"
                                                       class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                                   <PhotoIcon/>
                                               </button>
                                           </div>
                                       </div>
                                        <button  v-tooltip="'Add Comment'" type="button" class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200"><ChatIcon/></button>
                                    </div>
                                </MixpostContentEditable>
                                <div class="mt-6 space-x-2">
                                    <MixpostSecondaryButton><CalendarIcon class="mr-2"/>Pick time</MixpostSecondaryButton>
                                    <MixpostPrimaryButton><PaperAirplaneIcon class="mr-2"/>Post now</MixpostPrimaryButton>
                                </div>
                            </div>
                        </form>
                    </MixpostPanel>
                </div>
            </div>
        </div>
        <div class="w-2/5 h-full border-l border-gray-200">
            <div class="py-10">
                <PageHeader title="Preview"/>
                <div class="px-5">
                    <MixpostTwitterPreview/>
                </div>
            </div>
        </div>
    </div>
</template>
