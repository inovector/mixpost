<script setup>
import {computed, ref, inject} from "vue";
import {format, parseISO} from "date-fns";
import {capitalize, uniqBy} from "lodash";
import useEditor from "@/Composables/useEditor";
import Editor from "@/Components/Editor.vue";
import EmojiPicker from '@/Components/EmojiPicker.vue'
import MixpostPanel from "@/Components/Panel.vue";
import MixpostAccount from "@/Components/Account.vue"
import MixpostProviderPostCharacterCount from "@/Components/ProviderPostCharacterCount.vue"
import MixpostPrimaryButton from "@/Components/PrimaryButton.vue"
import MixpostSecondaryButton from "@/Components/SecondaryButton.vue"
import MixpostPickTime from "@/Components/PickTime.vue"
import PhotoIcon from "@/Icons/Photo.vue"
import ChatIcon from "@/Icons/Chat.vue"
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"
import ChevronDownIcon from "@/Icons/ChevronDown.vue"
import XIcon from "@/Icons/X.vue"

const postContext = inject('postContext')

const props = defineProps({
    form: {
        required: true,
        type: Object
    },
    accounts: {
        required: true,
        type: Array
    },
});

const timePicker = ref(false);

const scheduleTime = computed(() => {
    if (props.form.date && props.form.time) {
        return format(new Date(parseISO(props.form.date + ' ' + props.form.time)), "E, MMM do, y 'at' kk:mm");
    }

    return null;
})

const selectAccount = (account) => {
    if (props.form.accounts.includes(account)) {
        props.form.accounts = props.form.accounts.filter(item => item !== account);
        return;
    }

    props.form.accounts.push(account);
}

const providersWithDisabledSimultaneousPosting = computed(() => {
    return props.accounts.filter(function (account) {
        return props.form.accounts.includes(account.id) && !account.provider_options.simultaneous_posting_on_multiple_accounts;
    }).map(function (account) {
        return account.provider;
    });
});

const providersWithPostCharactersLimit = computed(() => {
    const items = props.accounts.filter(function (account) {
        return props.form.accounts.includes(account.id) && account.provider_options.post_characters_limit !== null;
    }).map(function (account) {
        return {
            provider: account.provider,
            limit: account.provider_options.post_characters_limit
        };
    });

    return uniqBy(items, 'provider');
});

const isAccountSelected = (account) => {
    return props.form.accounts.includes(account.id);
}

const isAccountUnselectable = (account) => {
    return !isAccountSelected(account) && providersWithDisabledSimultaneousPosting.value.includes(account.provider);
}

const clearScheduleTime = () => {
    props.form.date = '';
    props.form.time = '';
}

const {insertEmoji, focusEditor} = useEditor();
</script>
<template>
    <MixpostPanel>
        <div class="space-y-6">
            <div class="flex items-center space-x-3">
                <template v-for="account in $page.props.accounts" :key="account.id">
                    <button @click="selectAccount(account.id)"
                            :disabled="isAccountUnselectable(account)">
                        <MixpostAccount
                            :provider="account.provider"
                            :img-url="account.image"
                            :warning-message="isAccountUnselectable(account) ? capitalize(account.provider) + ' does not allow simultaneous posting of identical content to multiple accounts.' : ''"
                            :active="isAccountSelected(account)"
                            v-tooltip="account.name"
                        />
                    </button>
                </template>
            </div>

            <div>
                <Editor id="postEditor"
                        v-model="form.body"
                        placeholder="Type here something interesting for your audience...">
                    <template #default="props">
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <div class="flex items-center space-x-2">
                                <EmojiPicker
                                    @selected="insertEmoji({editorId: 'postEditor', emoji: $event})"
                                    @close="focusEditor({editorId: 'postEditor'})"
                                />

                                <div>
                                    <button type="button" v-tooltip="'Media'"
                                            class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                        <PhotoIcon/>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-center">
                                <div class="flex items-center justify-center mr-5">
                                    <template v-for="item in providersWithPostCharactersLimit" :key="item.provider">
                                        <MixpostProviderPostCharacterCount :provider="item.provider"
                                                                           :character-limit="item.limit"
                                                                           :text="props.bodyText"
                                                                           @reached="postContext.reachedMaxCharacterLimit[item.provider] = $event"/>
                                    </template>
                                </div>

                                <button v-tooltip="'Add Comment'" type="button"
                                        class="text-stone-800 hover:text-indigo-500 transition-colors ease-in-out duration-200">
                                    <ChatIcon/>
                                </button>
                            </div>
                        </div>
                    </template>
                </Editor>

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
                        <MixpostPrimaryButton @click="$emit('submit', scheduleTime ? 'schedule' : 'now')" size="md"
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
</template>
