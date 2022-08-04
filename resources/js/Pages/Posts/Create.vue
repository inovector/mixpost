<script setup>
import {computed, ref} from "vue";
import {format, parseISO} from "date-fns"
import {Head, useForm} from '@inertiajs/inertia-vue3';
import EmojiPicker from '@/Components/EmojiPicker.vue'
import MixpostPageHeader from "@/Components/PageHeader.vue";
import MixpostPanel from "@/Components/Panel.vue";
import MixpostContentEditable from "@/Components/ContentEditable.vue";
import MixpostAccount from "@/Components/Account.vue"
import MixpostTwitterPreview from "@/Components/TwitterPreview.vue"
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import MixpostPickTime from "@/Components/PickTime.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import ChatIcon from "@/Icons/Chat.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"
import ChevronDownIcon from "@/Icons/ChevronDown.vue"
import XIcon from "@/Icons/X.vue"

const timePicker = ref(false);

const form = useForm({
    accounts: [],
    body: '',
    date: '',
    time: ''
});

const scheduleTime = computed(() => {
    if (form.date && form.time) {
        return format(new Date(parseISO(form.date + ' ' + form.time)), "E, MMM do, y 'at' kk:mm");
    }

    return null;
})

const selectAccount = (account) => {
    if (form.accounts.includes(account)) {
        form.accounts = form.accounts.filter(item => item !== account);
        return;
    }

    form.accounts.push(account);
}

const clearScheduleTime = () => {
    form.date = '';
    form.time = '';
}

const onSelectEmoji = (emoji) => {
    console.log(emoji);
}
</script>
<template>
    <Head title="Create new post"/>

    <div class="flex flex-row h-full">
        <div class="w-3/5 h-full flex flex-col overflow-y-auto">
            <div class="default-y-padding">
                <MixpostPageHeader title="Create new post">
                    <div class="flex items-center">
                        <div class="w-4 h-4 mr-2 rounded-full bg-lime-500"></div>
                        <div>Saved</div>
                    </div>
                </MixpostPageHeader>

                <div class="max-w-7xl mx-auto default-x-padding">
                    <MixpostPanel>
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3">
                                <template v-for="account in $page.props.accounts" :key="account.id">
                                    <button @click="selectAccount(account.id)">
                                        <MixpostAccount
                                            :provider="account.provider"
                                            :img-url="account.image"
                                            :active="form.accounts.includes(account.id)"
                                            v-tooltip="account.name"
                                        />
                                    </button>
                                </template>
                            </div>

                            <div>
                                <MixpostContentEditable v-model="form.body" min-height="200px"
                                                        placeholder="Type here something interesting for your audience...">
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
                                        <button v-tooltip="'Add Comment'" type="button"
                                                class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                            <ChatIcon/>
                                        </button>
                                    </div>
                                </MixpostContentEditable>

                                <div class="mt-6 flex items-center space-x-2">
                                    <div class="flex items-center" role="group">
                                        <MixpostSecondaryButton size="md"
                                                                :class="{'!normal-case rounded-r-none border-r-indigo-800': scheduleTime}"
                                                                @click="timePicker = true">
                                            <CalendarIcon class="mr-2"/>
                                            <span>{{ scheduleTime ? scheduleTime : 'Pick time' }}</span>
                                        </MixpostSecondaryButton>

                                        <template v-if="scheduleTime">
                                            <MixpostSecondaryButton size="md"
                                                                    @click="clearScheduleTime"
                                                                    v-tooltip="'Clear time'"
                                                                    class="rounded-l-none border-l-0 hover:text-red-500 !px-2">
                                                <XIcon/>
                                            </MixpostSecondaryButton>
                                        </template>

                                        <MixpostPickTime :show="timePicker"
                                                         :date="form.date"
                                                         :time="form.time"
                                                         @close="timePicker = false"
                                                         @update="form.date = $event.date; form.time = $event.time;"/>
                                    </div>

                                    <div class="flex items-center" role="group">
                                        <MixpostPrimaryButton size="md"
                                                              :class="{'rounded-r-none border-r-indigo-400': scheduleTime}">
                                            <PaperAirplaneIcon class="mr-2"/>
                                            {{ scheduleTime ? 'Schedule' : 'Post now' }}
                                        </MixpostPrimaryButton>

                                        <MixpostPrimaryButton v-if="scheduleTime" size="md"
                                                              class="rounded-l-none border-l-0 !px-2">
                                            <ChevronDownIcon/>
                                        </MixpostPrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </MixpostPanel>
                </div>
            </div>
        </div>
        <div class="w-2/5 h-full border-l border-gray-200">
            <div class="py-10">
                <MixpostPageHeader title="Preview"/>

                <div class="px-5">
                    <MixpostTwitterPreview :body="form.body"/>
                </div>
            </div>
        </div>
    </div>
</template>
