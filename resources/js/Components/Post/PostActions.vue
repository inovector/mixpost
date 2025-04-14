<script setup>
import {computed, ref} from "vue";
import {format, parseISO} from "date-fns";
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import usePostValidator from "../../Composables/usePostValidator.js";
import usePost from "@/Composables/usePost";
import useNotifications from "@/Composables/useNotifications";
import useSettings from "@/Composables/useSettings";
import ConfirmationModal from "@/Components/Modal/ConfirmationModal.vue";
import PrimaryButton from "@/Components/Button/PrimaryButton.vue"
import SecondaryButton from "@/Components/Button/SecondaryButton.vue"
import PickTime from "@/Components/Package/PickTime.vue"
import PostTags from "@/Components/Post/PostTags.vue"
import Badge from "@/Components/DataDisplay/Badge.vue";
import ProviderIcon from "@/Components/Account/ProviderIcon.vue";
import CalendarIcon from "@/Icons/Calendar.vue"
import PaperAirplaneIcon from "@/Icons/PaperAirplane.vue"
import XIcon from "@/Icons/X.vue"
import WarningButton from "../Button/WarningButton.vue";
import Forward from "../../Icons/Forward.vue";
import UpgradePro from "../Pro/UpgradePro.vue";
import ProLabel from "../Pro/ProLabel.vue";

const props = defineProps({
    form: {
        required: true,
        type: Object
    }
});

const {postId, editAllowed} = usePost();
const {validationPassed} = usePostValidator();

const emit = defineEmits(['submit'])

const timePicker = ref(false);

const {timeFormat, weekStartsOn} = useSettings();

const scheduleTime = computed(() => {
    if (props.form.date && props.form.time) {
        return format(new Date(parseISO(props.form.date + ' ' + props.form.time)), `MMM do, ${timeFormat === 24 ? 'kk:mm' : 'h:mmaaa'}`, {
            weekStartsOn: weekStartsOn
        });
    }

    return null;
})

const clearScheduleTime = () => {
    props.form.date = '';
    props.form.time = '';
}

const {notify} = useNotifications();
const isLoading = ref(false);

const canSchedule = computed(() => {
    return (postId.value && props.form.accounts.length) &&
        editAllowed.value &&
        validationPassed.value;
});

const schedule = (postNow = false) => {
    isLoading.value = true;

    axios.post(route('mixpost.posts.schedule', {post: postId.value}), {
        postNow
    }).then((response) => {
        notify('success', response.data, {
            name: 'View in calendar',
            href: route('mixpost.calendar', {date: props.form.date})
        });

        router.visit(route('mixpost.posts.index'));
    }).catch((error) => {
        handleValidationError(error);
    }).finally(() => {
        isLoading.value = false;
    })
}

const handleValidationError = (error) => {
    if (error.response.status !== 422) {
        notify('error', error.response.data.message);
        return;
    }

    const validationErrors = error.response.data.errors;

    const mustRefreshPage = validationErrors.hasOwnProperty('in_history') || validationErrors.hasOwnProperty('publishing');

    if (!mustRefreshPage) {
        notify('error', validationErrors);
    }

    if (mustRefreshPage) {
        router.visit(route('mixpost.posts.edit', {post: postId.value}));
    }
}

const confirmationPostNow = ref(false);

const accounts = computed(() => {
    return usePage().props.accounts.filter(account => props.form.accounts.includes(account.id));
})
</script>
<template>
    <div class="w-full flex items-center justify-end bg-stone-500 border-t border-gray-200 z-10">
        <div class="py-4 flex items-center space-x-xs row-px">
            <PostTags :items="form.tags" @update="form.tags = $event"/>

            <div class="flex items-center" role="group">
                <SecondaryButton size="md"
                                 :class="{'normal-case! border-r-indigo-800 rounded-r-none': scheduleTime, 'rounded-r-lg!': !canSchedule}"
                                 @click="timePicker = true">
                    <CalendarIcon class="sm:mr-xs"/>
                    <span class="hidden sm:block">{{ scheduleTime ? scheduleTime : 'Pick time' }}</span>
                </SecondaryButton>

                <template v-if="scheduleTime && canSchedule">
                    <SecondaryButton size="md"
                                     @click="clearScheduleTime"
                                     v-tooltip="'Clear time'"
                                     class="rounded-l-none border-l-0 hover:text-red-500 px-2!">
                        <XIcon/>
                    </SecondaryButton>
                </template>

                <PickTime :show="timePicker"
                          :date="form.date"
                          :time="form.time"
                          :isSubmitActive="editAllowed"
                          @close="timePicker = false"
                          @update="form.date = $event.date; form.time = $event.time;"/>
            </div>

            <template v-if="editAllowed">
                <PrimaryButton @click="scheduleTime ? schedule() : confirmationPostNow = true"
                               :disabled="!canSchedule || isLoading"
                               :isLoading="isLoading"
                               size="md">
                    <PaperAirplaneIcon class="mr-xs"/>
                    {{ scheduleTime ? 'Schedule' : 'Post now' }}
                </PrimaryButton>

                <UpgradePro>
                    <template #trigger>
                        <WarningButton
                            :hiddenTextOnSmallScreen="true"
                            :disabled="!canSchedule || isLoading"
                            size="md">
                            <template #icon>
                                <Forward/>
                            </template>

                            Add to queue

                            <ProLabel/>
                        </WarningButton>
                    </template>
                </UpgradePro>
            </template>
        </div>

        <ConfirmationModal :show="confirmationPostNow" @close="confirmationPostNow = false">
            <template #header>
                Confirm publication
            </template>
            <template #body>
                This post will be immediately published to the following social accounts. Are you sure?

                <div class="mt-sm flex flex-wrap items-center gap-xs">
                    <Badge v-for="account in accounts" :key="account.id">
                        <ProviderIcon :provider="account.provider" class="w-4! h-4! mr-xs"/>
                        {{ account.name }}
                    </Badge>
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="confirmationPostNow = false" class="mr-xs">Cancel</SecondaryButton>
                <PrimaryButton :disabled="isLoading" :isLoading="isLoading" @click="schedule(true)">Post now
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
